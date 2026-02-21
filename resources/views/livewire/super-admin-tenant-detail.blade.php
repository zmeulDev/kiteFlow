<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Tenant;
use App\Models\TenantContact;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

new #[Layout('components.layouts.app')] class extends Component {
    public ?Tenant $tenant = null;
    public string $subscriptionTier = 'Hobby';
    public $subTenants = [];
    public $contacts = [];
    
    // Contract Modal State
    public bool $showContractModal = false;
    public string $editStartDate = '';
    public string $editEndDate = '';
    
    // Contact Modal State
    public bool $showContactModal = false;
    public ?int $editingContactId = null;
    public string $contactName = '';
    public string $contactEmail = '';
    public string $contactPhone = '';
    public bool $contactIsMain = false;

    // User Edit Modal State
    public $tenantUsers = [];
    public $roles = [];
    public bool $showEditUserModal = false;
    public ?int $editingUserId = null;
    public array $editUserData = ['name' => '', 'email' => '', 'role' => '', 'is_active' => true];

    // Unified Confirmation State
    public bool $showConfirmModal = false;
    public string $confirmActionType = '';
    public ?int $confirmId = null;
    public string $confirmMessage = '';

    public function mount(Tenant $tenant) {
        if ($tenant->parent_id !== null) {
            abort(404, 'Cannot view detail page for a sub-tenant.');
        }

        $this->tenant = $tenant;
        
        if ($tenant->subscribed('default')) {
            $this->subscriptionTier = 'Enterprise (Active)';
        } else {
            $this->subscriptionTier = 'Hobby // Grace Period';
        }

        $this->subTenants = $tenant->subTenants()->withCount('users')->get();
        $this->roles = Role::where('name', '!=', 'SuperAdmin')->get();
        $this->loadContacts();
        $this->loadUsers();
    }

    public function loadUsers() {
        $this->tenantUsers = User::where('tenant_id', $this->tenant->id)->with('allTenantRoles')->get();
    }

    public function loadContacts() {
        $this->contacts = $this->tenant->contacts()->orderByDesc('is_main')->get();
    }

    public function openContractModal() {
        $this->editStartDate = $this->tenant->contract_start_date ? $this->tenant->contract_start_date->format('Y-m-d') : '';
        $this->editEndDate = $this->tenant->contract_end_date ? $this->tenant->contract_end_date->format('Y-m-d') : '';
        $this->showContractModal = true;
    }

    public function closeContractModal() {
        $this->showContractModal = false;
    }

    public function updateContractDetails() {
        $this->validate([
            'editStartDate' => 'nullable|date',
            'editEndDate' => 'nullable|date|after_or_equal:editStartDate',
        ]);

        $this->tenant->update([
            'contract_start_date' => $this->editStartDate ?: null,
            'contract_end_date' => $this->editEndDate ?: null,
        ]);

        $this->showContractModal = false;
        session()->flash('message', 'Contract details updated.');
    }

    public function openContactModal($contactId = null) {
        $this->resetValidation();
        if ($contactId) {
            $contact = TenantContact::findOrFail($contactId);
            $this->editingContactId = $contact->id;
            $this->contactName = $contact->name;
            $this->contactEmail = $contact->email;
            $this->contactPhone = $contact->phone;
            $this->contactIsMain = $contact->is_main;
        } else {
            $this->editingContactId = null;
            $this->contactName = '';
            $this->contactEmail = '';
            $this->contactPhone = '';
            $this->contactIsMain = false;
        }
        $this->showContactModal = true;
    }

    public function closeContactModal() {
        $this->showContactModal = false;
    }

    public function saveContact() {
        $this->validate([
            'contactName' => 'required|string|max:255',
            'contactEmail' => 'nullable|email|max:255',
            'contactPhone' => 'nullable|string|max:20',
            'contactIsMain' => 'boolean',
        ]);

        if ($this->contactIsMain) {
            $this->tenant->contacts()->update(['is_main' => false]);
        }

        if ($this->editingContactId) {
            $contact = TenantContact::findOrFail($this->editingContactId);
            $contact->update([
                'name' => $this->contactName,
                'email' => $this->contactEmail,
                'phone' => $this->contactPhone,
                'is_main' => $this->contactIsMain,
            ]);
            session()->flash('message', 'Contact updated.');
        } else {
            $this->tenant->contacts()->create([
                'name' => $this->contactName,
                'email' => $this->contactEmail,
                'phone' => $this->contactPhone,
                'is_main' => $this->contactIsMain,
            ]);
            session()->flash('message', 'Contact added.');
        }
        
        $this->loadContacts();
        $this->showContactModal = false;
    }

    public function deleteContact($id) {
        TenantContact::destroy($id);
        $this->loadContacts();
        session()->flash('message', 'Contact removed.');
    }

    public function setMainContact($id) {
        $this->tenant->contacts()->update(['is_main' => false]);
        TenantContact::where('id', $id)->update(['is_main' => true]);
        $this->loadContacts();
        session()->flash('message', 'Main contact updated.');
    }

    public function updateTenantStatus($newStatus) {
        if (in_array($newStatus, ['Active', 'Suspended', 'Inactive'])) {
            $this->tenant->update(['status' => $newStatus]);
            session()->flash('message', "Tenant status updated to {$newStatus}.");
        }
    }

    public function deleteTenant() {
        $this->tenant->delete();
        session()->flash('message', 'Tenant deleted permanently.');
        return redirect('/super-admin/dashboard');
    }

    public function openEditUserModal($id) {
        $user = User::with('allTenantRoles')->findOrFail($id);
        $this->editingUserId = $id;
        $this->editUserData = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->allTenantRoles->first()?->name ?? '',
            'is_active' => $user->is_active,
        ];
        $this->showEditUserModal = true;
    }

    public function closeEditUserModal() {
        $this->showEditUserModal = false;
        $this->editingUserId = null;
    }

    public function updateUser() {
        $this->validate([
            'editUserData.name' => 'required|string|max:255',
            'editUserData.email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'editUserData.role' => 'required|exists:roles,name',
            'editUserData.is_active' => 'boolean',
        ]);

        $user = User::findOrFail($this->editingUserId);
        $user->update([
            'name' => $this->editUserData['name'],
            'email' => $this->editUserData['email'],
            'is_active' => $this->editUserData['is_active'],
        ]);

        setPermissionsTeamId($this->tenant->id);
        $user->syncRoles([$this->editUserData['role']]);

        session()->flash('message', 'User details updated.');
        $this->showEditUserModal = false;
        $this->editingUserId = null;
        $this->loadUsers();
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
        if ($this->confirmActionType === 'deleteContact' && $this->confirmId) {
            $this->deleteContact($this->confirmId);
        } elseif ($this->confirmActionType === 'deleteUser' && $this->confirmId) {
            $this->deleteUser($this->confirmId);
        } elseif ($this->confirmActionType === 'deleteTenant') {
            $this->deleteTenant();
        } elseif ($this->confirmActionType === 'resetPassword' && $this->confirmId) {
            $this->resetUserPassword($this->confirmId);
        }

        $this->showConfirmModal = false;
        $this->confirmActionType = '';
        $this->confirmId = null;
        $this->confirmMessage = '';
    }

    public function closeConfirmModal()
    {
        $this->showConfirmModal = false;
        $this->confirmActionType = '';
        $this->confirmId = null;
        $this->confirmMessage = '';
    }

    public function deleteUser($id) {
        if (auth()->id() === $id) {
            session()->flash('message', 'You cannot delete yourself.');
            return;
        }

        $user = User::findOrFail($id);
        $user->delete();
        session()->flash('message', 'User fully obliterated.');
        $this->loadUsers();
    }

    public function resetUserPassword($id) {
        if (auth()->id() === $id) {
            session()->flash('message', 'You cannot reset your own password here.');
            return;
        }

        $user = User::findOrFail($id);
        $newPassword = \Illuminate\Support\Str::random(12);
        
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($newPassword),
        ]);
        
        $this->showEditUserModal = false;
        $this->editingUserId = null;
        session()->flash('message', 'Password reset successfully for ' . $user->email . '. New Password: ' . $newPassword);
        $this->loadUsers();
    }
};
?>

