<?php

namespace Database\Factories;

use App\Models\Meeting;
use App\Models\ParkingRecord;
use App\Models\ParkingSpot;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ParkingRecordFactory extends Factory
{
    protected $model = ParkingRecord::class;

    public function definition(): array
    {
        $vehicleType = fake()->randomElement(['car', 'suv', 'truck', 'van', 'motorcycle']);
        $vehicleMakes = ['Toyota', 'Honda', 'Ford', 'Chevrolet', 'BMW', 'Mercedes', 'Tesla', 'Audi', 'Volkswagen', 'Hyundai'];
        $vehicleColors = ['White', 'Black', 'Silver', 'Gray', 'Red', 'Blue', 'Green', 'Yellow'];

        return [
            'uuid' => Str::uuid(),
            'tenant_id' => Tenant::factory(),
            'parking_spot_id' => ParkingSpot::factory(),
            'vehicle_type' => $vehicleType,
            'vehicle_type' => fake()->randomElement([User::class, Visitor::class]),
            'vehicle_id' => fake()->randomNumber(),
            'license_plate' => strtoupper(Str::random(3) . fake()->numberBetween(100, 999)),
            'vehicle_make' => fake()->randomElement($vehicleMakes),
            'vehicle_model' => fake()->word(),
            'vehicle_color' => fake()->randomElement($vehicleColors),
            'entry_at' => now()->subHours(fake()->numberBetween(1, 48)),
            'exit_at' => null,
            'checked_in_by' => User::factory(),
            'checked_out_by' => null,
            'fee' => 0,
            'is_paid' => false,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'exit_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'exit_at' => now()->subMinutes(fake()->numberBetween(30, 240)),
            'checked_out_by' => User::factory(),
            'fee' => fake()->randomFloat(2, 5, 50),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => true,
            'exit_at' => now()->subMinutes(fake()->numberBetween(30, 240)),
            'checked_out_by' => User::factory(),
            'fee' => fake()->randomFloat(2, 5, 50),
        ]);
    }

    public function forVisitor(VisitorVisit $visit): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $visit->tenant_id,
            'vehicle_type' => Visitor::class,
            'vehicle_id' => $visit->visitor_id,
        ]);
    }

    public function forMeeting(Meeting $meeting): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $meeting->tenant_id,
            'notes' => 'Meeting: ' . $meeting->title,
        ]);
    }
}