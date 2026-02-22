<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Zone;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $buildings = Building::all();

        foreach ($buildings as $building) {
            $floorCount = $building->floors;

            // Create zones for each floor
            for ($floor = 1; $floor <= $floorCount; $floor++) {
                // Office zone
                Zone::factory()->create([
                    'tenant_id' => $building->tenant_id,
                    'building_id' => $building->id,
                    'name' => "Office - Floor {$floor}",
                    'code' => 'OF-' . $floor,
                    'type' => 'office',
                    'floor' => $floor,
                    'is_active' => true,
                ]);

                // Conference zone (for floors 1-5)
                if ($floor <= 5) {
                    Zone::factory()->create([
                        'tenant_id' => $building->tenant_id,
                        'building_id' => $building->id,
                        'name' => "Conference - Floor {$floor}",
                        'code' => 'CONF-' . $floor,
                        'type' => 'conference',
                        'floor' => $floor,
                        'is_active' => true,
                    ]);
                }

                // Lobby on ground floor
                if ($floor === 1) {
                    Zone::factory()->public()->create([
                        'tenant_id' => $building->tenant_id,
                        'building_id' => $building->id,
                        'name' => 'Main Lobby',
                        'code' => 'LOBBY',
                        'type' => 'lobby',
                        'floor' => 1,
                    ]);
                }

                // Secure zone for higher floors
                if ($floor > $floorCount - 3) {
                    Zone::factory()->secure()->create([
                        'tenant_id' => $building->tenant_id,
                        'building_id' => $building->id,
                        'name' => "Executive - Floor {$floor}",
                        'code' => 'EXEC-' . $floor,
                        'type' => 'secure',
                        'floor' => $floor,
                    ]);
                }

                // Restricted zone (server room, etc.)
                if ($floor === $floorCount) {
                    Zone::factory()->restricted()->create([
                        'tenant_id' => $building->tenant_id,
                        'building_id' => $building->id,
                        'name' => 'Server Room',
                        'code' => 'SRV-1',
                        'type' => 'restricted',
                        'floor' => $floor,
                    ]);
                }
            }

            // Create some inactive zones
            Zone::factory()->inactive()->count(2)->create([
                'tenant_id' => $building->tenant_id,
                'building_id' => $building->id,
            ]);
        }

        $this->command->info('Zones seeded successfully.');
    }
}