<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\MeetingRoom;
use App\Models\Visit;
use App\Models\Booking;
use Livewire\Livewire;
use App\Livewire\Dashboard\PreRegisterGuest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class MeetingRoomTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_book_a_meeting_room_when_pre_registering_a_guest()
    {
        $tenant = Tenant::create(['name' => 'Hub', 'slug' => 'hub', 'plan' => 'pro']);
        $user = User::create(['tenant_id' => $tenant->id, 'name' => 'Admin', 'email' => 'a@t.com', 'password' => 'pass']);
        $room = MeetingRoom::create(['tenant_id' => $tenant->id, 'name' => 'Boardroom', 'capacity' => 10]);

        $this->actingAs($user);

        Livewire::test(PreRegisterGuest::class)
            ->set('first_name', 'Invited')
            ->set('last_name', 'Guest')
            ->set('email', 'guest@example.com')
            ->set('purpose', 'Strategic Planning')
            ->set('expected_at', now()->addDay()->format('Y-m-d H:i'))
            ->set('meeting_room_id', $room->id)
            ->set('booking_duration', 90)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('visits', [
            'purpose' => 'Strategic Planning',
            'checked_in_at' => null,
        ]);

        $visit = Visit::latest()->first();

        $this->assertDatabaseHas('bookings', [
            'meeting_room_id' => $room->id,
            'visit_id' => $visit->id,
            'notes' => 'Strategic Planning',
        ]);
    }
}
