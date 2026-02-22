<?php

namespace Tests\Feature;

use App\Models\AccessPoint;
use App\Models\Building;
use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantAdminTest extends TestCase
{
    use RefreshDatabase;

    private User $tenantAdmin;
    private User $subTenantAdmin;
    private Tenant $parentTenant;
    private Tenant $subTenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        // Create parent tenant
        $this->parentTenant = Tenant::factory()->create([
            'name' => 'Parent Company',
            'status' => 'active',
        ]);

        // Create sub-tenant
        $this->subTenant = Tenant::factory()->create([
            'name' => 'Subsidiary Company',
            'parent_id' => $this->parentTenant->id,
            'status' => 'active',
        ]);

        // Create tenant admin (owner of parent tenant)
        $this->tenantAdmin = User::factory()->create();
        $this->tenantAdmin->assignRole('admin');
        $this->parentTenant->users()->attach($this->tenantAdmin->id, ['is_owner' => true]);

        // Create sub-tenant admin
        $this->subTenantAdmin = User::factory()->create();
        $this->subTenantAdmin->assignRole('admin');
        $this->subTenant->users()->attach($this->subTenantAdmin->id, ['is_owner' => true]);
    }

    /** @test */
    public function tenant_admin_can_view_their_tenant_details()
    {
        $response = $this->actingAs($this->tenantAdmin)
            ->getJson("/api/tenants/{$this->parentTenant->slug}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Parent Company']);
    }

    /** @test */
    public function tenant_admin_can_view_their_sub_tenants()
    {
        $response = $this->actingAs($this->tenantAdmin)
            ->getJson("/api/tenants/{$this->parentTenant->slug}/sub-tenants");

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Subsidiary Company']);
    }

    /** @test */
    public function tenant_admin_can_create_sub_tenant()
    {
        $response = $this->actingAs($this->tenantAdmin)
            ->postJson("/api/tenants/{$this->parentTenant->slug}/sub-tenants", [
                'name' => 'New Subsidiary',
                'email' => 'contact@subsidiary.com',
                'phone' => '+1234567890',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tenants', [
            'name' => 'New Subsidiary',
            'parent_id' => $this->parentTenant->id,
        ]);
    }

    /** @test */
    public function tenant_admin_can_update_sub_tenant_details()
    {
        $response = $this->actingAs($this->tenantAdmin)
            ->putJson("/api/tenants/{$this->parentTenant->slug}/sub-tenants/{$this->subTenant->id}", [
                'name' => 'Updated Subsidiary',
                'status' => 'active',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated Subsidiary', $this->subTenant->fresh()->name);
    }

    /** @test */
    public function tenant_admin_can_delete_sub_tenant()
    {
        $response = $this->actingAs($this->tenantAdmin)
            ->deleteJson("/api/tenants/{$this->parentTenant->slug}/sub-tenants/{$this->subTenant->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('tenants', ['id' => $this->subTenant->id]);
    }

    /** @test */
    public function tenant_admin_can_create_building()
    {
        $response = $this->actingAs($this->tenantAdmin)
            ->postJson("/api/tenants/{$this->parentTenant->slug}/buildings", [
                'name' => 'Main Building',
                'address' => '123 Business Ave',
                'city' => 'New York',
                'floors' => 10,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('buildings', [
            'tenant_id' => $this->parentTenant->id,
            'name' => 'Main Building',
        ]);
    }

    /** @test */
    public function tenant_admin_can_create_meeting_room()
    {
        Building::factory()->create(['tenant_id' => $this->parentTenant->id]);

        $response = $this->actingAs($this->tenantAdmin)
            ->postJson("/api/tenants/{$this->parentTenant->slug}/meeting-rooms", [
                'name' => 'Conference Room A',
                'capacity' => 20,
                'location' => 'Floor 3',
                'amenities' => ['projector', 'whiteboard', 'wifi'],
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('meeting_rooms', [
            'tenant_id' => $this->parentTenant->id,
            'name' => 'Conference Room A',
        ]);
    }

    /** @test */
    public function tenant_admin_can_add_users_to_tenant()
    {
        $response = $this->actingAs($this->tenantAdmin)
            ->postJson("/api/tenants/{$this->parentTenant->slug}/users", [
                'name' => 'New User',
                'email' => 'newuser@parent.com',
                'password' => 'password123',
                'role' => 'user',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'newuser@parent.com']);

        $user = User::where('email', 'newuser@parent.com')->first();
        $this->assertTrue($user->belongsToTenant($this->parentTenant->id));
    }

    /** @test */
    public function tenant_admin_can_schedule_meeting()
    {
        $room = MeetingRoom::factory()->create(['tenant_id' => $this->parentTenant->id]);

        $response = $this->actingAs($this->tenantAdmin)
            ->postJson("/api/tenants/{$this->parentTenant->slug}/meetings", [
                'title' => 'Quarterly Review',
                'meeting_room_id' => $room->id,
                'start_at' => now()->addDay()->setHour(10)->format('Y-m-d H:i:s'),
                'end_at' => now()->addDay()->setHour(12)->format('Y-m-d H:i:s'),
                'description' => 'Q4 Performance Review',
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('meetings', [
            'tenant_id' => $this->parentTenant->id,
            'title' => 'Quarterly Review',
            'host_id' => $this->tenantAdmin->id,
        ]);
    }

    /** @test */
    public function tenant_admin_can_invite_visitor_to_meeting()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->parentTenant->id,
            'host_id' => $this->tenantAdmin->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->postJson("/api/tenants/{$this->parentTenant->slug}/meetings/{$meeting->id}/invite-visitor", [
                'first_name' => 'John',
                'last_name' => 'Visitor',
                'email' => 'john@visitor.com',
                'company' => 'Visitor Corp',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('visitors', [
            'email' => 'john@visitor.com',
            'tenant_id' => $this->parentTenant->id,
        ]);
    }

    /** @test */
    public function tenant_admin_can_cancel_meeting()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->parentTenant->id,
            'host_id' => $this->tenantAdmin->id,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->postJson("/api/tenants/{$this->parentTenant->slug}/meetings/{$meeting->id}/cancel", [
                'reason' => 'Emergency reschedule',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('cancelled', $meeting->fresh()->status);
    }

    /** @test */
    public function tenant_admin_can_view_visitors()
    {
        Visitor::factory()->count(5)->create(['tenant_id' => $this->parentTenant->id]);

        $response = $this->actingAs($this->tenantAdmin)
            ->getJson("/api/tenants/{$this->parentTenant->slug}/visitors");

        $response->assertStatus(200)
            ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function tenant_admin_can_blacklist_visitor()
    {
        $visitor = Visitor::factory()->create(['tenant_id' => $this->parentTenant->id]);

        $response = $this->actingAs($this->tenantAdmin)
            ->postJson("/api/tenants/{$this->parentTenant->slug}/visitors/{$visitor->id}/blacklist", [
                'reason' => 'Security concern',
            ]);

        $response->assertStatus(200);
        $this->assertTrue($visitor->fresh()->is_blacklisted);
        $this->assertEquals('Security concern', $visitor->fresh()->blacklist_reason);
    }

    /** @test */
    public function tenant_admin_can_manage_kiosk_settings()
    {
        $accessPoint = AccessPoint::factory()->create([
            'tenant_id' => $this->parentTenant->id,
            'is_kiosk_mode' => true,
        ]);

        $response = $this->actingAs($this->tenantAdmin)
            ->putJson("/api/tenants/{$this->parentTenant->slug}/kiosks/{$accessPoint->code}", [
                'settings' => [
                    'require_id_scan' => true,
                    'capture_photo' => true,
                    'show_nda' => true,
                    'show_gdpr' => true,
                    'custom_fields' => ['company', 'purpose'],
                ],
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function tenant_admin_can_view_analytics()
    {
        VisitorVisit::factory()->count(20)->create(['tenant_id' => $this->parentTenant->id]);

        $response = $this->actingAs($this->tenantAdmin)
            ->getJson("/api/tenants/{$this->parentTenant->slug}/analytics");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_visitors',
                    'total_meetings',
                    'average_visit_duration',
                    'visitors_by_day',
                ],
            ]);
    }

    /** @test */
    public function tenant_admin_can_update_notification_preferences()
    {
        $response = $this->actingAs($this->tenantAdmin)
            ->putJson("/api/tenants/{$this->parentTenant->slug}/notifications-preferences", [
                'visitor_check_in' => ['email' => true, 'sms' => false],
                'meeting_reminder' => ['email' => true, 'sms' => true],
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function sub_tenant_admin_can_view_own_tenant_details()
    {
        $response = $this->actingAs($this->subTenantAdmin)
            ->getJson("/api/tenants/{$this->subTenant->slug}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Subsidiary Company']);
    }

    /** @test */
    public function sub_tenant_admin_can_add_users()
    {
        $response = $this->actingAs($this->subTenantAdmin)
            ->postJson("/api/tenants/{$this->subTenant->slug}/users", [
                'name' => 'Sub Tenant User',
                'email' => 'user@subsidiary.com',
                'password' => 'password123',
                'role' => 'user',
            ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function sub_tenant_admin_can_schedule_meeting_in_parent_rooms()
    {
        // Sub-tenant can use parent tenant's meeting rooms
        $room = MeetingRoom::factory()->create(['tenant_id' => $this->parentTenant->id]);

        $response = $this->actingAs($this->subTenantAdmin)
            ->postJson("/api/tenants/{$this->subTenant->slug}/meetings", [
                'title' => 'Team Sync',
                'meeting_room_id' => $room->id,
                'start_at' => now()->addDay()->setHour(14)->format('Y-m-d H:i:s'),
                'end_at' => now()->addDay()->setHour(15)->format('Y-m-d H:i:s'),
            ]);

        $response->assertStatus(201);
    }

    /** @test */
    public function sub_tenant_admin_can_receive_visitors()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->subTenant->id,
            'host_id' => $this->subTenantAdmin->id,
        ]);

        $response = $this->actingAs($this->subTenantAdmin)
            ->postJson("/api/tenants/{$this->subTenant->slug}/meetings/{$meeting->id}/invite-visitor", [
                'first_name' => 'Jane',
                'last_name' => 'Guest',
                'email' => 'jane@guest.com',
            ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function tenant_admin_cannot_access_other_tenant_data()
    {
        $otherTenant = Tenant::factory()->create();
        Visitor::factory()->count(5)->create(['tenant_id' => $otherTenant->id]);

        $response = $this->actingAs($this->tenantAdmin)
            ->getJson("/api/tenants/{$otherTenant->slug}/visitors");

        $response->assertStatus(403);
    }

    /** @test */
    public function tenant_admin_permissions_are_correct()
    {
        $this->assertTrue($this->tenantAdmin->can('view tenants'));
        $this->assertTrue($this->tenantAdmin->can('view users'));
        $this->assertTrue($this->tenantAdmin->can('create users'));
        $this->assertTrue($this->tenantAdmin->can('manage users'));
        $this->assertTrue($this->tenantAdmin->can('view visitors'));
        $this->assertTrue($this->tenantAdmin->can('check-in visitors'));
        $this->assertTrue($this->tenantAdmin->can('blacklist visitors'));
        $this->assertTrue($this->tenantAdmin->can('view meetings'));
        $this->assertTrue($this->tenantAdmin->can('create meetings'));
        $this->assertTrue($this->tenantAdmin->can('manage kiosks'));
        $this->assertTrue($this->tenantAdmin->can('view reports'));

        // Cannot manage tenants at super-admin level
        $this->assertFalse($this->tenantAdmin->can('manage tenants'));
        $this->assertFalse($this->tenantAdmin->can('impersonate users'));
    }
}