<div class="min-h-screen bg-[#F4F5F7] p-6 md:p-12">
    <div class="max-w-5xl mx-auto space-y-8 animate-fade-in-up">
        
        <!-- Header & Breadcrumbs -->
        <div class="flex items-center justify-between">
            <div>
                <a href="/super-admin/dashboard" class="text-sm font-bold text-gray-500 hover:text-gray-900 mb-2 inline-block transition-colors">&larr; Back to Dashboard</a>
                <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">{{ $tenant->name }}</h1>
                <p class="text-gray-500 font-mono text-sm tracking-wide mt-1">Tenant ID: {{ $tenant->id }} &bull; Domain: {{ $tenant->domain }}</p>
            </div>
            
            <div class="flex items-center gap-4">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" @click.away="open = false" class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-extrabold transition-colors {{ $tenant->status === 'Active' ? 'bg-green-100 text-green-700 hover:bg-green-200' : ($tenant->status === 'Suspended' ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' : 'bg-red-100 text-red-700 hover:bg-red-200') }}">
                        <span class="w-2 h-2 rounded-full {{ $tenant->status === 'Active' ? 'bg-green-500 animate-pulse' : ($tenant->status === 'Suspended' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                        {{ $tenant->status }}
                        <svg class="w-4 h-4 ml-1 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    
                    <div x-show="open" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-20" style="display: none;">
                        <button wire:click="updateTenantStatus('Active')" @click="open = false" class="w-full text-left px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-green-500"></span> Set Active
                        </button>
                        <button wire:click="updateTenantStatus('Suspended')" @click="open = false" class="w-full text-left px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2 border-t border-gray-50">
                            <span class="w-2 h-2 rounded-full bg-yellow-500"></span> Set Suspended
                        </button>
                        <button wire:click="updateTenantStatus('Inactive')" @click="open = false" class="w-full text-left px-4 py-3 text-sm font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-2 border-t border-gray-50">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span> Set Inactive
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if(session()->has('message'))
            <div class="bg-green-100 border border-green-200 text-green-700 p-4 mb-6 animate-fade-in-up text-sm font-bold rounded-xl flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('message') }}
            </div>
        @endif

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card p-6 border border-gray-100 flex flex-col justify-between h-full hover:-translate-y-1 transition-transform relative">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Primary Contact</p>
                    <button wire:click="openContactModal" class="text-xs font-bold text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-full hover:bg-indigo-100 transition-colors">+ Add</button>
                </div>
                
                <div class="flex-1 flex flex-col justify-center">
                    @php $mainContact = $contacts->firstWhere('is_main', true); @endphp
                    @if($mainContact)
                        <div class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            {{ $mainContact->name }}
                        </div>
                        <div class="text-sm text-gray-500 mt-1">{{ $mainContact->email ?: 'No Email' }}</div>
                        <div class="text-sm text-gray-500">{{ $mainContact->phone ?: 'No Phone' }}</div>
                    @else
                        <div class="text-sm text-gray-400 italic">No Primary Contact</div>
                    @endif
                </div>
            </div>

            <div class="card p-6 border border-gray-100 flex flex-col justify-between h-full hover:-translate-y-1 transition-transform relative">
                <div class="flex justify-between items-start mb-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Contract Details</p>
                    <button wire:click="openContractModal" class="text-xs font-bold text-blue-600 bg-blue-50 px-3 py-1.5 rounded-full hover:bg-blue-100 transition-colors">Edit</button>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-sm font-medium text-gray-500">Start:</span>
                        <span class="text-sm font-bold text-gray-900">{{ $tenant->contract_start_date ? $tenant->contract_start_date->format('M j, Y') : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-medium text-gray-500">End:</span>
                        <span class="text-sm font-bold text-gray-900">{{ $tenant->contract_end_date ? $tenant->contract_end_date->format('M j, Y') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <div class="card p-6 border border-gray-100 flex flex-col justify-between h-full hover:-translate-y-1 transition-transform bg-[#FF4B4B] text-white">
                <p class="text-xs font-bold text-white/70 uppercase tracking-widest mb-4">Sub-Tenant Entities</p>
                <div>
                    <div class="text-4xl font-extrabold">{{ count($subTenants) }}</div>
                    <div class="text-sm font-medium text-white/80 mt-1">Active Branches</div>
                </div>
            </div>
        </div>

        <!-- Administrative Contacts Grid -->
        <div class="card p-0 overflow-hidden mt-8">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-extrabold text-gray-900">Administrative Contacts</h2>
                <button wire:click="openContactModal" class="btn-secondary text-sm px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 font-bold transition-colors">Add Contact</button>
            </div>
            
            @if(count($contacts) > 0)
                <div class="overflow-x-auto">
                    <table class="data-grid w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                                <th class="p-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($contacts as $c)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td class="p-4">
                                        <div class="font-bold text-gray-900">{{ $c->name }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-sm text-gray-500">{{ $c->email ?: '-' }}</div>
                                    </td>
                                    <td class="p-4">
                                        <div class="text-sm text-gray-500">{{ $c->phone ?: '-' }}</div>
                                    </td>
                                    <td class="p-4">
                                        @if($c->is_main)
                                            <span class="bg-yellow-100 text-yellow-800 text-[10px] px-2 py-1 rounded font-bold uppercase tracking-wide">Main Contact</span>
                                        @else
                                            <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-1 rounded font-bold uppercase tracking-wide">Secondary</span>
                                        @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        <div class="flex items-center justify-end gap-2 transition-opacity">
                                            @if(!$c->is_main)
                                                <button wire:click="setMainContact({{ $c->id }})" class="text-xs font-bold text-yellow-600 hover:text-yellow-800 bg-yellow-50 hover:bg-yellow-100 px-2 py-1 rounded transition-colors">Make Main</button>
                                            @endif
                                            <button wire:click="confirmAction('deleteContact', {{ $c->id }}, 'Remove this contact?')" class="text-gray-400 hover:text-red-600 transition-colors p-1"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500 font-medium bg-gray-50/50">
                    No contacts provisioned yet.
                </div>
            @endif
        </div>

        <!-- Sub-Tenants Data Grid -->
        <div class="card p-0 overflow-hidden mt-8">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-xl font-extrabold text-gray-900">Registered Sub-Tenants</h2>
            </div>
            
            @if(count($subTenants) > 0)
                <div class="overflow-x-auto">
                    <table class="data-grid w-full">
                        <thead>
                            <tr>
                                <th>Branch Name</th>
                                <th>Domain Prefix</th>
                                <th class="text-center">Assigned Users</th>
                                <th class="text-right">Created On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subTenants as $sub)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td>
                                        <div class="font-bold text-gray-900">{{ $sub->name }}</div>
                                    </td>
                                    <td>
                                        <div class="text-sm text-gray-500 font-mono">{{ $sub->domain }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="inline-flex items-center justify-center px-2.5 py-1 text-xs font-bold bg-gray-100 text-gray-600 rounded-full">
                                            {{ $sub->users_count }}
                                        </span>
                                    </td>
                                    <td class="text-right text-sm text-gray-500">
                                        {{ $sub->created_at->format('M j, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-16 text-gray-500 font-medium bg-gray-50/50">
                    This parent tenant has not provisioned any child sub-tenants.
                </div>
            @endif
        </div>

        <!-- Provisioned Users Data Grid -->
        <div class="card p-0 overflow-hidden mt-8">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-xl font-extrabold text-gray-900">Provisioned Users</h2>
            </div>
            
            @if(count($tenantUsers) > 0)
                <div class="overflow-x-auto">
                    <table class="data-grid w-full">
                        <thead>
                            <tr>
                                <th>Identity</th>
                                <th>Platform Role</th>
                                <th>Created On</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenantUsers as $user)
                                <tr class="hover:bg-gray-50/50 transition-colors group">
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                            @if(!$user->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold bg-red-100 text-red-800 uppercase tracking-wider">Suspended</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500 font-mono">{{ $user->email }}</div>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-gray-100 text-gray-800">
                                            {{ $user->allTenantRoles->pluck('name')->join(', ') ?: 'No Role' }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-gray-500">
                                        {{ $user->created_at->format('M j, Y') }}
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2 transition-opacity">
                                            <button wire:click="openEditUserModal({{ $user->id }})" class="text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">Edit</button>
                                            @if(auth()->id() !== $user->id)
                                                <button wire:click="confirmAction('deleteUser', {{ $user->id }}, 'Permanently obliterate this user account?')" class="text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">Delete</button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-16 text-gray-500 font-medium bg-gray-50/50">
                    No users have been provisioned directly under this workspace.
                </div>
            @endif
        </div>

        <!-- Danger Zone -->
        <div class="mt-12 p-6 border border-red-200 bg-red-50/30 rounded-[24px]">
            <h3 class="text-lg font-extrabold text-red-900 mb-2">Danger Zone</h3>
            <p class="text-sm text-red-700 mb-4">Deleting a tenant environment is a non-reversible action that will permanently destroy all associated sub-tenants, user identities, contacts, and configuration data.</p>
            <button wire:click="confirmAction('deleteTenant', {{ $tenant->id }}, 'Are you absolutely sure you want to permanently obliterate this Tenant and all its data?')" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors border border-red-700 border-b-red-800">
                Permanently Delete {{ $tenant->name }}
            </button>
        </div>

    </div>

    <!-- Edit Contract Modal Overlay -->
    @if($showContractModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm animate-fade-in-up" wire:click.self="closeContractModal">
            <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 m-4">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-extrabold text-gray-900">Contract Dates</h3>
                    <button wire:click="closeContractModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form wire:submit="updateContractDetails" class="p-8 space-y-6">
                    <div class="space-y-4">
                        <div class="input-group mb-0">
                            <label class="text-xs font-bold text-gray-900 mb-1">Contract Start Date</label>
                            <input wire:model="editStartDate" type="date" class="input py-2">
                            @error('editStartDate') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="input-group mb-0">
                            <label class="text-xs font-bold text-gray-900 mb-1">Contract End Date</label>
                            <input wire:model="editEndDate" type="date" class="input py-2">
                            @error('editEndDate') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="closeContractModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="btn">Update Dates</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Edit/Add Contact Modal Overlay -->
    @if($showContactModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm animate-fade-in-up" wire:click.self="closeContactModal">
            <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 m-4">
                <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-xl font-extrabold text-gray-900">{{ $editingContactId ? 'Edit Contact' : 'New Contact' }}</h3>
                    <button wire:click="closeContactModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form wire:submit="saveContact" class="p-8 space-y-6">
                    <div class="space-y-4">
                        <div class="input-group mb-0">
                            <label class="text-xs font-bold text-gray-900 mb-1">Contact Name <span class="text-[#FF4B4B]">*</span></label>
                            <input wire:model="contactName" type="text" placeholder="Jane Doe" class="input py-2">
                            @error('contactName') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="input-group mb-0">
                            <label class="text-xs font-bold text-gray-900 mb-1">Email Address</label>
                            <input wire:model="contactEmail" type="email" placeholder="jane.doe@example.com" class="input py-2">
                            @error('contactEmail') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="input-group mb-0">
                            <label class="text-xs font-bold text-gray-900 mb-1">Phone Number</label>
                            <input wire:model="contactPhone" type="text" placeholder="+1 (555) 123-4567" class="input py-2">
                            @error('contactPhone') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <input wire:model="contactIsMain" id="contactIsMain" type="checkbox" class="w-4 h-4 text-[#FF4B4B] border-gray-300 rounded focus:ring-[#FF4B4B]">
                            <label for="contactIsMain" class="text-sm font-medium text-gray-900">Set as Main Contact</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <button type="button" wire:click="closeContactModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">Cancel</button>
                        <button type="submit" class="btn">Save Contact</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
                            <input wire:model="editUserData.is_active" id="editTenantUserIsActive" type="checkbox" class="w-4 h-4 text-[#FF4B4B] border-gray-300 rounded focus:ring-[#FF4B4B]">
                            <label for="editTenantUserIsActive" class="text-sm font-medium text-gray-900">Account Active (Allows Login)</label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div>
                            @if(auth()->id() !== $editingUserId)
                                <button type="button" wire:click="confirmAction('resetPassword', {{ $editingUserId }}, 'Are you sure you want to forcibly reset this user\'s password? They will be locked out until you provide the new one.')" class="px-4 py-2 text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Force Reset Password</button>
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

    <!-- Global Action Confirmation Modal -->
    @if($showConfirmModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm" wire:click="$set('showConfirmModal', false)" style="animation: fade-in-up 0.3s ease-out forwards;">
            <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 m-4 relative" wire:click.stop>
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-[#FF4B4B]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-[#FF4B4B]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Are you certain?</h3>
                    <p class="text-sm text-gray-500 mb-8">{{ $confirmMessage }}</p>
                    <div class="flex items-center justify-center gap-3">
                        <button type="button" wire:click="closeConfirmModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition-colors mb-0">Cancel</button>
                        <button type="button" wire:click="executeAction" class="px-5 py-2.5 text-sm font-bold text-white bg-[#FF4B4B] hover:bg-red-600 shadow-md hover:shadow-lg rounded-xl transition-all mb-0">Yes, Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
