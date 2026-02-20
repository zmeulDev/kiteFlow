<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'manage tenants',
            'view tenants',
            'manage users',
            'view users',
            'manage buildings',
            'view buildings',
            'manage meeting_rooms',
            'view meeting_rooms',
            'manage visits',
            'view visits',
            'manage visitors',
            'view visitors',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // create roles and assign created permissions
        
        // Super Admin: Has all permissions everywhere
        $role = Role::firstOrCreate(['name' => 'super_admin'])
            ->givePermissionTo(Permission::all());

        // Admin: Similar to super admin but maybe restricted in some global actions? For now all.
        $role = Role::firstOrCreate(['name' => 'admin'])
            ->givePermissionTo(Permission::all());

        // Tenant Admin: Manages their own tenant resources
        $role = Role::firstOrCreate(['name' => 'tenant_admin'])
            ->givePermissionTo([
                'manage users', 'view users',
                'manage buildings', 'view buildings',
                'manage meeting_rooms', 'view meeting_rooms',
                'manage visits', 'view visits',
                'manage visitors', 'view visitors',
                'manage settings',
            ]);

        // Standard User: Basic access within a tenant
        $role = Role::firstOrCreate(['name' => 'user'])
            ->givePermissionTo([
                'view buildings',
                'view meeting_rooms',
                'manage visits', 'view visits',
                'manage visitors', 'view visitors',
            ]);
    }
}
