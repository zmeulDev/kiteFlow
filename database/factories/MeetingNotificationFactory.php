<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingNotificationFactory extends Factory
{
    protected $model = MeetingNotification::class;

    public function definition(): array
    {
        $types = ['created', 'updated', 'cancelled', 'reminder', 'starting_soon', 'rescheduled'];
        $channels = ['email', 'sms', 'push', 'in_app'];
        $statuses = ['pending', 'sent', 'failed', 'cancelled'];

        return [
            'meeting_id' => Meeting::factory(),
            'type' => fake()->randomElement($types),
            'channel' => fake()->randomElement($channels),
            'recipients' => [
                ['type' => 'user', 'id' => fake()->numberBetween(1, 100), 'email' => fake()->email()],
                ['type' => 'user', 'id' => fake()->numberBetween(1, 100), 'email' => fake()->email()],
            ],
            'sent_at' => fake()->boolean(50) ? now()->subMinutes(fake()->numberBetween(5, 60)) : null,
            'status' => fake()->randomElement($statuses),
            'error' => null,
        ];
    }

    public function created(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'created',
            'status' => 'sent',
            'sent_at' => now()->subMinutes(fake()->numberBetween(1, 10)),
        ]);
    }

    public function reminder(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'reminder',
            'channel' => 'email',
            'status' => 'sent',
            'sent_at' => now()->subMinutes(fake()->numberBetween(5, 30)),
        ]);
    }

    public function startingSoon(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'starting_soon',
            'channel' => 'push',
            'status' => 'sent',
            'sent_at' => now()->subMinutes(fake()->numberBetween(1, 5)),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'cancelled',
            'status' => 'sent',
            'sent_at' => now()->subMinutes(fake()->numberBetween(1, 10)),
        ]);
    }

    public function rescheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'rescheduled',
            'status' => 'sent',
            'sent_at' => now()->subMinutes(fake()->numberBetween(1, 10)),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'sent_at' => now()->subMinutes(fake()->numberBetween(1, 10)),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
            'sent_at' => now()->subMinutes(fake()->numberBetween(1, 5)),
            'error' => fake()->sentence(),
        ]);
    }

    public function email(): static
    {
        return $this->state(fn (array $attributes) => [
            'channel' => 'email',
        ]);
    }

    public function sms(): static
    {
        return $this->state(fn (array $attributes) => [
            'channel' => 'sms',
        ]);
    }

    public function push(): static
    {
        return $this->state(fn (array $attributes) => [
            'channel' => 'push',
        ]);
    }
}