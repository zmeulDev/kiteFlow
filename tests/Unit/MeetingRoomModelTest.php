<?php

namespace Tests\Unit;

use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeetingRoomModelTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private MeetingRoom $room;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::factory()->create();
        $this->room = MeetingRoom::factory()->create([
            'tenant_id' => $this->tenant->id,
            'amenities' => ['projector', 'whiteboard', 'wifi', 'video_conference'],
        ]);
    }

    /** @test */
    public function meeting_room_generates_uuid_on_create()
    {
        $room = MeetingRoom::factory()->create(['tenant_id' => $this->tenant->id, 'uuid' => null]);

        $this->assertNotNull($room->uuid);
    }

    /** @test */
    public function meeting_room_generates_code_on_create()
    {
        $room = MeetingRoom::factory()->create(['tenant_id' => $this->tenant->id, 'code' => null]);

        $this->assertNotNull($room->code);
        $this->assertEquals(6, strlen($room->code));
    }

    /** @test */
    public function meeting_room_availability_check_works()
    {
        // Create a meeting from 10:00 to 12:00
        Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->setTime(10, 0)->setSecond(0),
            'end_at' => now()->setTime(12, 0)->setSecond(0),
            'status' => 'scheduled',
        ]);

        // Requested time 8:00-9:30 should be available (before the meeting)
        $this->assertTrue($this->room->isAvailable(
            now()->setTime(8, 0)->setSecond(0)->format('Y-m-d H:i:s'),
            now()->setTime(9, 30)->setSecond(0)->format('Y-m-d H:i:s')
        ));

        // Requested time 11:00-13:00 should not be available (overlaps)
        $this->assertFalse($this->room->isAvailable(
            now()->setTime(11, 0)->setSecond(0)->format('Y-m-d H:i:s'),
            now()->setTime(13, 0)->setSecond(0)->format('Y-m-d H:i:s')
        ));

        // Requested time 10:30-11:30 should not be available (during meeting)
        $this->assertFalse($this->room->isAvailable(
            now()->setTime(10, 30)->setSecond(0)->format('Y-m-d H:i:s'),
            now()->setTime(11, 30)->setSecond(0)->format('Y-m-d H:i:s')
        ));

        // Requested time 13:00-14:00 should be available (after the meeting)
        $this->assertTrue($this->room->isAvailable(
            now()->setTime(13, 0)->setSecond(0)->format('Y-m-d H:i:s'),
            now()->setTime(14, 0)->setSecond(0)->format('Y-m-d H:i:s')
        ));
    }

    /** @test */
    public function meeting_room_availability_excludes_specific_meeting()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->setTime(10, 0),
            'end_at' => now()->setTime(12, 0),
            'status' => 'scheduled',
        ]);

        // When updating an existing meeting, exclude it from availability check
        $this->assertTrue($this->room->isAvailable(
            now()->setTime(10, 0)->format('Y-m-d H:i:s'),
            now()->setTime(12, 0)->format('Y-m-d H:i:s'),
            $meeting->id
        ));
    }

    /** @test */
    public function meeting_room_availability_ignores_cancelled_meetings()
    {
        Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->setTime(10, 0),
            'end_at' => now()->setTime(12, 0),
            'status' => 'cancelled',
        ]);

        // Should be available because the meeting is cancelled
        $this->assertTrue($this->room->isAvailable(
            now()->setTime(10, 0)->format('Y-m-d H:i:s'),
            now()->setTime(12, 0)->format('Y-m-d H:i:s')
        ));
    }

    /** @test */
    public function meeting_room_has_amenity_check()
    {
        $this->assertTrue($this->room->hasAmenity('projector'));
        $this->assertTrue($this->room->hasAmenity('wifi'));
        $this->assertFalse($this->room->hasAmenity('catering'));
    }

    /** @test */
    public function meeting_room_gets_availability_for_date()
    {
        Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->setTime(9, 0),
            'end_at' => now()->setTime(10, 0),
            'status' => 'scheduled',
        ]);

        Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'meeting_room_id' => $this->room->id,
            'start_at' => now()->setTime(14, 0),
            'end_at' => now()->setTime(16, 0),
            'status' => 'scheduled',
        ]);

        $availability = $this->room->getAvailabilityForDate(now()->format('Y-m-d'));

        $this->assertCount(2, $availability);
        $this->assertEquals('09:00', $availability[0]['start']);
        $this->assertEquals('14:00', $availability[1]['start']);
    }

    /** @test */
    public function meeting_room_scope_active_returns_only_active_rooms()
    {
        MeetingRoom::factory()->count(3)->create(['tenant_id' => $this->tenant->id, 'is_active' => true]);
        MeetingRoom::factory()->count(2)->create(['tenant_id' => $this->tenant->id, 'is_active' => false]);

        $activeRooms = MeetingRoom::active()->get();

        $this->assertCount(4, $activeRooms); // 3 + the one created in setUp
    }
}