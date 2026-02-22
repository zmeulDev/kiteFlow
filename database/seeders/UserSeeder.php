<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Get the main tenant for default users
        $mainTenant = Tenant::where('slug', 'acme-corp')->first();
        $otherTenants = Tenant::where('id', '!=', $mainTenant->id)->get();

        // Create super admin (global user, attached to main tenant as owner)
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@kiteflow.com',
        ]);
        $superAdmin->assignRole('super-admin');
        $superAdmin->tenants()->attach($mainTenant->id, ['is_owner' => true]);

        // Create admin for main tenant
        $admin = User::factory()->create([
            'name' => 'John Administrator',
            'email' => 'admin@acme.com',
        ]);
        $admin->assignRole('admin');
        $admin->tenants()->attach($mainTenant->id, ['is_owner' => true]);

        // Create receptionist for main tenant
        $receptionist = User::factory()->create([
            'name' => 'Sarah Reception',
            'email' => 'reception@acme.com',
        ]);
        $receptionist->assignRole('receptionist');
        $receptionist->tenants()->attach($mainTenant->id, ['is_owner' => false]);

        // Create regular users for main tenant
        User::factory()->count(10)->create()->each(function ($user) use ($mainTenant) {
            $user->assignRole('user');
            $user->tenants()->attach($mainTenant->id, ['is_owner' => false]);
        });

        // Create a few inactive users
        User::factory()->inactive()->count(2)->create()->each(function ($user) use ($mainTenant) {
            $user->assignRole('user');
            $user->tenants()->attach($mainTenant->id, ['is_owner' => false]);
        });

        // Create a few unverified users
        User::factory()->unverified()->count(2)->create()->each(function ($user) use ($mainTenant) {
            $user->assignRole('user');
            $user->tenants()->attach($mainTenant->id, ['is_owner' => false]);
        });

        // Create users for other tenants
        foreach ($otherTenants as $tenant) {
            // Admin for each tenant
            $tenantAdmin = User::factory()->create([
                'name' => "Admin - {$tenant->name}",
                'email' => "admin@{$tenant->slug}.test",
            ]);
            $tenantAdmin->assignRole('admin');
            $tenantAdmin->tenants()->attach($tenant->id, ['is_owner' => true]);

            // Receptionist for each tenant
            $tenantReceptionist = User::factory()->create([
                'name' => "Receptionist - {$tenant->name}",
                'email' => "reception@{$tenant->slug}.test",
            ]);
            $tenantReceptionist->assignRole('receptionist');
            $tenantReceptionist->tenants()->attach($tenant->id, ['is_owner' => false]);

            // Regular users for each tenant
            User::factory()->count(5)->create()->each(function ($user) use ($tenant) {
                $user->assignRole('user');
                $user->tenants()->attach($tenant->id, ['is_owner' => false]);
            });
        }

        $this->command->info('Users seeded successfully.');
    }
}