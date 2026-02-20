<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MeetingRoom>
 */
class MeetingRoomFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tenant_id' => \App\Models\Tenant::factory(),
            'building_id' => \App\Models\Building::factory(),
            'name' => 'Meeting Room ' . fake()->numberBetween(1, 10),
            'capacity' => fake()->numberBetween(2, 20),
            'amenities' => json_encode(['projector', 'whiteboard', 'video_conferencing']),
            'floor' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
