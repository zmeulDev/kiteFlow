<?php

use function Livewire\Volt\{state, mount, layout};
use App\Models\Tenant;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

layout('components.layouts.app');

state(['tenants' => [], 'activeTab' => 'tenants', 'newTenantName' => '', 'newTenantDomain' => '', 'roles' => [], 'users' => [], 'newUser' => ['name' => '', 'email' => '', 'password' => '', 'tenant_id' => '', 'role' => ''], 'showCreateTenantModal' => false, 'totalTenants' => 0, 'totalUsers' => 0, 'showEditUserModal' => false, 'editingUserId' => null, 'editUserData' => ['name' => '', 'email' => '', 'tenant_id' => '', 'role' => '', 'is_active' => true]]);

mount(function () {
    // Ensure roles exist
    $coreRoles = ['SuperAdmin', 'TenantAdmin', 'SubTenantAdmin', 'FrontDesk', 'StandardUser'];
    foreach ($coreRoles as $role) {
        Role::firstOrCreate(['name' => $role]);
    }
    
    $this->loadData();
});

$loadData = function() {
    $this->tenants = Tenant::whereNull('parent_id')->withCount('users')->orderByDesc('created_at')->get();
    $this->roles = Role::where('name', '!=', 'SuperAdmin')->get();
    $this->users = User::with('allTenantRoles', 'tenant')->get();
    
    $this->totalTenants = Tenant::count();
    $this->totalUsers = User::count();
};

$setTab = function($tab) {
    $this->activeTab = $tab;
};

$createTenant = function() {
    $this->validate([
        'newTenantName' => 'required|string|max:255',
        'newTenantDomain' => 'required|string|max:255|unique:tenants,domain',
    ]);
    
    Tenant::create([
        'name' => $this->newTenantName,
        'domain' => $this->newTenantDomain,
        'status' => 'Active',
    ]);
    
    session()->flash('message', 'Tenant environment provisioned.');
    $this->newTenantName = '';
    $this->newTenantDomain = '';
    $this->showCreateTenantModal = false;
    $this->loadData();
};

$openCreateTenantModal = function() {
    $this->newTenantName = '';
    $this->newTenantDomain = '';
    $this->showCreateTenantModal = true;
};

$closeCreateTenantModal = function() {
    $this->showCreateTenantModal = false;
};



$createUser = function() {
    $this->validate([
        'newUser.name' => 'required|string|max:255',
        'newUser.email' => 'required|email|unique:users,email',
        'newUser.password' => 'required|min:8',
        'newUser.tenant_id' => 'required|exists:tenants,id',
        'newUser.role' => 'required|exists:roles,name',
    ]);

    $user = User::create([
        'name' => $this->newUser['name'],
        'email' => $this->newUser['email'],
        'password' => Hash::make($this->newUser['password']),
        'tenant_id' => $this->newUser['tenant_id'],
    ]);

    setPermissionsTeamId($this->newUser['tenant_id']);
    $user->assignRole($this->newUser['role']);

    session()->flash('user_message', 'User created and role assigned securely.');
    $this->newUser = ['name' => '', 'email' => '', 'password' => '', 'tenant_id' => '', 'role' => ''];
    $this->loadData();
};

$openEditUserModal = function($id) {
    // Must find without any team_id restrictions, eager load the direct pivot mapping
    $user = User::with('allTenantRoles')->findOrFail($id);
    $this->editingUserId = $id;
    $this->editUserData = [
        'name' => $user->name,
        'email' => $user->email,
        'tenant_id' => $user->tenant_id,
        'role' => $user->allTenantRoles->first()?->name ?? '',
        'is_active' => $user->is_active,
    ];
    $this->showEditUserModal = true;
};

$closeEditUserModal = function() {
    $this->showEditUserModal = false;
    $this->editingUserId = null;
};

