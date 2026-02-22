<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorModelTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
    }

    /** @test */
    public function visitor_generates_uuid_on_create()
    {
        $visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);

        $this->assertNotNull($visitor->uuid);
    }

    /** @test */
    public function visitor_full_name_is_appended()
    {
        $visitor = Visitor::factory()->create([
            'tenant_id' => $this->tenant->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $this->assertEquals('John Doe', $visitor->full_name);
    }

    /** @test */
    public function visitor_can_be_blacklisted()
    {
        $visitor = Visitor::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_blacklisted' => false,
        ]);

        $visitor->blacklist('Security violation');

        $this->assertTrue($visitor->fresh()->is_blacklisted);
        $this->assertEquals('Security violation', $visitor->fresh()->blacklist_reason);
    }

    /** @test */
    public function visitor_can_be_unblacklisted()
    {
        $visitor = Visitor::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_blacklisted' => true,
            'blacklist_reason' => 'Previous violation',
        ]);

        $visitor->unblacklist();

        $this->assertFalse($visitor->fresh()->is_blacklisted);
        $this->assertNull($visitor->fresh()->blacklist_reason);
    }

    /** @test */
    public function visitor_checked_in_status()
    {
        $visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);
        $host = User::factory()->create();

        // No visits
        $this->assertFalse($visitor->isCheckedIn());

        // Active visit
        VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $visitor->id,
            'host_id' => $host->id,
            'status' => 'checked_in',
            'check_out_at' => null,
        ]);

        $this->assertTrue($visitor->fresh()->isCheckedIn());
    }

    /** @test */
    public function visitor_current_visit_returns_active_visit()
    {
        $visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);
        $host = User::factory()->create();

        $activeVisit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $visitor->id,
            'host_id' => $host->id,
            'status' => 'checked_in',
            'check_out_at' => null,
        ]);

        VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $visitor->id,
            'host_id' => $host->id,
            'status' => 'checked_out',
            'check_out_at' => now()->subHour(),
        ]);

        $currentVisit = $visitor->getCurrentVisit();

        $this->assertNotNull($currentVisit);
        $this->assertEquals($activeVisit->id, $currentVisit->id);
    }

    /** @test */
    public function visitor_has_many_visits()
    {
        $visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);
        $host = User::factory()->create();

        VisitorVisit::factory()->count(5)->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $visitor->id,
            'host_id' => $host->id,
        ]);

        $this->assertCount(5, $visitor->visits);
    }

    /** @test */
    public function visitor_scope_blacklisted_returns_only_blacklisted()
    {
        Visitor::factory()->count(3)->create(['tenant_id' => $this->tenant->id, 'is_blacklisted' => false]);
        Visitor::factory()->count(2)->create(['tenant_id' => $this->tenant->id, 'is_blacklisted' => true]);

        $blacklisted = Visitor::blacklisted()->get();

        $this->assertCount(2, $blacklisted);
    }

    /** @test */
    public function visitor_scope_active_returns_non_blacklisted()
    {
        Visitor::factory()->count(3)->create(['tenant_id' => $this->tenant->id, 'is_blacklisted' => false]);
        Visitor::factory()->count(2)->create(['tenant_id' => $this->tenant->id, 'is_blacklisted' => true]);

        $active = Visitor::active()->get();

        $this->assertCount(3, $active);
    }

    /** @test */
    public function visitor_soft_deletes()
    {
        $visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);
        $visitor->delete();

        $this->assertSoftDeleted('visitors', ['id' => $visitor->id]);
        $this->assertNotNull(Visitor::withTrashed()->find($visitor->id));
    }
}