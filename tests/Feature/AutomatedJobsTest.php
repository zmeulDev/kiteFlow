<?php

namespace Tests\Feature;

use App\Models\CheckIn;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\MeetingRoom;
use App\Models\Building;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutomatedJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_checkout_command_checks_out_past_visitors()
    {
        $tenant = Tenant::factory()->create();
        $visitor = Visitor::factory()->create(['tenant_id' => $tenant->id]);
        $building = Building::factory()->create(['tenant_id' => $tenant->id]);
        $room = MeetingRoom::factory()->create(['building_id' => $building->id]);

        $pastVisit = Visit::factory()->create([
            'tenant_id' => $tenant->id,
            'visitor_id' => $visitor->id,
            'meeting_room_id' => $room->id,
            'status' => 'checked_in',
            'scheduled_start' => Carbon::now()->subHours(3),
            'scheduled_end' => Carbon::now()->subHours(2),
        ]);

        $checkIn = CheckIn::create([
            'tenant_id' => $tenant->id,
            'visit_id' => $pastVisit->id,
            'visitor_id' => $visitor->id,
            'check_in_time' => Carbon::now()->subHours(3),
            'check_out_time' => null,
            'check_in_method' => 'manual',
            'status' => 'active'
        ]);

        $futureVisit = Visit::factory()->create([
            'tenant_id' => $tenant->id,
            'visitor_id' => $visitor->id,
            'meeting_room_id' => $room->id,
            'status' => 'checked_in',
            'scheduled_start' => Carbon::now()->addHours(1),
            'scheduled_end' => Carbon::now()->addHours(2),
        ]);

        $this->artisan('visitors:auto-checkout')
            ->expectsOutput('Running auto-checkout for visitors...')
            ->assertExitCode(0);

        $this->assertDatabaseHas('visits', [
            'id' => $pastVisit->id,
            'status' => 'checked_out',
        ]);
        
        $this->assertDatabaseHas('check_ins', [
            'visit_id' => $pastVisit->id,
            'check_out_method' => 'auto',
        ]);

        $this->assertDatabaseHas('visits', [
            'id' => $futureVisit->id,
            'status' => 'checked_in',
        ]);
    }

    public function test_gdpr_purge_command_deletes_old_data_based_on_retention()
    {
        $tenant1 = Tenant::factory()->create(['gdpr_retention_months' => 6]);
        $tenant2 = Tenant::factory()->create(['gdpr_retention_months' => 12]);
        
        $visitor1 = Visitor::factory()->create(['tenant_id' => $tenant1->id]);
        $visitor2 = Visitor::factory()->create(['tenant_id' => $tenant2->id]);
        
        $building1 = Building::factory()->create(['tenant_id' => $tenant1->id]);
        $room1 = MeetingRoom::factory()->create(['building_id' => $building1->id]);

        $building2 = Building::factory()->create(['tenant_id' => $tenant2->id]);
        $room2 = MeetingRoom::factory()->create(['building_id' => $building2->id]);

        // A visit from 7 months ago for tenant 1 (retention is 6) -> Should be deleted
        $visitT1Old = Visit::factory()->create([
            'tenant_id' => $tenant1->id,
            'visitor_id' => $visitor1->id,
            'meeting_room_id' => $room1->id,
            'status' => 'checked_out',
            'checked_out_at' => Carbon::now()->subMonths(7),
        ]);
        
        // A visit from 5 months ago for tenant 1 -> Should be kept
        $visitT1Recent = Visit::factory()->create([
            'tenant_id' => $tenant1->id,
            'visitor_id' => $visitor1->id,
            'meeting_room_id' => $room1->id,
            'status' => 'checked_out',
            'checked_out_at' => Carbon::now()->subMonths(5),
        ]);

        // A visit from 7 months ago for tenant 2 (retention is 12) -> Should be kept
        $visitT2Old = Visit::factory()->create([
            'tenant_id' => $tenant2->id,
            'visitor_id' => $visitor2->id,
            'meeting_room_id' => $room2->id,
            'status' => 'checked_out',
            'checked_out_at' => Carbon::now()->subMonths(7),
        ]);

        $this->artisan('visitors:purge-gdpr')
            ->expectsOutput('Starting GDPR data purge...')
            ->assertExitCode(0);

        $this->assertSoftDeleted('visits', ['id' => $visitT1Old->id]);
        $this->assertDatabaseHas('visits', ['id' => $visitT1Recent->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('visits', ['id' => $visitT2Old->id, 'deleted_at' => null]);
    }
}
