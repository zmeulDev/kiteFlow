<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tenant;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\MeetingRoom;
use App\Models\Location;
use App\Models\User;
use App\Jobs\AutoCheckoutVisitsJob;
use App\Jobs\PurgeTenantDataJob;

class CronJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_checks_out_visits_at_end_of_day(): void
    {
        $tenant = Tenant::forceCreate(['name' => 'Test']);
        $visitor = Visitor::forceCreate(['tenant_id' => $tenant->id, 'name' => 'V', 'email' => 'v@v.com']);
        $location = Location::forceCreate(['tenant_id' => $tenant->id, 'name' => 'HQ']);
        $room = MeetingRoom::forceCreate(['tenant_id' => $tenant->id, 'location_id' => $location->id, 'name' => 'R']);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        
        $visit = Visit::forceCreate([
            'tenant_id' => $tenant->id,
            'visitor_id' => $visitor->id,
            'meeting_room_id' => $room->id,
            'host_user_id' => $user->id,
            'scheduled_at' => now()->subHours(5),
            'status' => 'checked_in',
            'invite_code' => 'TEST1'
        ]);
        
        AutoCheckoutVisitsJob::dispatchSync();
        
        $this->assertEquals('completed', $visit->fresh()->status);
        $this->assertNotNull($visit->fresh()->check_out_time);
    }

    public function test_purges_old_tenant_data_based_on_retention_policy(): void
    {
        $tenant = Tenant::forceCreate(['name' => 'Test', 'data_retention_days' => 30]);
        $visitor = Visitor::forceCreate(['tenant_id' => $tenant->id, 'name' => 'V2', 'email' => 'v2@v.com']);
        $location = Location::forceCreate(['tenant_id' => $tenant->id, 'name' => 'HQ']);
        $room = MeetingRoom::forceCreate(['tenant_id' => $tenant->id, 'location_id' => $location->id, 'name' => 'R']);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        
        $visit = Visit::forceCreate([
            'tenant_id' => $tenant->id,
            'visitor_id' => $visitor->id,
            'meeting_room_id' => $room->id,
            'host_user_id' => $user->id,
            'scheduled_at' => now()->subDays(40),
            'check_out_time' => now()->subDays(35),
            'status' => 'completed',
            'invite_code' => 'TEST2'
        ]);
        
        PurgeTenantDataJob::dispatchSync();
        
        $this->assertNull(Visit::find($visit->id));
        $this->assertNull(Visitor::find($visitor->id));
    }
}
