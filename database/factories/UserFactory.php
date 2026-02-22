<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'timezone' => 'UTC',
            'locale' => 'en',
            'phone' => fake()->phoneNumber(),
            'department' => fake()->randomElement(['Engineering', 'Sales', 'Marketing', 'HR', 'Operations']),
            'job_title' => fake()->jobTitle(),
            'is_active' => true,
            'last_login_at' => now()->subHours(fake()->numberBetween(1, 24)),
            'preferences' => [
                'notifications' => [
                    'email' => true,
                    'sms' => false,
                ],
            ],
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function superAdmin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('super-admin');
        });
    }

    public function admin(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('admin');
        });
    }

    public function receptionist(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('receptionist');
        });
    }

    public function user(): static
    {
        return $this->afterCreating(function ($user) {
            $user->assignRole('user');
        });
    }
}
