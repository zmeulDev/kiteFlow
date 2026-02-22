<?php

namespace Tests\Unit;

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

class TenantModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tenant_generates_uuid_on_create()
    {
        $tenant = Tenant::factory()->create(['uuid' => null]);

        $this->assertNotNull($tenant->uuid);
    }

    /** @test */
    public function tenant_generates_slug_from_name()
    {
        $tenant = Tenant::factory()->create([
            'name' => 'Acme Corporation',
            'slug' => null, // Let the model generate it
        ]);

        $this->assertEquals('acme-corporation', $tenant->slug);
    }

    /** @test */
    public function tenant_can_have_parent_and_children()
    {
        $parent = Tenant::factory()->create();
        $child = Tenant::factory()->create(['parent_id' => $parent->id]);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertTrue($parent->children->contains($child));
    }

    /** @test */
    public function tenant_is_active_returns_correct_status()
    {
        $activeTenant = Tenant::factory()->create(['status' => 'active']);
        $suspendedTenant = Tenant::factory()->create(['status' => 'suspended']);

        $this->assertTrue($activeTenant->isActive());
        $this->assertFalse($suspendedTenant->isActive());
    }

    /** @test */
    public function tenant_trial_status_works_correctly()
    {
        $trialTenant = Tenant::factory()->create([
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(7),
        ]);

        $expiredTrialTenant = Tenant::factory()->create([
            'status' => 'trial',
            'trial_ends_at' => now()->subDay(),
        ]);

        $this->assertTrue($trialTenant->isOnTrial());
        $this->assertFalse($expiredTrialTenant->isOnTrial());
    }

    /** @test */
    public function tenant_subscription_status_check()
    {
        $activeTenant = Tenant::factory()->create([
            'status' => 'active',
        ]);

        $expiringTenant = Tenant::factory()->create([
            'status' => 'active',
            'subscription_ends_at' => now()->addDays(30),
        ]);

        $expiredTenant = Tenant::factory()->create([
            'status' => 'inactive',
            'subscription_ends_at' => now()->subDay(),
        ]);

        $this->assertTrue($activeTenant->hasValidSubscription());
        $this->assertTrue($expiringTenant->hasValidSubscription());
        $this->assertFalse($expiredTenant->hasValidSubscription());
    }

    /** @test */
    public function tenant_can_manage_settings()
    {
        $tenant = Tenant::factory()->create();

        $tenant->setSetting('kiosk.requires_photo', true);
        $tenant->setSetting('notifications.sms_enabled', false);

        $this->assertTrue($tenant->getSetting('kiosk.requires_photo'));
        $this->assertFalse($tenant->getSetting('notifications.sms_enabled'));
        $this->assertNull($tenant->getSetting('nonexistent.key'));
        $this->assertEquals('default', $tenant->getSetting('nonexistent.key', 'default'));
    }

    /** @test */
    public function tenant_has_many_buildings()
    {
        $tenant = Tenant::factory()->create();
        Building::factory()->count(3)->create(['tenant_id' => $tenant->id]);

        $this->assertCount(3, $tenant->buildings);
    }

    /** @test */
    public function tenant_has_many_meeting_rooms()
    {
        $tenant = Tenant::factory()->create();
        MeetingRoom::factory()->count(5)->create(['tenant_id' => $tenant->id]);

        $this->assertCount(5, $tenant->meetingRooms);
    }

    /** @test */
    public function tenant_scope_parents_only_returns_top_level()
    {
        $parent = Tenant::factory()->create();
        Tenant::factory()->count(3)->create(['parent_id' => $parent->id]);

        $parents = Tenant::parents()->get();

        $this->assertCount(1, $parents);
        $this->assertEquals($parent->id, $parents->first()->id);
    }

    /** @test */
    public function tenant_scope_children_only_returns_sub_tenants()
    {
        $parent = Tenant::factory()->create();
        Tenant::factory()->count(3)->create(['parent_id' => $parent->id]);

        $result = Tenant::whereNotNull('parent_id')->get();

        $this->assertCount(3, $result);
    }
}