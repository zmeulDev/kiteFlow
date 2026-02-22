<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        // Create a main active tenant
        Tenant::factory()->create([
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'domain' => 'acme.kiteflow.test',
            'email' => 'admin@acme.com',
            'status' => 'active',
        ]);

        // Create additional active tenants
        Tenant::factory()->count(3)->active()->create();

        // Create a tenant on trial
        Tenant::factory()->onTrial()->create([
            'name' => 'TechStart Inc.',
            'slug' => 'techstart-inc',
            'domain' => 'techstart.kiteflow.test',
        ]);

        // Create a suspended tenant
        Tenant::factory()->suspended()->create([
            'name' => 'Suspended Company',
            'slug' => 'suspended-co',
            'domain' => 'suspended.kiteflow.test',
        ]);

        // Create an inactive tenant
        Tenant::factory()->inactive()->create([
            'name' => 'Inactive Company',
            'slug' => 'inactive-co',
            'domain' => 'inactive.kiteflow.test',
        ]);

        // Create parent-child tenant hierarchy
        $parentTenant = Tenant::factory()->create([
            'name' => 'Global Holdings Ltd',
            'slug' => 'global-holdings',
            'domain' => 'global-holdings.kiteflow.test',
        ]);

        Tenant::factory()->count(2)->withParent($parentTenant)->create();

        $this->command->info('Tenants seeded successfully.');
    }
}