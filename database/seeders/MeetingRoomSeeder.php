<?php

namespace Database\Seeders;

use App\Models\MeetingRoom;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class MeetingRoomSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::active()->get();

        foreach ($tenants as $tenant) {
            $roomCount = $tenant->slug === 'acme-corp' ? 8 : fake()->numberBetween(3, 6);

            // Create various types of meeting rooms
            for ($i = 1; $i <= $roomCount; $i++) {
                $amenities = match (true) {
                    $i <= 2 => ['projector', 'whiteboard', 'video_conference', 'wifi', 'phone'],
                    $i <= 4 => ['whiteboard', 'tv_screen', 'wifi'],
                    $i <= 6 => ['projector', 'wifi', 'phone'],
                    default => ['wifi'],
                };

                $capacity = match (true) {
                    $i <= 2 => fake()->numberBetween(20, 50),  // Large conference rooms
                    $i <= 4 => fake()->numberBetween(10, 20),  // Medium rooms
                    default => fake()->numberBetween(4, 10),    // Small/huddle rooms
                };

                $roomTypes = ['Conference Room', 'Board Room', 'Meeting Room', 'Huddle Space', 'Executive Suite'];
                $roomType = match (true) {
                    $i <= 2 => 'Conference Room',
                    $i === 3 => 'Board Room',
                    $i <= 6 => 'Meeting Room',
                    default => 'Huddle Space',
                };

                MeetingRoom::factory()->create([
                    'tenant_id' => $tenant->id,
                    'name' => "{$roomType} {$i}",
                    'code' => 'MR-' . strtoupper(substr($tenant->slug, 0, 3)) . "-{$i}",
                    'location' => 'Floor ' . fake()->numberBetween(1, 10),
                    'capacity' => $capacity,
                    'amenities' => $amenities,
                    'is_active' => true,
                ]);
            }

            // Create some inactive meeting rooms
            if ($tenant->slug === 'acme-corp') {
                MeetingRoom::factory()->inactive()->count(2)->create([
                    'tenant_id' => $tenant->id,
                ]);
            }
        }

        $this->command->info('Meeting rooms seeded successfully.');
    }
}