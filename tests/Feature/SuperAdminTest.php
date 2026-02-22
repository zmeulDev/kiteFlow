<?php

namespace Tests\Feature;

use App\Models\AccessPoint;
use App\Models\ActivityLog;
use App\Models\Building;
use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Create super-admin user
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('super-admin');
    }

    /** @test */
    public function super_admin_can_view_all_tenants()
    {
        Tenant::factory()->count(5)->create();

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/api/admin/tenants');

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function super_admin_can_create_tenant()
    {
        $tenantData = [
            'name' => 'Acme Corporation',
            'email' => 'contact@acme.com',
            'phone' => '+1234567890',
            'status' => 'active',
            'subscription_plan' => 'enterprise',
            'billing_cycle' => 'yearly',
            'monthly_price' => 299.00,
            'address' => [
                'street' => '123 Business Ave',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'US',
            ],
        ];

        $response = $this->actingAs($this->superAdmin)
            ->postJson('/api/admin/tenants', $tenantData);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Acme Corporation']);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Acme Corporation',
            'email' => 'contact@acme.com',
        ]);
    }

    /** @test */
    public function super_admin_can_update_tenant()
    {
        $tenant = Tenant::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->superAdmin)
            ->putJson("/api/admin/tenants/{$tenant->slug}", [
                'name' => 'New Name',
                'email' => $tenant->email,
                'status' => 'suspended',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'New Name',
            'status' => 'suspended',
        ]);
    }

    /** @test */
    public function super_admin_can_delete_tenant()
    {
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->deleteJson("/api/admin/tenants/{$tenant->slug}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('tenants', ['id' => $tenant->id]);
    }

    /** @test */
    public function super_admin_can_view_tenant_contract_details()
    {
        $tenant = Tenant::factory()->create([
            'subscription_plan' => 'enterprise',
            'billing_cycle' => 'yearly',
            'contract_start_date' => now()->startOfYear(),
            'contract_end_date' => now()->addYear(),
            'payment_status' => 'current',
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->getJson("/api/admin/tenants/{$tenant->slug}");

        $response->assertStatus(200)
            ->assertJsonFragment([
                'subscription_plan' => 'enterprise',
                'billing_cycle' => 'yearly',
                'payment_status' => 'current',
            ]);
    }

    /** @test */
    public function super_admin_can_manage_tenant_status()
    {
        $tenant = Tenant::factory()->create(['status' => 'active']);

        // Suspend tenant
        $response = $this->actingAs($this->superAdmin)
            ->putJson("/api/admin/tenants/{$tenant->slug}/status", [
                'status' => 'suspended',
                'reason' => 'Payment overdue',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('suspended', $tenant->fresh()->status);

        // Reactivate tenant
        $response = $this->actingAs($this->superAdmin)
            ->putJson("/api/admin/tenants/{$tenant->slug}/status", [
                'status' => 'active',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('active', $tenant->fresh()->status);
    }

    /** @test */
    public function super_admin_can_assign_users_to_tenants()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->postJson("/api/admin/tenants/{$tenant->slug}/users", [
                'user_id' => $user->id,
                'is_owner' => false,
            ]);

        $response->assertStatus(200);
        $this->assertTrue($user->belongsToTenant($tenant->id));
    }

    /** @test */
    public function super_admin_can_view_all_users()
    {
        User::factory()->count(10)->create();

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/api/admin/users');

        $response->assertStatus(200)
            ->assertJsonCount(11, 'data'); // 10 + super admin
    }

    /** @test */
    public function super_admin_can_create_user_with_role()
    {
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->postJson('/api/admin/users', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'role' => 'admin',
                'tenant_id' => $tenant->id,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);

        $user = User::where('email', 'john@example.com')->first();
        $this->assertTrue($user->hasRole('admin'));
    }

    /** @test */
    public function super_admin_can_update_user_role()
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($this->superAdmin)
            ->putJson("/api/admin/users/{$user->id}", [
                'role' => 'admin',
            ]);

        $response->assertStatus(200);
        $this->assertTrue($user->fresh()->hasRole('admin'));
    }

    /** @test */
    public function super_admin_can_delete_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->deleteJson("/api/admin/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function super_admin_can_view_visitor_analytics()
    {
        $tenant = Tenant::factory()->create();
        VisitorVisit::factory()->count(50)->create(['tenant_id' => $tenant->id]);

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/api/admin/analytics/visitors');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_visits',
                    'visits_by_tenant',
                    'visits_by_day',
                    'average_visit_duration',
                ],
            ]);
    }

    /** @test */
    public function super_admin_can_view_tenant_performance_reports()
    {
        Tenant::factory()->count(5)->create();

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/api/admin/reports/tenant-performance');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'tenants' => [
                        '*' => [
                            'id',
                            'name',
                            'total_visitors',
                            'total_meetings',
                            'active_users',
                        ],
                    ],
                ],
            ]);
    }

    /** @test */
    public function super_admin_can_view_activity_logs()
    {
        ActivityLog::factory()->count(20)->create();

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/api/admin/activity-logs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'log_type',
                        'description',
                        'created_at',
                    ],
                ],
            ]);
    }

    /** @test */
    public function super_admin_can_manage_system_settings()
    {
        $response = $this->actingAs($this->superAdmin)
            ->putJson('/api/admin/settings/system', [
                'data_retention_days' => 365,
                'max_users_per_tenant' => 100,
                'enable_two_factor' => true,
                'gdpr_compliance_mode' => true,
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function super_admin_can_configure_notification_templates()
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson('/api/admin/notification-templates', [
                'type' => 'visitor_check_in',
                'channel' => 'email',
                'subject' => 'Your visitor has arrived',
                'body' => 'Hello {host_name}, your visitor {visitor_name} has checked in.',
            ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function super_admin_can_manage_billing_for_tenant()
    {
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->postJson("/api/admin/tenants/{$tenant->slug}/billing", [
                'subscription_plan' => 'enterprise',
                'billing_cycle' => 'yearly',
                'payment_method' => 'credit_card',
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function super_admin_has_full_access_to_all_permissions()
    {
        $this->assertTrue($this->superAdmin->can('view tenants'));
        $this->assertTrue($this->superAdmin->can('create tenants'));
        $this->assertTrue($this->superAdmin->can('update tenants'));
        $this->assertTrue($this->superAdmin->can('delete tenants'));
        $this->assertTrue($this->superAdmin->can('manage tenants'));
        $this->assertTrue($this->superAdmin->can('view users'));
        $this->assertTrue($this->superAdmin->can('create users'));
        $this->assertTrue($this->superAdmin->can('delete users'));
        $this->assertTrue($this->superAdmin->can('impersonate users'));
        $this->assertTrue($this->superAdmin->can('manage kiosks'));
        $this->assertTrue($this->superAdmin->can('view reports'));
        $this->assertTrue($this->superAdmin->can('export reports'));
        $this->assertTrue($this->superAdmin->can('view settings'));
        $this->assertTrue($this->superAdmin->can('update settings'));
    }
}