$updateUser = function() {
    $this->validate([
        'editUserData.name' => 'required|string|max:255',
        'editUserData.email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingUserId)],
        'editUserData.tenant_id' => 'required|exists:tenants,id',
        'editUserData.role' => 'required|exists:roles,name',
        'editUserData.is_active' => 'boolean',
    ]);

    $user = User::findOrFail($this->editingUserId);
    $user->update([
        'name' => $this->editUserData['name'],
        'email' => $this->editUserData['email'],
        'tenant_id' => $this->editUserData['tenant_id'],
        'is_active' => $this->editUserData['is_active'],
    ]);

    // Force team context to sync the role properly for this user
    setPermissionsTeamId($this->editUserData['tenant_id']);
    $user->syncRoles([$this->editUserData['role']]);

    session()->flash('user_message', 'User updated securely.');
    $this->showEditUserModal = false;
    $this->editingUserId = null;
    $this->loadData();
};

$deleteUser = function($id) {
    if (auth()->id() === $id) {
        session()->flash('user_message', 'You cannot delete yourself.');
        return;
    }

    $user = User::findOrFail($id);
    $user->delete();
    session()->flash('user_message', 'System user completely removed.');
    $this->loadData();
};

$resetUserPassword = function($id) {
    if (auth()->id() === $id) {
        session()->flash('user_message', 'You cannot reset your own password here.');
        return;
    }

    $user = User::findOrFail($id);
    $newPassword = Str::random(12);
    
    $user->update([
        'password' => Hash::make($newPassword),
    ]);
    
    // Close modal if open to show the flash message clearly
    $this->showEditUserModal = false;
    $this->editingUserId = null;
    session()->flash('user_message', 'Password reset successfully for ' . $user->email . '. New Password: ' . $newPassword);
    $this->loadData();
};

$__volt_compiler_fix = true;
?>

