<?php

namespace Tests\Unit;

use App\Models\Meeting;
use App\Models\Tenant;
use App\Models\User;
use App\Models\VisitorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    /** @test */
    public function user_can_belong_to_multiple_tenants()
    {
        $user = User::factory()->create();
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        $user->tenants()->attach([$tenant1->id, $tenant2->id]);

        $this->assertCount(2, $user->tenants);
    }

    /** @test */
    public function user_can_be_tenant_owner()
    {
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create();

        $user->tenants()->attach($tenant->id, ['is_owner' => true]);

        $this->assertTrue($user->isTenantAdmin($tenant->id));
        $this->assertTrue($user->tenants()->wherePivot('is_owner', true)->exists());
    }

    /** @test */
    public function user_role_checks_work_correctly()
    {
        $superAdmin = User::factory()->create();
        $superAdmin->assignRole('super-admin');

        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $user = User::factory()->create();
        $user->assignRole('user');

        $this->assertTrue($superAdmin->isSuperAdmin());
        $this->assertTrue($superAdmin->isAdmin());
        $this->assertFalse($admin->isSuperAdmin());
        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($user->isSuperAdmin());
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function user_can_manage_preferences()
    {
        $user = User::factory()->create(['preferences' => []]);

        $user->setPreference('notifications.email', true);
        $user->setPreference('notifications.sms', false);
        $user->setPreference('theme', 'dark');

        $this->assertTrue($user->getPreference('notifications.email'));
        $this->assertFalse($user->getPreference('notifications.sms'));
        $this->assertEquals('dark', $user->getPreference('theme'));
        $this->assertNull($user->getPreference('nonexistent'));
        $this->assertEquals('default', $user->getPreference('nonexistent', 'default'));
    }

    /** @test */
    public function user_can_record_login()
    {
        $user = User::factory()->create(['last_login_at' => null]);

        $user->recordLogin();

        $this->assertNotNull($user->fresh()->last_login_at);
    }

    /** @test */
    public function user_can_be_activated_and_deactivated()
    {
        $user = User::factory()->create(['is_active' => false]);

        $user->activate();
        $this->assertTrue($user->fresh()->is_active);

        $user->deactivate();
        $this->assertFalse($user->fresh()->is_active);
    }

    /** @test */
    public function user_has_meetings()
    {
        $user = User::factory()->create();
        Meeting::factory()->count(5)->create(['host_id' => $user->id]);

        $this->assertCount(5, $user->meetings);
    }

    /** @test */
    public function user_has_hosted_meetings()
    {
        $user = User::factory()->create();
        Meeting::factory()->count(3)->create([
            'host_id' => $user->id,
            'status' => 'scheduled',
        ]);
        Meeting::factory()->count(2)->create([
            'host_id' => $user->id,
            'status' => 'completed',
        ]);

        $this->assertCount(3, $user->hostedMeetings);
    }

    /** @test */
    public function user_has_visitor_visits_as_host()
    {
        $user = User::factory()->create();
        VisitorVisit::factory()->count(7)->create(['host_id' => $user->id]);

        $this->assertCount(7, $user->visitorVisitsAsHost);
    }

    /** @test */
    public function user_scope_active_returns_only_active_users()
    {
        User::factory()->count(3)->create(['is_active' => true]);
        User::factory()->count(2)->create(['is_active' => false]);

        $activeUsers = User::active()->get();

        $this->assertCount(3, $activeUsers);
    }

    /** @test */
    public function user_scope_by_tenant_filters_correctly()
    {
        $tenant = Tenant::factory()->create();
        $otherTenant = Tenant::factory()->create();

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $user1->tenants()->attach($tenant->id);
        $user2->tenants()->attach($otherTenant->id);

        $tenantUsers = User::byTenant($tenant->id)->get();

        $this->assertCount(1, $tenantUsers);
        $this->assertEquals($user1->id, $tenantUsers->first()->id);
    }

    /** @test */
    public function user_belongs_to_tenant_check()
    {
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create();

        $this->assertFalse($user->belongsToTenant($tenant->id));

        $user->tenants()->attach($tenant->id);

        $this->assertTrue($user->fresh()->belongsToTenant($tenant->id));
    }

    /** @test */
    public function user_get_current_tenant()
    {
        $user = User::factory()->create();
        $tenant = Tenant::factory()->create();

        $this->assertNull($user->getCurrentTenant());

        $user->tenants()->attach($tenant->id);

        $this->assertEquals($tenant->id, $user->fresh()->getCurrentTenant()->id);
    }
}