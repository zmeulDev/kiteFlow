<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class RolePermissions extends Component
{
    // The matrix of permissions assigned to roles.
    public array $permissions = [];

    // The defined roles in the system
    public array $roles = [];

    // The available permissions grouped by category
    public array $permissionCategories = [
        'Operations' => [
            'manage_visits' => 'Manage Visits',
            'kiosk_access' => 'Kiosk Access',
            'download_reports' => 'Download Reports',
        ],
        'Management' => [
            'view_users' => 'View Users',
            'manage_users' => 'Manage Users',
            'view_companies' => 'View Companies',
            'manage_companies' => 'Manage Companies',
            'view_buildings' => 'View Buildings',
            'manage_buildings' => 'Manage Buildings',
        ],
        'System' => [
            'manage_settings' => 'Manage Settings',
        ],
    ];

    public function mount(): void
    {
        abort_if(!in_array(auth()->user()->role, ['admin', 'administrator']), 403, 'Unauthorized action.');

        // Load roles from User model
        $this->roles = User::getRoles();

        // Load the stored JSON array from settings.
        $storedPermissions = Setting::get('rbac_permissions', null);

        if ($storedPermissions === null) {
            // Extract all flattened keys
            $allPermKeys = collect($this->permissionCategories)->flatten(1)->keys()->toArray();

            // Default sensibly if non-existent
            $this->permissions = [
                'admin' => $allPermKeys, // Admin gets all permissions by default
                'administrator' => ['manage_users', 'view_users', 'manage_companies', 'view_companies', 'manage_buildings', 'view_buildings', 'manage_visits', 'manage_settings'],
                'tenant' => ['manage_users', 'view_users', 'view_buildings', 'manage_visits', 'download_reports'],
                'receptionist' => ['manage_users', 'view_users', 'manage_companies', 'view_companies', 'manage_buildings', 'view_buildings', 'manage_visits', 'kiosk_access'],
                'viewer' => ['manage_visits'], // Employee can only see/schedule their own visits
            ];
        } else {
            $this->permissions = $storedPermissions;
            // Ensure all roles exist in the array to prevent undefined array keys in the view
            foreach (array_keys($this->roles) as $role) {
                if (!isset($this->permissions[$role])) {
                    $this->permissions[$role] = [];
                }
            }
        }
    }

    public function save(): void
    {
        // For Tenants (administrator role), ensure they do not accidentally overwrite God Mode or their own permissions,
        // since those inputs are disabled on their view.
        if (auth()->user()->role === 'administrator') {
            $allPermKeys = collect($this->permissionCategories)->flatten(1)->keys()->toArray();
            $storedPermissions = Setting::get('rbac_permissions', []);
            $this->permissions['admin'] = $storedPermissions['admin'] ?? $allPermKeys;
            $this->permissions['administrator'] = $storedPermissions['administrator'] ?? ['manage_users', 'view_users', 'manage_companies', 'view_companies', 'manage_buildings', 'view_buildings', 'manage_visits', 'manage_settings'];
        }

        // Prevent removing God Mode's core permissions for 'admin' role
        $requiredAdminPermissions = ['manage_settings', 'manage_users', 'view_users', 'manage_companies', 'view_companies', 'manage_buildings', 'view_buildings', 'manage_visits'];
        $this->permissions['admin'] = array_unique(array_merge($this->permissions['admin'] ?? [], $requiredAdminPermissions));

        // Persist the array to the settings DB table
        Setting::set('rbac_permissions', $this->permissions);

        session()->flash('message', 'Role permissions have been saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.role-permissions');
    }
}
