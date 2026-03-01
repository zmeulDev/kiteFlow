<?php

namespace Database\Factories;

use App\Models\Entrance;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Visit>
 */
class VisitFactory extends Factory
{
    protected $model = Visit::class;

    public function definition(): array
    {
        return [
            'visitor_id' => Visitor::factory(),
            'entrance_id' => Entrance::factory(),
            'space_id' => null,
            'host_id' => null,
            'host_name' => fake()->name(),
            'host_email' => fake()->safeEmail(),
            'purpose' => fake()->sentence(),
            'status' => 'pending',
            'qr_code' => Str::random(32),
            'check_in_code' => Str::upper(Str::random(6)),
            'check_in_at' => null,
            'check_out_at' => null,
            'scheduled_at' => fake()->dateTimeBetween('-1 day', '+1 day'),
            'gdpr_consent_at' => null,
            'nda_consent_at' => null,
            'signature' => null,
            'photo_path' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function checkedIn(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'checked_in',
            'check_in_at' => now(),
        ]);
    }

    public function checkedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'checked_out',
            'check_in_at' => now()->subHours(2),
            'check_out_at' => now(),
        ]);
    }

    public function withHost(): static
    {
        return $this->state(fn (array $attributes) => [
            'host_id' => User::factory(),
        ]);
    }
}
