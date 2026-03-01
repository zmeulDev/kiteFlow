<?php

namespace Database\Factories;

use App\Models\Building;
use App\Models\Space;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Space>
 */
class SpaceFactory extends Factory
{
    protected $model = Space::class;

    public function definition(): array
    {
        return [
            'building_id' => Building::factory(),
            'name' => fake()->word() . ' Space',
            'amenities' => ['WiFi', 'Projector', 'Whiteboard'],
            'is_active' => true,
        ];
    }
}
