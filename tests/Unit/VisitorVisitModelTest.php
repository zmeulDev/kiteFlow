<?php

namespace Tests\Unit;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorVisitModelTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $host;
    private Visitor $visitor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = Tenant::factory()->create();
        $this->host = User::factory()->create();
        $this->visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);
    }

    /** @test */
    public function visitor_visit_generates_uuid_on_create()
    {
        $visit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
        ]);

        $this->assertNotNull($visit->uuid);
    }

    /** @test */
    public function visitor_visit_generates_badge_number_on_create()
    {
        $visit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'badge_number' => null,
        ]);

        $this->assertNotNull($visit->badge_number);
        $this->assertStringStartsWith('V', $visit->badge_number);
    }

    /** @test */
    public function visitor_visit_sets_check_in_time_automatically()
    {
        $visit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'check_in_at' => null,
        ]);

        $this->assertNotNull($visit->check_in_at);
    }

    /** @test */
    public function visitor_visit_can_be_checked_out()
    {
        $visit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'status' => 'checked_in',
            'check_out_at' => null,
        ]);

        $visit->checkOut($this->host->id);

        $this->assertNotNull($visit->fresh()->check_out_at);
        $this->assertEquals('checked_out', $visit->fresh()->status);
        $this->assertEquals($this->host->id, $visit->fresh()->checked_out_by);
    }

    /** @test */
    public function visitor_visit_can_be_cancelled()
    {
        $visit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'status' => 'checked_in',
        ]);

        $visit->cancel('Visitor did not show up');

        $this->assertEquals('cancelled', $visit->fresh()->status);
        $this->assertStringContainsString('Visitor did not show up', $visit->fresh()->notes);
    }

    /** @test */
    public function visitor_visit_can_be_marked_as_no_show()
    {
        $visit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'status' => 'checked_in',
        ]);

        $visit->markAsNoShow();

        $this->assertEquals('no_show', $visit->fresh()->status);
    }

    /** @test */
    public function visitor_visit_duration_is_calculated_correctly()
    {
        $visit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'check_in_at' => now()->subHours(2)->subMinutes(30),
            'check_out_at' => now(),
        ]);

        $this->assertEquals(150, $visit->getDurationInMinutes());
        $this->assertEquals('2h 30m', $visit->getDurationFormatted());
    }

    /** @test */
    public function visitor_visit_duration_returns_zero_if_not_checked_out()
    {
        $visit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'check_out_at' => null,
        ]);

        $this->assertEquals(0, $visit->getDurationInMinutes());
    }

    /** @test */
    public function visitor_visit_is_active_check()
    {
        $activeVisit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'status' => 'checked_in',
            'check_out_at' => null,
        ]);

        $checkedOutVisit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'status' => 'checked_out',
            'check_out_at' => now(),
        ]);

        $this->assertTrue($activeVisit->isActive());
        $this->assertFalse($checkedOutVisit->isActive());
    }

    /** @test */
    public function badge_number_format_is_correct()
    {
        $badgeNumber = VisitorVisit::generateBadgeNumber();

        $this->assertStringStartsWith('V', $badgeNumber);
        $this->assertEquals(13, strlen($badgeNumber)); // V + YYYYMMDD + 4 digits
    }

    /** @test */
    public function visitor_visit_scope_active_returns_only_active()
    {
        VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'status' => 'checked_in',
            'check_out_at' => null,
        ]);

        VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'status' => 'checked_out',
            'check_out_at' => now(),
        ]);

        $activeVisits = VisitorVisit::active()->get();

        $this->assertCount(1, $activeVisits);
    }

    /** @test */
    public function visitor_visit_scope_today_returns_only_today()
    {
        VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'check_in_at' => now(),
        ]);

        VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'host_id' => $this->host->id,
            'check_in_at' => now()->subDay(),
        ]);

        $todayVisits = VisitorVisit::today()->get();

        $this->assertCount(1, $todayVisits);
    }
}