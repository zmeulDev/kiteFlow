<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Clear cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Tenant permissions
            'view tenants', 'create tenants', 'update tenants', 'delete tenants',
            'manage tenants',
            
            // User permissions
            'view users', 'create users', 'update users', 'delete users',
            'manage users', 'impersonate users',
            
            // Visitor permissions
            'view visitors', 'create visitors', 'update visitors', 'delete visitors',
            'check-in visitors', 'check-out visitors', 'blacklist visitors',
            
            // Meeting permissions
            'view meetings', 'create meetings', 'update meetings', 'delete meetings',
            'cancel meetings', 'manage meetings',
            
            // Meeting Room permissions
            'view meeting-rooms', 'create meeting-rooms', 'update meeting-rooms', 'delete meeting-rooms',
            
            // Building permissions
            'view buildings', 'create buildings', 'update buildings', 'delete buildings',
            
            // Access permissions
            'view access-logs', 'manage access-points',
            
            // Kiosk permissions
            'use kiosk', 'manage kiosks',
            
            // Reports
            'view reports', 'export reports',
            
            // Settings
            'view settings', 'update settings',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'sanctum']);
        }

        // Create roles and assign permissions
        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::where('guard_name', 'web')->get());
        
        $superAdminApi = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'sanctum']);
        $superAdminApi->givePermissionTo(Permission::where('guard_name', 'sanctum')->get());

        // Admin - tenant administrator
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'view tenants', 'update tenants',
            'view users', 'create users', 'update users', 'delete users', 'manage users',
            'view visitors', 'create visitors', 'update visitors', 'delete visitors',
            'check-in visitors', 'check-out visitors', 'blacklist visitors',
            'view meetings', 'create meetings', 'update meetings', 'delete meetings', 'cancel meetings',
            'view meeting-rooms', 'create meeting-rooms', 'update meeting-rooms', 'delete meeting-rooms',
            'view buildings', 'create buildings', 'update buildings', 'delete buildings',
            'view access-logs', 'manage access-points',
            'use kiosk', 'manage kiosks',
            'view reports', 'export reports',
            'view settings', 'update settings',
        ]);
        
        $adminApi = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'sanctum']);
        $adminApi->givePermissionTo($admin->permissions->pluck('name')->toArray());

        // User - regular tenant user
        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->givePermissionTo([
            'view visitors', 'create visitors', 'update visitors',
            'check-in visitors', 'check-out visitors',
            'view meetings', 'create meetings', 'update meetings',
            'view meeting-rooms',
            'view buildings',
            'use kiosk',
        ]);
        
        $userApi = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'sanctum']);
        $userApi->givePermissionTo($user->permissions->pluck('name')->toArray());

        // Reception - front desk staff
        $receptionist = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'web']);
        $receptionist->givePermissionTo([
            'view visitors', 'create visitors', 'update visitors',
            'check-in visitors', 'check-out visitors',
            'view meetings', 'create meetings', 'update meetings',
            'view meeting-rooms',
            'view buildings',
            'use kiosk', 'manage kiosks',
        ]);
        
        $receptionistApi = Role::firstOrCreate(['name' => 'receptionist', 'guard_name' => 'sanctum']);
        $receptionistApi->givePermissionTo($receptionist->permissions->pluck('name')->toArray());

        $this->command->info('Roles and permissions seeded successfully.');
    }
}