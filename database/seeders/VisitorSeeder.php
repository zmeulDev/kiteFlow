<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\Visitor;
use Illuminate\Database\Seeder;

class VisitorSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::active()->get();

        foreach ($tenants as $tenant) {
            $visitorCount = $tenant->slug === 'acme-corp' ? 30 : fake()->numberBetween(10, 20);

            // Create regular visitors
            Visitor::factory()->count($visitorCount)->create([
                'tenant_id' => $tenant->id,
            ]);

            // Create blacklisted visitors
            $blacklistedCount = fake()->numberBetween(2, 5);
            Visitor::factory()->blacklisted()->count($blacklistedCount)->create([
                'tenant_id' => $tenant->id,
            ]);

            // Create some visitors without email (for walk-ins)
            Visitor::factory()->count(5)->create([
                'tenant_id' => $tenant->id,
                'email' => null,
            ]);

            // Create some visitors with detailed notes
            Visitor::factory()->count(3)->create([
                'tenant_id' => $tenant->id,
                'notes' => 'VIP visitor - Provide premium service',
            ]);
        }

        $this->command->info('Visitors seeded successfully.');
    }
}