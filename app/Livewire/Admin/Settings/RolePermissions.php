<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class RolePermissions extends Component
{
    // The matrix of permissions assigned to roles.
    public array $permissions = [];

    // The defined roles in the system
    public array $roles = [
        'admin' => 'God Mode',
        'administrator' => 'Tenant',
        'tenant' => 'Sub-Tenant',
        'receptionist' => 'Receptionist',
        'viewer' => 'Viewer'
    ];

    // The available permissions to toggle
    public array $availablePermissions = [
        'manage_users' => 'Manage Users',
        'manage_companies' => 'Manage Companies',
        'manage_buildings' => 'Manage Buildings',
        'manage_visits' => 'Manage Visits',
        'manage_settings' => 'Manage Settings',
        'kiosk_access' => 'Kiosk Access',
        'download_reports' => 'Download Reports',
    ];

    public function mount(): void
    {
        abort_if(!in_array(auth()->user()->role, ['admin', 'administrator']), 403, 'Unauthorized action.');

        // Load the stored JSON array from settings.
        $storedPermissions = Setting::get('rbac_permissions', null);

        if ($storedPermissions === null) {
            // Default sensibly if non-existent
            $this->permissions = [
                'admin' => array_keys($this->availablePermissions),
                'administrator' => ['manage_users', 'manage_companies', 'manage_buildings', 'manage_visits'],
                'tenant' => ['manage_visits', 'download_reports'],
                'receptionist' => ['manage_users', 'manage_visits', 'kiosk_access'],
                'viewer' => ['manage_visits', 'download_reports'], // Read-only logic can be applied later via this permission
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
            $storedPermissions = Setting::get('rbac_permissions', []);
            $this->permissions['admin'] = $storedPermissions['admin'] ?? array_keys($this->availablePermissions);
            $this->permissions['administrator'] = $storedPermissions['administrator'] ?? ['manage_users', 'manage_companies', 'manage_buildings', 'manage_visits'];
        }

        // Persist the array to the settings DB table
        Setting::set('rbac_permissions', $this->permissions);

        session()->flash('message', 'Role permissions have been saved successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.role-permissions');
    }
}