<div class="min-h-screen bg-[#F4F5F7] flex flex-col md:flex-row">
    
    <!-- Sidebar -->
    <aside class="w-full md:w-64 bg-white border-r border-gray-200 flex flex-col sticky top-0 md:h-screen z-20 shadow-sm">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <h1 class="text-xl font-extrabold tracking-tight text-gray-900 group flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-[#FF4B4B] group-hover:animate-ping"></span>
                VisiFlow <span class="text-gray-400 font-medium">SA</span>
            </h1>
        </div>
        
        <div class="p-6 flex-grow flex flex-col justify-between overflow-y-auto">
            <nav class="space-y-2">
                <button wire:click="setTab('tenants')" class="w-full text-left px-4 py-3 text-sm font-bold transition-all duration-150 rounded-xl {{ $activeTab === 'tenants' ? 'bg-[#FF4B4B]/10 text-[#FF4B4B]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    Manage Tenants
                </button>
                <button wire:click="setTab('users')" class="w-full text-left px-4 py-3 text-sm font-bold transition-all duration-150 rounded-xl {{ $activeTab === 'users' ? 'bg-[#FF4B4B]/10 text-[#FF4B4B]' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                    Users & RBAC
                </button>
                <a href="/admin/dashboard" class="block w-full text-left px-4 py-3 text-sm font-bold transition-all duration-150 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                    &larr; Tenant View
                </a>
            </nav>
            
            <div class="pt-6 border-t border-gray-100">
                <div class="card p-5 bg-gray-50/50 border-0 mb-6">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Platform Stats</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600">Total Workspaces</span>
                            <span class="text-sm font-extrabold text-gray-900">{{ $totalTenants }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600">Total Users</span>
                            <span class="text-sm font-extrabold text-gray-900">{{ $totalUsers }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 px-2">
                    @if(auth()->check())
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold text-xs">
                            {{ substr(auth()->user()->name, 0, 2) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">Super Admin</p>
                        </div>
                    @else
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">System Admin</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 p-6 md:p-12 overflow-y-auto w-full relative">
        @if($activeTab === 'tenants')
            <div class="max-w-6xl mx-auto space-y-8 animate-fade-in-up">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight mb-2 text-gray-900">Tenant Environments</h2>
                        <p class="text-gray-500">Global overview of all provisioned workspaces on the platform.</p>
                    </div>
                    <button wire:click="openCreateTenantModal" class="btn shrink-0">+ Initialize Tenant</button>
                </div>
                
                @if(session()->has('message'))
                    <div class="bg-green-100 border border-green-200 text-green-700 p-4 mb-6 animate-fade-in-up text-sm font-bold rounded-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('message') }}
                    </div>
                @endif
                
                <div class="card p-0 overflow-hidden w-full">
                    @if(count($tenants) > 0)
                        <div class="overflow-x-auto">
                            <table class="data-grid w-full">
                                <thead>
                                    <tr>
                                        <th>Tenant Details</th>
                                        <th class="text-center">Lifecycle Status</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tenants as $t)
                                        <tr class="group hover:bg-gray-50/50 transition-colors">
                                            <td>
                                                <div class="font-bold text-gray-900 text-lg">{{ $t->name }}</div>
                                                <div class="text-sm text-gray-500 font-mono mt-1">{{ $t->domain }} &bull; {{ $t->users_count }} users</div>
                                            </td>
                                            <td class="text-center">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $t->status === 'Active' ? 'bg-green-100 text-green-800' : ($t->status === 'Suspended' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $t->status === 'Active' ? 'bg-green-500' : ($t->status === 'Suspended' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                                                    {{ $t->status }}
                                                </span>
                                            </td>
                                            <td class="text-right">
                                                <a href="{{ route('super-admin.tenant.detail', $t->id) }}" class="text-indigo-600 font-bold text-sm bg-indigo-50 px-5 py-2.5 rounded-full hover:bg-indigo-100 transition-colors inline-block">Manage Details &rarr;</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-16 text-gray-500 font-medium bg-gray-50/50">
                            No tenants provisioned on this instance.
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Initialize Tenant Modal -->
            @if($showCreateTenantModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm animate-fade-in-up" wire:click.self="closeCreateTenantModal">
                    <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 m-4">
                        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-xl font-extrabold text-gray-900">Initialize Tenant</h3>
                            <button wire:click="closeCreateTenantModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <form wire:submit="createTenant" class="p-8 space-y-6">
                            <div class="space-y-4">
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Business Name <span class="text-[#FF4B4B]">*</span></label>
                                    <input wire:model="newTenantName" type="text" class="input py-2" placeholder="e.g. Acme Corp">
                                    @error('newTenantName') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Domain Identifier <span class="text-[#FF4B4B]">*</span></label>
                                    <input wire:model="newTenantDomain" type="text" class="input py-2 font-mono" placeholder="acme">
                                    @error('newTenantDomain') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                                <button type="button" wire:click="closeCreateTenantModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">Cancel</button>
                                <button type="submit" class="btn">Provision Environment</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
            
        @elseif($activeTab === 'users')
            <div class="max-w-6xl mx-auto space-y-8 animate-fade-in-up delay-100">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight mb-2 text-gray-900">Identity & RBAC Access</h2>
                    <p class="text-gray-500">Manage super administrators and tenant administrators across the entire system.</p>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <div class="lg:col-span-2 card p-0 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="data-grid w-full">
                                <thead>
                                    <tr>
                                        <th>Identity</th>
                                        <th>Platform Role</th>
                                        <th>Assigned Tenant</th>
                                        <th class="text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr class="group hover:bg-gray-50/50 transition-colors">
                                            <td>
                                                <div class="flex items-center gap-2">
                                                    <div class="font-bold text-gray-900 text-base">{{ $user->name }}</div>
                                                    @if(!$user->is_active)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-800 uppercase tracking-wider">Suspended</span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500 mt-1">{{ $user->email }}</div>
                                            </td>
                                            <td>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-gray-100 text-gray-800">
                                                    {{ $user->allTenantRoles->pluck('name')->join(', ') ?: 'No Role' }}
                                                </span>
                                            </td>
                                            <td class="text-gray-600 font-medium">
                                                {{ $user->tenant?->name ?? 'System Level' }}
                                            </td>
                                            <td class="text-right">
                                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <button wire:click="openEditUserModal({{ $user->id }})" class="text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">Edit</button>
                                                    @if(auth()->id() !== $user->id)
                                                        <button wire:click="deleteUser({{ $user->id }})" wire:confirm="Permanently obliterate this user account?" class="text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">Delete</button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="lg:col-span-1">
                        <div class="card p-8 bg-white border border-gray-200">
                            <h3 class="text-xl font-extrabold text-gray-900 mb-6">Create System User</h3>
                            
                            @if(session()->has('user_message'))
                                <div class="bg-green-100 border border-green-200 text-green-700 p-3 mb-6 animate-fade-in-up text-sm font-bold rounded-xl text-center">
                                    {{ session('user_message') }}
                                </div>
                            @endif
                            
                            <form wire:submit="createUser" class="space-y-4">
                                <div class="input-group mb-0">
                                    <label class="text-xs font-bold text-gray-900 mb-1">Full Name</label>
                                    <input wire:model="newUser.name" type="text" class="input py-2">
                                    @error('newUser.name') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-xs font-bold text-gray-900 mb-1">Email Address</label>
                                    <input wire:model="newUser.email" type="email" class="input py-2">
                                    @error('newUser.email') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-xs font-bold text-gray-900 mb-1">Temporary Password</label>
                                    <input wire:model="newUser.password" type="password" class="input py-2">
                                    @error('newUser.password') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-xs font-bold text-gray-900 mb-1">Select Associated Tenant</label>
                                    <select wire:model="newUser.tenant_id" class="input py-2 bg-white">
                                        <option value="">-- Choose Tenant --</option>
                                        @foreach($tenants as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newUser.tenant_id') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-6">
                                    <label class="text-xs font-bold text-gray-900 mb-1">Assign RBAC Policy</label>
                                    <select wire:model="newUser.role" class="input py-2 bg-white">
                                        <option value="">-- Select Authority Level --</option>
                                        @foreach($roles as $r)
                                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newUser.role') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>

                                <button type="submit" class="btn w-full mt-2">Generate Account</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit User Modal -->
            @if($showEditUserModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm animate-fade-in-up" wire:click.self="closeEditUserModal">
                    <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 m-4">
                        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-xl font-extrabold text-gray-900">Edit System User</h3>
                            <button wire:click="closeEditUserModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        
                        <form wire:submit="updateUser" class="p-8 space-y-6">
                            <div class="space-y-4">
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Full Name <span class="text-[#FF4B4B]">*</span></label>
                                    <input wire:model="editUserData.name" type="text" class="input py-2">
                                    @error('editUserData.name') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Email Address <span class="text-[#FF4B4B]">*</span></label>
                                    <input wire:model="editUserData.email" type="email" class="input py-2">
                                    @error('editUserData.email') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Associated Tenant <span class="text-[#FF4B4B]">*</span></label>
                                    <select wire:model="editUserData.tenant_id" class="input py-2 bg-white">
                                        <option value="">-- Choose Tenant --</option>
                                        @foreach($tenants as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('editUserData.tenant_id') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">RBAC Policy <span class="text-[#FF4B4B]">*</span></label>
                                    <select wire:model="editUserData.role" class="input py-2 bg-white">
                                        <option value="">-- Select Authority Level --</option>
                                        @foreach($roles as $r)
                                            <option value="{{ $r->name }}">{{ $r->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('editUserData.role') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex items-center gap-3 pt-2">
                                    <input wire:model="editUserData.is_active" id="editIsActive" type="checkbox" class="w-4 h-4 text-[#FF4B4B] border-gray-300 rounded focus:ring-[#FF4B4B]">
                                    <label for="editIsActive" class="text-sm font-medium text-gray-900">Account Active (Allows Login)</label>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div>
                                    @if(auth()->id() !== $editingUserId)
                                        <button type="button" wire:click="resetUserPassword({{ $editingUserId }})" wire:confirm="Are you sure you want to forcibly reset this user's password? They will be locked out until you provide the new one." class="px-4 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Force Reset Password</button>
                                    @endif
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" wire:click="closeEditUserModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">Cancel</button>
                                    <button type="submit" class="btn">Save Changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        @endif
    </main>
</div>