<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Tenant;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ZoneFactory extends Factory
{
    protected $model = Zone::class;

    public function definition(): array
    {
        $zoneTypes = ['office', 'conference', 'lobby', 'laboratory', 'storage', 'secure', 'public', 'restricted'];
        $zoneType = fake()->randomElement($zoneTypes);

        return [
            'uuid' => Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'building_id' => Building::factory(),
            'name' => fake()->randomElement(['Floor ', 'Zone ', 'Wing ', 'Section ', 'Area ']) . fake()->numberBetween(1, 10),
            'code' => strtoupper(Str::random(3)),
            'type' => $zoneType,
            'floor' => fake()->numberBetween(1, 20),
            'description' => fake()->optional()->sentence(),
            'access_rules' => fake()->randomElements(['badge_required', 'escort_needed', 'appointment_only', 'time_restricted', 'visitor_banned'], fake()->numberBetween(0, 3)),
            'requires_authorization' => fake()->boolean(20),
            'is_active' => true,
        ];
    }

    public function secure(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'secure',
            'requires_authorization' => true,
            'access_rules' => ['badge_required', 'escort_needed'],
        ]);
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'public',
            'requires_authorization' => false,
            'access_rules' => [],
        ]);
    }

    public function restricted(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'restricted',
            'requires_authorization' => true,
            'access_rules' => ['badge_required', 'appointment_only', 'visitor_banned'],
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}