<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class UserManagement extends Component
{
    use WithPagination;

    public ?int $tenantId = null;
    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?User $selectedUser = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'user';
    public bool $is_active = true;
    public ?int $selectedTenantId = null;  // For tenant selection in modal

    protected $queryString = ['search'];

    public function mount(?int $tenantId = null): void
    {
        $tenantId = $tenantId
            ?? request()->attributes->get('tenant_id')
            ?? auth()->user()?->getCurrentTenant()?->id;

        // Verify user has access to this tenant
        if ($tenantId && auth()->check()) {
            $user = auth()->user();
            if (!$user->belongsToOneOfTenants([$tenantId])) {
                abort(403, 'You do not have access to this tenant data.');
            }
        }

        $this->tenantId = $tenantId;
    }

    public function getUsersProperty()
    {
        if (!$this->tenantId) {
            return collect();
        }

        return User::whereHas('tenants', fn ($q) => $q->where('tenant_id', $this->tenantId))
            ->with(['roles', 'tenants'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->selectedUser = null;
        // Default to current tenant
        $this->selectedTenantId = $this->tenantId;
        $this->showModal = true;
    }

    public function openEditModal(int $userId): void
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->fill([
            'name' => $this->selectedUser->name,
            'email' => $this->selectedUser->email,
            'role' => $this->selectedUser->roles->first()?->name ?? 'user',
            'is_active' => $this->selectedUser->is_active,
            'selectedTenantId' => $this->selectedUser->tenants->first()?->id,
        ]);
        $this->showModal = true;
    }

    public function getAvailableTenantsProperty(): \Illuminate\Support\Collection
    {
        if (!$this->tenantId) {
            return collect();
        }

        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) {
            return collect();
        }

        // Get current tenant and all sub-tenants
        return collect([$tenant])
            ->merge($tenant->children()->orderBy('name')->get())
            ->sortBy(fn ($t) => $t->id === $this->tenantId ? 0 : 1);  // Put parent tenant first
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => $this->selectedUser ? 'nullable|min:8' : 'required|min:8',
            'selectedTenantId' => 'required|integer|exists:tenants,id',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'is_active' => $this->is_active,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->selectedUser) {
            $this->selectedUser->update($data);
            $this->selectedUser->syncRoles([$this->role]);
            // Update tenant assignment
            $this->selectedUser->tenants()->sync([$this->selectedTenantId]);
            session()->flash('message', 'User updated successfully.');
        } else {
            $user = User::create($data);
            $user->assignRole($this->role);
            // Assign to selected tenant
            $user->tenants()->attach($this->selectedTenantId);
            session()->flash('message', 'User created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function openDeleteModal(int $userId): void
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->showDeleteModal = true;
    }

    public function confirmDelete(): void
    {
        if (!$this->selectedUser) {
            return;
        }

        // Prevent deleting yourself
        if ($this->selectedUser->id === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            $this->showDeleteModal = false;
            return;
        }

        $this->selectedUser->delete();
        $this->showDeleteModal = false;
        $this->selectedUser = null;
        session()->flash('message', 'User deleted successfully.');
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'email', 'password', 'role', 'is_active', 'selectedTenantId']);
        $this->role = 'user';
        $this->is_active = true;
        $this->selectedTenantId = null;
    }

    public function render()
    {
        // Allow both super-admins and tenant admins
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Access denied.');
        }

        return view('livewire.settings.user-management', [
            'users' => $this->users,
            'availableTenants' => $this->availableTenants,
        ])->layout('layouts.app');
    }
}