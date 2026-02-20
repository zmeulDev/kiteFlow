<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    public function definition(): array
    {
        return [
            'visitor_id' => \App\Models\Visitor::factory(),
            'tenant_id' => \App\Models\Tenant::factory(),
            'host_user_id' => \App\Models\User::factory(),
            'meeting_room_id' => \App\Models\MeetingRoom::factory(),
            'building_id' => \App\Models\Building::factory(),
            'visit_code' => strtoupper(Str::random(8)),
            'scheduled_start' => now()->addDays(fake()->numberBetween(1, 7)),
            'scheduled_end' => now()->addDays(fake()->numberBetween(1, 7))->addHours(fake()->numberBetween(1, 4)),
            'purpose' => fake()->randomElement(['Business Meeting', 'Interview', 'Client Visit', 'Delivery', 'Other']),
            'status' => fake()->randomElement(['pre_registered', 'checked_in', 'checked_out', 'cancelled', 'no_show']),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'checked_in',
            'checked_in_at' => now(),
        ]);
    }

    public function checkedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'checked_out',
            'checked_in_at' => now()->subHours(2),
            'checked_out_at' => now(),
        ]);
    }

    public function preRegistered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pre_registered',
        ]);
    }
}
