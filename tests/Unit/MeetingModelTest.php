<?php

namespace Tests\Unit;

use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingModelTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private User $host;
    private MeetingRoom $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->host = User::factory()->create();
        $this->room = MeetingRoom::factory()->create(['tenant_id' => $this->tenant->id]);
    }

    /** @test */
    public function meeting_generates_uuid_on_create()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
        ]);

        $this->assertNotNull($meeting->uuid);
    }

    /** @test */
    public function meeting_duration_is_calculated_correctly()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->setHour(10),
            'end_at' => now()->setHour(12),
        ]);

        $this->assertEquals(120, $meeting->getDurationInMinutes());
        $this->assertEquals('2h 0m', $meeting->getDurationFormatted());
    }

    /** @test */
    public function meeting_status_checks_work_correctly()
    {
        $pastMeeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->subHours(3),
            'end_at' => now()->subHours(1),
        ]);

        $ongoingMeeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->subMinutes(30),
            'end_at' => now()->addHours(1),
        ]);

        $upcomingMeeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->addHours(2),
            'end_at' => now()->addHours(3),
        ]);

        $this->assertTrue($pastMeeting->isPast());
        $this->assertFalse($pastMeeting->isOngoing());
        $this->assertFalse($pastMeeting->isUpcoming());

        $this->assertFalse($ongoingMeeting->isPast());
        $this->assertTrue($ongoingMeeting->isOngoing());
        $this->assertFalse($ongoingMeeting->isUpcoming());

        $this->assertFalse($upcomingMeeting->isPast());
        $this->assertFalse($upcomingMeeting->isOngoing());
        $this->assertTrue($upcomingMeeting->isUpcoming());
    }

    /** @test */
    public function meeting_can_be_cancelled()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
            'status' => 'scheduled',
        ]);

        $meeting->cancel('Host unavailable');

        $this->assertEquals('cancelled', $meeting->status);
        $this->assertEquals('Host unavailable', $meeting->cancellation_reason);
    }

    /** @test */
    public function meeting_can_be_completed()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
            'status' => 'scheduled',
        ]);

        $meeting->markAsCompleted();

        $this->assertEquals('completed', $meeting->status);
    }

    /** @test */
    public function meeting_can_add_attendees()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
        ]);

        $visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);
        $user = User::factory()->create();

        $meeting->addAttendee($visitor, 'required');
        $meeting->addAttendee($user, 'optional');

        $this->assertCount(2, $meeting->attendees);
        $this->assertTrue($meeting->visitorAttendees->contains($visitor));
        $this->assertTrue($meeting->userAttendees->contains($user));
    }

    /** @test */
    public function meeting_scope_upcoming_returns_future_meetings()
    {
        Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHours(2),
            'status' => 'scheduled',
        ]);

        Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->subDay(),
            'end_at' => now()->subDay()->addHours(2),
            'status' => 'completed',
        ]);

        $upcoming = Meeting::upcoming()->get();

        $this->assertCount(1, $upcoming);
    }

    /** @test */
    public function meeting_scope_today_returns_today_meetings()
    {
        Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->startOfDay()->addHours(10),
            'end_at' => now()->startOfDay()->addHours(12),
        ]);

        Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->addDay(),
            'end_at' => now()->addDay()->addHours(2),
        ]);

        $todayMeetings = Meeting::today()->get();

        $this->assertCount(1, $todayMeetings);
    }
}