<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'uuid' => Str::uuid(),
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(4)),
            'domain' => Str::slug($name) . '-' . Str::lower(Str::random(4)) . '.kiteflow.test',
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'timezone' => 'UTC',
            'locale' => 'en',
            'currency' => 'USD',
            'status' => 'active',
            'subscription_plan' => fake()->randomElement(['starter', 'professional', 'enterprise']),
            'billing_cycle' => fake()->randomElement(['monthly', 'yearly']),
            'monthly_price' => fake()->randomFloat(2, 29, 299),
            'yearly_price' => fake()->randomFloat(2, 290, 2990),
            'contract_start_date' => fake()->date(),
            'contract_end_date' => fake()->date(),
            'payment_status' => fake()->randomElement(['current', 'overdue', 'cancelled']),
            'address' => [
                'street' => fake()->streetAddress(),
                'city' => fake()->city(),
                'state' => fake()->state(),
                'postal_code' => fake()->postcode(),
                'country' => fake()->country(),
            ],
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function onTrial(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'trial',
            'trial_ends_at' => now()->addDays(14),
        ]);
    }

    public function withParent(Tenant $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
}