<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;

class TenantManagement extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?Tenant $selectedTenant = null;
    
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $slug = '';
    public string $status = 'active';

    protected $queryString = ['search'];

    public function getTenantsProperty()
    {
        $query = Tenant::withCount(['users', 'visitors', 'meetings']);

        // Super-admins can see all tenants, tenant admins only see their own and sub-tenants
        if (!auth()->user()->isSuperAdmin()) {
            $currentTenant = auth()->user()->getCurrentTenant();
            if ($currentTenant) {
                // Get accessible tenant IDs (self + descendants)
                $accessibleTenantIds = Tenant::getAccessibleTenantIds($currentTenant->id);
                $query->whereIn('id', $accessibleTenantIds);
            } else {
                // User has no tenant, show nothing
                $query->where('id', -1);
            }
        }

        return $query->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('slug', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->selectedTenant = null;
        $this->showModal = true;
    }

    public function openEditModal(int $tenantId): void
    {
        $this->selectedTenant = Tenant::findOrFail($tenantId);
        $this->fill([
            'name' => $this->selectedTenant->name,
            'email' => $this->selectedTenant->email,
            'phone' => $this->selectedTenant->phone ?? '',
            'slug' => $this->selectedTenant->slug,
            'status' => $this->selectedTenant->status,
        ]);
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . ($this->selectedTenant?->id ?? 'NULL'),
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'slug' => $this->slug,
            'status' => $this->status,
        ];

        if ($this->selectedTenant) {
            $this->selectedTenant->update($data);
            session()->flash('message', 'Tenant updated successfully.');
        } else {
            Tenant::create($data);
            session()->flash('message', 'Tenant created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'email', 'phone', 'slug', 'status']);
        $this->status = 'active';
    }

    public function openDeleteModal(int $tenantId): void
    {
        $this->selectedTenant = Tenant::findOrFail($tenantId);
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->selectedTenant) {
            $this->selectedTenant->delete();
            session()->flash('message', 'Tenant deleted successfully.');
        }
        $this->showDeleteModal = false;
        $this->selectedTenant = null;
    }

    public function render()
    {
        // Only super-admins can access this page
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only Super Admins can manage tenants.');
        }

        return view('livewire.settings.tenant-management', [
            'tenants' => $this->tenants,
        ])->layout('layouts.app');
    }
}