<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class BuildingSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::active()->get();

        foreach ($tenants as $tenant) {
            // Create main building for each tenant
            Building::factory()->create([
                'tenant_id' => $tenant->id,
                'name' => $tenant->name . ' - Main Building',
                'code' => strtoupper(substr($tenant->slug, 0, 3)) . '-MAIN',
                'floors' => 10,
            ]);

            // Create additional buildings for main tenant
            if ($tenant->slug === 'acme-corp') {
                Building::factory()->create([
                    'tenant_id' => $tenant->id,
                    'name' => $tenant->name . ' - Innovation Center',
                    'code' => strtoupper(substr($tenant->slug, 0, 3)) . '-INNO',
                    'floors' => 5,
                ]);

                Building::factory()->create([
                    'tenant_id' => $tenant->id,
                    'name' => $tenant->name . ' - Warehouse',
                    'code' => strtoupper(substr($tenant->slug, 0, 3)) . '-WH',
                    'floors' => 2,
                ]);

                // Create an inactive building
                Building::factory()->inactive()->create([
                    'tenant_id' => $tenant->id,
                    'name' => $tenant->name . ' - Old Building',
                    'code' => strtoupper(substr($tenant->slug, 0, 3)) . '-OLD',
                ]);
            } else {
                // Create 1-2 additional buildings for other tenants
                Building::factory()->count(fake()->numberBetween(0, 2))->create([
                    'tenant_id' => $tenant->id,
                ]);
            }
        }

        $this->command->info('Buildings seeded successfully.');
    }
}