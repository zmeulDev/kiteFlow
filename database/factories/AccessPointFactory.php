<?php

namespace Database\Factories;

use App\Models\AccessPoint;
use App\Models\Building;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AccessPointFactory extends Factory
{
    protected $model = AccessPoint::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'building_id' => Building::factory(),
            'name' => fake()->randomElement(['Main Entrance', 'Side Door', 'Reception', 'Parking Gate', 'Back Entrance']),
            'code' => strtoupper(Str::random(4)),
            'type' => fake()->randomElement(['door', 'gate', 'turnstile', 'kiosk']),
            'direction' => fake()->randomElement(['entry', 'exit', 'both']),
            'is_kiosk_mode' => false,
            'is_active' => true,
        ];
    }

    public function kiosk(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'kiosk',
            'is_kiosk_mode' => true,
            'name' => 'Visitor Kiosk',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}