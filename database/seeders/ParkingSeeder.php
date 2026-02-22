<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\ParkingRecord;
use App\Models\ParkingSpot;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ParkingSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::active()->get();

        foreach ($tenants as $tenant) {
            $buildings = $tenant->buildings;

            foreach ($buildings as $building) {
                // Create different types of parking spots
                $spotCounts = [
                    'standard' => 20,
                    'compact' => 10,
                    'large' => 5,
                    'ev' => 5,
                    'disabled' => 3,
                    'vip' => 2,
                ];

                foreach ($spotCounts as $type => $count) {
                    $spots = ParkingSpot::factory()->count($count)->create([
                        'tenant_id' => $tenant->id,
                        'building_id' => $building->id,
                        'type' => $type,
                    ]);

                    // Set some spots as occupied
                    $occupiedCount = fake()->numberBetween(1, (int) ($count * 0.4));
                    $spots->take($occupiedCount)->each(function ($spot) {
                        $spot->update(['status' => 'occupied']);
                    });

                    // Set some spots as reserved
                    $reservedCount = fake()->numberBetween(1, (int) ($count * 0.2));
                    $spots->skip($occupiedCount)->take($reservedCount)->each(function ($spot) {
                        $spot->update(['status' => 'reserved']);
                    });
                }

                // Create inactive spots
                ParkingSpot::factory()->inactive()->count(2)->create([
                    'tenant_id' => $tenant->id,
                    'building_id' => $building->id,
                ]);
            }

            // Create parking records
            $parkingSpots = ParkingSpot::where('tenant_id', $tenant->id)->get();
            if ($parkingSpots->isEmpty()) continue;

            // Get users who belong to this tenant
            $users = User::whereHas('tenants', fn ($q) => $q->where('tenant_id', $tenant->id))->get();

            // Create active parking records
            $activeCount = fake()->numberBetween(5, 10);
            for ($i = 0; $i < $activeCount; $i++) {
                $spot = fake()->randomElement($parkingSpots);
                $user = fake()->randomElement($users);
                ParkingRecord::factory()->active()->create([
                    'tenant_id' => $tenant->id,
                    'parking_spot_id' => $spot->id,
                    'checked_in_by' => $user ? $user->id : null,
                ]);
                $spot->update(['status' => 'occupied']);
            }

            // Create completed parking records
            $completedCount = fake()->numberBetween(15, 30);
            for ($i = 0; $i < $completedCount; $i++) {
                $spot = fake()->randomElement($parkingSpots);
                $user = fake()->randomElement($users);
                ParkingRecord::factory()->completed()->create([
                    'tenant_id' => $tenant->id,
                    'parking_spot_id' => $spot->id,
                    'checked_in_by' => $user ? $user->id : null,
                    'checked_out_by' => $user ? $user->id : null,
                ]);
            }

            // Create paid parking records
            $paidCount = fake()->numberBetween(10, 20);
            for ($i = 0; $i < $paidCount; $i++) {
                $spot = fake()->randomElement($parkingSpots);
                $user = fake()->randomElement($users);
                ParkingRecord::factory()->paid()->create([
                    'tenant_id' => $tenant->id,
                    'parking_spot_id' => $spot->id,
                    'checked_in_by' => $user ? $user->id : null,
                    'checked_out_by' => $user ? $user->id : null,
                ]);
            }
        }

        $this->command->info('Parking spots and records seeded successfully.');
    }
}