<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VisitorVisitFactory extends Factory
{
    protected $model = VisitorVisit::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'visitor_id' => Visitor::factory(),
            'host_id' => User::factory(),
            'purpose' => fake()->randomElement(['meeting', 'interview', 'delivery', 'maintenance', 'consultation', 'other']),
            'check_in_method' => fake()->randomElement(['kiosk_code', 'kiosk_manual', 'reception']),
            'check_in_at' => now(),
            'check_out_at' => null,
            'badge_number' => VisitorVisit::generateBadgeNumber(),
            'badge_type' => 'visitor',
            'status' => 'checked_in',
            'custom_fields' => [],
        ];
    }

    public function checkedOut(): static
    {
        return $this->state(fn (array $attributes) => [
            'check_out_at' => now()->addHours(2),
            'status' => 'checked_out',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
            'notes' => 'Visit cancelled',
        ]);
    }
}