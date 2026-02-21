<?php

use Livewire\Volt\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

new class extends Component {
    public $roles = [];
    public $permissions = [];

    // Form State
    public string $newRoleName = '';
    public string $newPermissionName = '';

    // Edit Role State
    public bool $showEditRoleModal = false;
    public ?int $editingRoleId = null;
    public string $editRoleName = '';
    public array $rolePermissions = [];

    // Master list of all possible system permissions
    public array $availableSystemPermissions = [
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

    // Unified Confirmation State
    public bool $showConfirmModal = false;
    public string $confirmActionType = '';
    public ?int $confirmId = null;
    public string $confirmMessage = '';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->roles = Role::with('permissions')->orderBy('name')->get();
        $this->permissions = Permission::orderBy('name')->get();
    }

    public function createRole()
    {
        $this->validate(['newRoleName' => 'required|string|unique:roles,name|max:255']);
        Role::create(['name' => $this->newRoleName]);
        $this->newRoleName = '';
        $this->loadData();
        session()->flash('rbac_message', 'Role created successfully.');
    }

    public function createPermission()
    {
        $this->validate([
            'newPermissionName' => 'required|string|unique:permissions,name|in:' . implode(',', $this->availableSystemPermissions)
        ]);
        
        Permission::create(['name' => $this->newPermissionName]);
        $this->newPermissionName = '';
        $this->loadData();
        session()->flash('rbac_message', 'Permission deployed to system successfully.');
    }

    public function openEditRoleModal($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        
        if (in_array($role->name, ['SuperAdmin', 'TenantAdmin'])) {
            session()->flash('rbac_error', 'Cannot edit core system roles directly.');
            return;
        }

        $this->editingRoleId = $role->id;
        $this->editRoleName = $role->name;
        // Pluck the IDs of the permissions currently assigned to this role into our bounded array
        $this->rolePermissions = $role->permissions->pluck('id')->map(fn($id) => (string)$id)->toArray();
        $this->showEditRoleModal = true;
    }

    public function closeEditRoleModal()
    {
        $this->showEditRoleModal = false;
        $this->editingRoleId = null;
        $this->editRoleName = '';
        $this->rolePermissions = [];
    }

    public function updateRole()
    {
        $role = Role::findOrFail($this->editingRoleId);
        
        $this->validate([
            'editRoleName' => 'required|string|max:255|unique:roles,name,' . $this->editingRoleId,
            'rolePermissions' => 'array'
        ]);

        $role->update(['name' => $this->editRoleName]);
        
        // Sync permissions (requires permission names, not IDs generally, or array of IDs depending on Spatie Setup. 
        // Spatie syncPermissions accepts IDs perfectly fine.
        $permissionsToSync = Permission::whereIn('id', $this->rolePermissions)->get();
        $role->syncPermissions($permissionsToSync);

        $this->closeEditRoleModal();
        $this->loadData();
        session()->flash('rbac_message', 'Role updated successfully.');
    }

    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        
        if (in_array($role->name, ['SuperAdmin', 'TenantAdmin'])) {
            session()->flash('rbac_error', 'Critical System Roles cannot be deleted.');
            return;
        }

        if ($role->users()->count() > 0) {
            session()->flash('rbac_error', 'Cannot delete role: There are users currently assigned to it.');
            return;
        }

        $role->delete();
        $this->loadData();
        session()->flash('rbac_message', 'Role deleted successfully.');
    }

    public function deletePermission($id)
    {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        $this->loadData();
        session()->flash('rbac_message', 'Permission deleted successfully.');
    }

    public function confirmAction(string $actionType, ?int $id, string $message)
    {
        $this->confirmActionType = $actionType;
        $this->confirmId = $id;
        $this->confirmMessage = $message;
        $this->showConfirmModal = true;
    }

    public function executeAction()
    {
        if ($this->confirmActionType === 'deleteRole' && $this->confirmId) {
            $this->deleteRole($this->confirmId);
        } elseif ($this->confirmActionType === 'deletePermission' && $this->confirmId) {
            $this->deletePermission($this->confirmId);
        }

        $this->closeConfirmModal();
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->confirmActionType = '';
        $this->confirmId = null;
        $this->confirmMessage = '';
    }
};
?>

