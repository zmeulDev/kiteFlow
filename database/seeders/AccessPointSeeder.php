<?php

namespace Database\Seeders;

use App\Models\AccessPoint;
use App\Models\Building;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class AccessPointSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = Building::all();

        foreach ($buildings as $building) {
            // Main entrance
            AccessPoint::factory()->create([
                'tenant_id' => $building->tenant_id,
                'building_id' => $building->id,
                'name' => 'Main Entrance',
                'code' => 'ME-' . strtoupper(substr($building->code, -3)),
                'type' => 'door',
                'direction' => 'both',
                'is_kiosk_mode' => false,
                'is_active' => true,
            ]);

            // Side entrance
            AccessPoint::factory()->create([
                'tenant_id' => $building->tenant_id,
                'building_id' => $building->id,
                'name' => 'Side Entrance',
                'code' => 'SE-' . strtoupper(substr($building->code, -3)),
                'type' => 'door',
                'direction' => 'both',
                'is_kiosk_mode' => false,
                'is_active' => true,
            ]);

            // Reception desk
            AccessPoint::factory()->create([
                'tenant_id' => $building->tenant_id,
                'building_id' => $building->id,
                'name' => 'Reception',
                'code' => 'RCP-' . strtoupper(substr($building->code, -3)),
                'type' => 'kiosk',
                'direction' => 'entry',
                'is_kiosk_mode' => true,
                'is_active' => true,
            ]);

            // Parking gate
            AccessPoint::factory()->create([
                'tenant_id' => $building->tenant_id,
                'building_id' => $building->id,
                'name' => 'Parking Gate',
                'code' => 'PK-' . strtoupper(substr($building->code, -3)),
                'type' => 'gate',
                'direction' => 'both',
                'is_kiosk_mode' => false,
                'is_active' => true,
            ]);

            // Additional access points for main tenant
            if ($building->tenant->slug === 'acme-corp') {
                AccessPoint::factory()->count(5)->create([
                    'tenant_id' => $building->tenant_id,
                    'building_id' => $building->id,
                ]);

                // Create a kiosk
                AccessPoint::factory()->kiosk()->create([
                    'tenant_id' => $building->tenant_id,
                    'building_id' => $building->id,
                    'name' => 'Visitor Check-in Kiosk',
                    'code' => 'KSK-01',
                ]);
            } else {
                // Create 2-3 additional access points for other tenants
                AccessPoint::factory()->count(fake()->numberBetween(2, 3))->create([
                    'tenant_id' => $building->tenant_id,
                    'building_id' => $building->id,
                ]);
            }

            // Create some inactive access points
            AccessPoint::factory()->inactive()->count(1)->create([
                'tenant_id' => $building->tenant_id,
                'building_id' => $building->id,
            ]);
        }

        $this->command->info('Access points seeded successfully.');
    }
}