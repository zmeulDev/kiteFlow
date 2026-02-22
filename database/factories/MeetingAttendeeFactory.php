<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\MeetingAttendee;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeetingAttendeeFactory extends Factory
{
    protected $model = MeetingAttendee::class;

    public function definition(): array
    {
        $statuses = ['pending', 'accepted', 'declined', 'tentative'];
        $types = ['required', 'optional'];

        return [
            'meeting_id' => Meeting::factory(),
            'attendee_type' => null, // Will be set by forUser or forVisitor
            'attendee_id' => null,   // Will be set by forUser or forVisitor
            'type' => fake()->randomElement($types),
            'status' => fake()->randomElement($statuses),
            'responded_at' => fake()->boolean(70) ? now()->subHours(fake()->numberBetween(1, 24)) : null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function forUser($user): static
    {
        return $this->state(fn (array $attributes) => [
            'attendee_type' => \App\Models\User::class,
            'attendee_id' => $user instanceof \App\Models\User ? $user->id : $user,
        ]);
    }

    public function forVisitor($visitor): static
    {
        return $this->state(fn (array $attributes) => [
            'attendee_type' => \App\Models\Visitor::class,
            'attendee_id' => $visitor instanceof \App\Models\Visitor ? $visitor->id : $visitor,
        ]);
    }

    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'required',
        ]);
    }

    public function optional(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'optional',
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'responded_at' => now()->subHours(fake()->numberBetween(1, 24)),
        ]);
    }

    public function declined(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'declined',
            'responded_at' => now()->subHours(fake()->numberBetween(1, 24)),
            'notes' => fake()->sentence(),
        ]);
    }

    public function tentative(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'tentative',
            'responded_at' => now()->subHours(fake()->numberBetween(1, 24)),
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'responded_at' => null,
        ]);
    }
}