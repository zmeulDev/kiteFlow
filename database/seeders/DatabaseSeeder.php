<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);
        
        // Super admin user creation
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@kiteflow.com',
            'role' => 'super_admin'
        ]);
        
        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            $superAdmin->assignRole('super_admin');
        }
    }
}
