<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VisitorFactory extends Factory
{
    protected $model = Visitor::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'company' => fake()->company(),
            'id_type' => fake()->randomElement(['passport', 'driver_license', 'national_id']),
            'id_number' => strtoupper(Str::random(10)),
            'is_blacklisted' => false,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function blacklisted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_blacklisted' => true,
            'blacklist_reason' => fake()->sentence(),
        ]);
    }
}