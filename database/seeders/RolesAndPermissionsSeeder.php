<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Tenant;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all possible granular permissions
        $permissions = [
            'manage-tenants',
            'manage-users',
            'manage-roles',
            'manage-billing',
            'manage-locations',
            'manage-rooms',
            'manage-visitors',
            'view-reports',
            'view-dashboard',
            'kiosk-access',
            'manage-settings'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin', 'tenant_id' => null]);
        $tenantAdminRole = Role::firstOrCreate(['name' => 'TenantAdmin', 'tenant_id' => null]);
        $subTenantAdminRole = Role::firstOrCreate(['name' => 'SubTenantAdmin', 'tenant_id' => null]);
        $frontDeskRole = Role::firstOrCreate(['name' => 'FrontDesk', 'tenant_id' => null]);
        $standardUserRole = Role::firstOrCreate(['name' => 'StandardUser', 'tenant_id' => null]);

        // 3. Attach Permission Matrices to Roles
        
        // SuperAdmin: Owns the app, has access everywhere
        $superAdminRole->syncPermissions(Permission::all());

        // TenantAdmin: Owns business, can manage their business scope
        $tenantAdminRole->syncPermissions([
            'view-dashboard',
            'manage-users',
            'manage-roles',
            'manage-locations',
            'manage-rooms',
            'manage-visitors',
            'view-reports',
            'kiosk-access',
            'manage-settings'
        ]);

        // SubTenantAdmin: Can make meeting reservations, view/create visitors
        $subTenantAdminRole->syncPermissions([
            'view-dashboard',
            'manage-visitors',
            'manage-rooms'
        ]);

        // FrontDesk: Create, view, edit rooms, visitors, visits, buildings
        $frontDeskRole->syncPermissions([
            'view-dashboard',
            'manage-locations',
            'manage-rooms',
            'manage-visitors',
            'kiosk-access'
        ]);

        // StandardUser: Can view/edit his visit/reservation
        $standardUserRole->syncPermissions([
            'view-dashboard',
            'manage-visitors' // Scoped by application logic to their own models
        ]);

        // 4. Provision Initial Super Admin Environment
        $systemTenant = Tenant::firstOrCreate(
            ['domain' => 'system'],
            ['name' => 'System Administration', 'status' => 'Active']
        );

        $superAdmin = User::firstOrCreate([
            'email' => 'super@visiflow.test'
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('password'),
            'tenant_id' => $systemTenant->id,
            'is_active' => true,
        ]);

        setPermissionsTeamId($systemTenant->id);

        if (!$superAdmin->hasRole('SuperAdmin')) {
            $superAdmin->assignRole($superAdminRole);
        }
    }
}
