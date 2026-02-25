<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Entrance;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Entrance>
 */
class EntranceFactory extends Factory
{
    protected $model = Entrance::class;

    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'name' => 'Main Entrance',
            'kiosk_identifier' => Str::uuid()->toString(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
