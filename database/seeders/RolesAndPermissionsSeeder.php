<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use App\Models\Tenant;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin', 'tenant_id' => null]);
        Role::firstOrCreate(['name' => 'TenantAdmin', 'tenant_id' => null]);
        Role::firstOrCreate(['name' => 'SubTenantAdmin', 'tenant_id' => null]);
        Role::firstOrCreate(['name' => 'FrontDesk', 'tenant_id' => null]);
        Role::firstOrCreate(['name' => 'StandardUser', 'tenant_id' => null]);

        $systemTenant = Tenant::firstOrCreate(
            ['domain' => 'system'],
            ['name' => 'System Administration']
        );

        $superAdmin = User::firstOrCreate([
            'email' => 'super@visiflow.test'
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
            'tenant_id' => $systemTenant->id,
        ]);

        setPermissionsTeamId($systemTenant->id);

        if (!$superAdmin->hasRole('SuperAdmin')) {
            $superAdmin->assignRole($superAdminRole);
        }
    }
}
