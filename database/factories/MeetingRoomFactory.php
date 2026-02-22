<?php

namespace Database\Factories;

use App\Models\MeetingRoom;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MeetingRoomFactory extends Factory
{
    protected $model = MeetingRoom::class;

    public function definition(): array
    {
        $name = fake()->randomElement(['Conference Room', 'Board Room', 'Meeting Room', 'Huddle Space', 'Executive Suite']) . ' ' . fake()->numberBetween(1, 10);

        return [
            'uuid' => Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'name' => $name,
            'code' => strtoupper(Str::random(6)),
            'location' => 'Floor ' . fake()->numberBetween(1, 10),
            'capacity' => fake()->numberBetween(4, 50),
            'description' => fake()->sentence(),
            'amenities' => fake()->randomElements(['projector', 'whiteboard', 'video_conference', 'tv_screen', 'wifi', 'phone', 'catering'], 3),
            'image' => null,
            'is_active' => true,
            'settings' => [],
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}