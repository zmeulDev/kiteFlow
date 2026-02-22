<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\ParkingSpot;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ParkingSpotFactory extends Factory
{
    protected $model = ParkingSpot::class;

    public function definition(): array
    {
        $spotTypes = ['standard', 'compact', 'large', 'ev', 'disabled', 'vip', 'motorcycle'];
        $spotType = fake()->randomElement($spotTypes);
        $zones = ['A', 'B', 'C', 'D', 'E', 'F'];

        return [
            'uuid' => Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'building_id' => Building::factory(),
            'number' => fake()->randomElement($zones) . fake()->numberBetween(10, 99),
            'zone' => fake()->randomElement($zones),
            'type' => $spotType,
            'status' => 'available',
            'is_active' => true,
        ];
    }

    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'available',
        ]);
    }

    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'occupied',
        ]);
    }

    public function reserved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'reserved',
        ]);
    }

    public function ev(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'ev',
            'number' => 'EV-' . fake()->numberBetween(1, 20),
        ]);
    }

    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'disabled',
            'number' => 'D-' . fake()->numberBetween(1, 10),
        ]);
    }

    public function vip(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'vip',
            'number' => 'VIP-' . fake()->numberBetween(1, 5),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}