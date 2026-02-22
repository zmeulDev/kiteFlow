<?php

namespace Database\Seeders;

use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class MeetingSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::active()->get();

        foreach ($tenants as $tenant) {
            $meetingRooms = MeetingRoom::where('tenant_id', $tenant->id)->active()->get();
            // Get users who belong to this tenant
            $hosts = User::whereHas('tenants', fn ($q) => $q->where('tenant_id', $tenant->id))->get();

            if ($meetingRooms->isEmpty() || $hosts->isEmpty()) {
                continue;
            }

            // Create scheduled meetings
            Meeting::factory()->count(10)->create([
                'tenant_id' => $tenant->id,
                'meeting_room_id' => fake()->randomElement($meetingRooms)->id,
                'host_id' => fake()->randomElement($hosts)->id,
            ]);

            // Create ongoing meetings
            Meeting::factory()->ongoing()->count(2)->create([
                'tenant_id' => $tenant->id,
                'meeting_room_id' => fake()->randomElement($meetingRooms)->id,
                'host_id' => fake()->randomElement($hosts)->id,
            ]);

            // Create completed meetings
            Meeting::factory()->completed()->count(15)->create([
                'tenant_id' => $tenant->id,
                'meeting_room_id' => fake()->randomElement($meetingRooms)->id,
                'host_id' => fake()->randomElement($hosts)->id,
            ]);

            // Create cancelled meetings
            Meeting::factory()->cancelled()->count(3)->create([
                'tenant_id' => $tenant->id,
                'meeting_room_id' => fake()->randomElement($meetingRooms)->id,
                'host_id' => fake()->randomElement($hosts)->id,
            ]);

            // Create recurring meetings
            Meeting::factory()->recurring()->count(5)->create([
                'tenant_id' => $tenant->id,
                'meeting_room_id' => fake()->randomElement($meetingRooms)->id,
                'host_id' => fake()->randomElement($hosts)->id,
            ]);

            // Create virtual meetings
            Meeting::factory()->count(5)->create([
                'tenant_id' => $tenant->id,
                'meeting_room_id' => fake()->randomElement($meetingRooms)->id,
                'host_id' => fake()->randomElement($hosts)->id,
                'meeting_type' => 'virtual',
            ]);

            // Create hybrid meetings
            Meeting::factory()->count(5)->create([
                'tenant_id' => $tenant->id,
                'meeting_room_id' => fake()->randomElement($meetingRooms)->id,
                'host_id' => fake()->randomElement($hosts)->id,
                'meeting_type' => 'hybrid',
            ]);
        }

        $this->command->info('Meetings seeded successfully.');
    }
}