<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MeetingFactory extends Factory
{
    protected $model = Meeting::class;

    public function definition(): array
    {
        $startAt = now()->addHours(fake()->numberBetween(1, 72));
        $endAt = (clone $startAt)->addHours(fake()->numberBetween(1, 3));

        return [
            'uuid' => Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'meeting_room_id' => MeetingRoom::factory(),
            'host_id' => User::factory(),
            'title' => fake()->randomElement(['Project Review', 'Team Sync', 'Client Meeting', 'Interview', 'Training Session', 'Strategy Meeting']),
            'description' => fake()->sentence(),
            'purpose' => fake()->randomElement(['discussion', 'presentation', 'interview', 'training', 'brainstorming']),
            'start_at' => $startAt,
            'end_at' => $endAt,
            'timezone' => 'UTC',
            'status' => 'scheduled',
            'meeting_type' => fake()->randomElement(['in_person', 'virtual', 'hybrid']),
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'cancellation_reason' => fake()->sentence(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'start_at' => now()->subHours(3),
            'end_at' => now()->subHours(1),
        ]);
    }

    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
            'start_at' => now()->subMinutes(30),
            'end_at' => now()->addHours(1),
        ]);
    }

    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_recurring' => true,
            'recurrence_rule' => [
                'frequency' => 'weekly',
                'interval' => 1,
                'days' => ['monday', 'wednesday', 'friday'],
            ],
        ]);
    }
}