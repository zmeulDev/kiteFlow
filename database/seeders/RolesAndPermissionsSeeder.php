<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $permissions = [
            'manage-tenants',
            'manage-users',
            'manage-visitors',
            'check-in-visitors',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // create roles and assign created permissions

        $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::all());

        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $role->givePermissionTo(['manage-users', 'manage-visitors', 'check-in-visitors']);

        $role = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $role->givePermissionTo(['check-in-visitors', 'manage-visitors']);

        $role = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        // employees might have basic permissions or none by default
    }
}
