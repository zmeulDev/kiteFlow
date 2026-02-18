<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleList extends Component
{
    public $name = '';
    public $guard_name = 'web';
    public $editingRoleId = null;
    
    public $showRoleModal = false;
    public $showPermissionModal = false;
    public $showDeleteModal = false;
    public $deleteId = null;
    public $deleteType = ''; // 'role' or 'permission'

    protected $rules = [
        'name' => 'required|min:3|unique:roles,name',
        'guard_name' => 'required',
    ];

    public function confirmDelete($id, $type)
    {
        $this->deleteId = $id;
        $this->deleteType = $type;
        $this->showDeleteModal = true;
    }

    public function deleteConfirmed()
    {
        if ($this->deleteType === 'role') {
            $this->deleteRole($this->deleteId);
        } elseif ($this->deleteType === 'permission') {
            $this->deletePermission($this->deleteId);
        }
        $this->showDeleteModal = false;
        $this->reset(['deleteId', 'deleteType']);
    }

    public function createRole()
    {
        $this->reset(['name', 'guard_name', 'editingRoleId']);
        $this->showRoleModal = true;
    }

    public function editRole($id)
    {
        $role = Role::findOrFail($id);
        $this->name = $role->name;
        $this->guard_name = $role->guard_name;
        $this->editingRoleId = $id;
        $this->showRoleModal = true;
    }

    public function saveRole()
    {
        $rules = $this->rules;
        if ($this->editingRoleId) {
            $rules['name'] = 'required|min:3|unique:roles,name,' . $this->editingRoleId;
        }
        
        $this->validate($rules);

        if ($this->editingRoleId) {
            $role = Role::findOrFail($this->editingRoleId);
            $role->update([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);
        } else {
            Role::create([
                'name' => $this->name,
                'guard_name' => $this->guard_name,
            ]);
        }

        $this->showRoleModal = false;
        $this->reset(['name', 'guard_name', 'editingRoleId']);
        $this->dispatch('notify', type: 'success', message: 'Role saved successfully.');
    }

    public function deleteRole($id)
    {
        Role::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Role deleted.');
    }

    public function createPermission()
    {
        $this->reset(['name', 'guard_name']);
        $this->showPermissionModal = true;
    }

    public function savePermission()
    {
        $this->validate([
            'name' => 'required|min:3|unique:permissions,name',
            'guard_name' => 'required',
        ]);

        Permission::create([
            'name' => $this->name,
            'guard_name' => $this->guard_name,
        ]);

        $this->showPermissionModal = false;
        $this->reset(['name', 'guard_name']);
        $this->dispatch('notify', type: 'success', message: 'Permission created.');
    }

    public function deletePermission($id)
    {
        Permission::findOrFail($id)->delete();
        $this->dispatch('notify', type: 'success', message: 'Permission deleted.');
    }

    public function togglePermission($roleId, $permissionName)
    {
        $role = Role::findOrFail($roleId);
        if ($role->hasPermissionTo($permissionName)) {
            $role->revokePermissionTo($permissionName);
        } else {
            $role->givePermissionTo($permissionName);
        }
    }

    public function render()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('livewire.superadmin.role-list', [
            'roles' => $roles,
            'permissions' => $permissions
        ]);
    }
}