<div>
    <!-- Success/Error Messaging -->
    @if(session()->has('rbac_message'))
        <div class="bg-green-100 border border-green-200 text-green-700 p-4 mb-8 animate-fade-in-up font-bold rounded-xl shadow-sm">
            {{ session('rbac_message') }}
        </div>
    @endif
    
    @if(session()->has('rbac_error'))
        <div class="bg-red-100 border border-red-200 text-red-700 p-4 mb-8 animate-fade-in-up font-bold rounded-xl shadow-sm">
            {{ session('rbac_error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Roles Management Column -->
        <div class="space-y-6">
            <div class="card p-6 border border-gray-200 bg-white">
                <h3 class="text-xl font-extrabold text-gray-900 mb-6">Access Roles</h3>
                
                <form wire:submit="createRole" class="flex gap-2 mb-6">
                    <input wire:model="newRoleName" type="text" placeholder="e.g. Receptionist" class="input flex-1 py-2">
                    <button type="submit" class="btn whitespace-nowrap bg-gray-900 !text-white hover:bg-black">Add Role</button>
                </form>
                @error('newRoleName') <span class="text-[#FF4B4B] text-xs font-bold block -mt-4 mb-4">{{ $message }}</span> @enderror

                <div class="space-y-3">
                    @foreach($roles as $role)
                        <div class="flex items-center justify-between p-4 rounded-xl border {{ in_array($role->name, ['SuperAdmin', 'TenantAdmin']) ? 'bg-blue-50/50 border-blue-100' : 'bg-gray-50 border-gray-200' }}">
                            <div>
                                <div class="font-bold text-gray-900 flex items-center gap-2">
                                    {{ $role->name }}
                                    @if(in_array($role->name, ['SuperAdmin', 'TenantAdmin']))
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-blue-100 text-blue-800 uppercase tracking-wider">System Default</span>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500 mt-1">{{ $role->permissions->count() }} Permissions Attached</div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                @if(!in_array($role->name, ['SuperAdmin', 'TenantAdmin']))
                                    <button wire:click="openEditRoleModal({{ $role->id }})" class="text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">Edit</button>
                                    <button wire:click="confirmAction('deleteRole', {{ $role->id }}, 'Permanently delete this role? Any attached permissions will be unlinked, but users must be reassigned manually if applicable.')" class="text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">Delete</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Permissions Management Column -->
        <div class="space-y-6">
            <div class="card p-6 border border-gray-200 bg-white">
                <h3 class="text-xl font-extrabold text-gray-900 mb-6">Isolated Permissions</h3>
                
                <form wire:submit="createPermission" class="flex gap-2 mb-6">
                    <select wire:model="newPermissionName" class="input flex-1 py-2 font-mono text-sm bg-white">
                        <option value="">-- Select Available Permission --</option>
                        @foreach(collect($availableSystemPermissions)->diff($permissions->pluck('name')) as $availablePerm)
                            <option value="{{ $availablePerm }}">{{ $availablePerm }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn whitespace-nowrap bg-gray-900 !text-white hover:bg-black">Deploy Perm</button>
                </form>
                @error('newPermissionName') <span class="text-[#FF4B4B] text-xs font-bold block -mt-4 mb-4">{{ $message }}</span> @enderror

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($permissions as $perm)
                        <div class="flex items-center justify-between p-3 rounded-lg border bg-gray-50 border-gray-200">
                            <div class="font-mono text-xs font-medium text-gray-700">{{ $perm->name }}</div>
                            <button wire:click="confirmAction('deletePermission', {{ $perm->id }}, 'Delete this permission globally?')" class="text-gray-400 hover:text-[#FF4B4B] transition-colors p-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                
                @if(count($permissions) === 0)
                    <div class="text-sm text-gray-500 text-center py-8">No granular permissions exist yet. Create specific policies above.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Edit Role / Attach Permissions Modal -->
    @if($showEditRoleModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm animate-fade-in-up" wire:click.self="closeEditRoleModal">
            <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-2xl overflow-hidden border border-gray-100 m-4">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-extrabold text-gray-900">Manage Role Policy: <span class="text-[#FF4B4B]">{{ $editRoleName }}</span></h3>
                    <button wire:click="closeEditRoleModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form wire:submit="updateRole" class="p-8 space-y-6">
                    <div class="input-group mb-0">
                        <label class="text-sm font-bold text-gray-900 mb-2">Role Name <span class="text-[#FF4B4B]">*</span></label>
                        <input wire:model="editRoleName" type="text" class="input py-2">
                        @error('editRoleName') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-100">
                        <label class="text-sm font-bold text-gray-900 mb-4 block">Assign Available Permissions</label>
                        @if(count($permissions) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-60 overflow-y-auto p-2 bg-gray-50 rounded-xl border border-gray-200">
                                @foreach($permissions as $perm)
                                    <label class="flex items-start gap-3 p-2 rounded hover:bg-white transition-colors cursor-pointer">
                                        <input wire:model="rolePermissions" type="checkbox" value="{{ $perm->id }}" class="mt-1 w-4 h-4 text-[#FF4B4B] border-gray-300 rounded focus:ring-[#FF4B4B]">
                                        <span class="text-sm font-mono text-gray-700">{{ $perm->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <div class="text-sm text-gray-500 bg-gray-50 p-4 rounded-xl text-center">No permissions exist in the system to assign to this role.</div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <button type="button" wire:click="closeEditRoleModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="btn">Save Policy Restrictions</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Global Action Confirmation Modal -->
    @if($showConfirmModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm animate-fade-in-up" wire:click.self="closeConfirmModal">
            <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 m-4 relative">
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-[#FF4B4B]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-[#FF4B4B]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Are you certain?</h3>
                    <p class="text-sm text-gray-500 mb-8">{{ $confirmMessage }}</p>
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" wire:click="closeConfirmModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">Cancel</button>
                        <button type="button" wire:click="executeAction" class="px-5 py-2.5 text-sm font-bold text-white bg-[#FF4B4B] hover:bg-red-600 shadow-md hover:shadow-lg rounded-xl transition-all">Yes, Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
