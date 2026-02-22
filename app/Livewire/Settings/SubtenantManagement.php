<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Meeting;
use App\Models\VisitorVisit;
use Livewire\Component;
use Livewire\WithPagination;

class SubtenantManagement extends Component
{
    use WithPagination;

    public ?int $tenantId = null;
    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?Tenant $selectedSubtenant = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $slug = '';
    public string $status = 'active';

    protected $queryString = ['search'];

    public function mount(?int $tenantId = null): void
    {
        // Get the user's current tenant
        $currentUser = auth()->user();
        $currentTenant = $currentUser->getCurrentTenant();

        // Verify user is a tenant admin
        if (!$currentUser->hasRole('admin')) {
            abort(403, 'Access denied. Only tenant admins can manage sub-tenants.');
        }

        // Use the current tenant as the parent tenant
        $this->tenantId = $currentTenant?->id;
    }

    public function getSubtenantsProperty()
    {
        if (!$this->tenantId) {
            return collect();
        }

        $query = Tenant::where('parent_id', $this->tenantId)
            ->withCount(['users', 'visitors', 'meetings'])
            ->orderBy('name');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        return $query->paginate(15);
    }

    public function getParentTenantProperty(): ?Tenant
    {
        if (!$this->tenantId) {
            return null;
        }
        return Tenant::find($this->tenantId);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->selectedSubtenant = null;
        $this->showModal = true;
    }

    public function openEditModal(int $subtenantId): void
    {
        $this->selectedSubtenant = Tenant::findOrFail($subtenantId);

        // Verify this subtenant belongs to the current tenant
        if ($this->selectedSubtenant->parent_id !== $this->tenantId) {
            abort(403, 'You do not have access to this sub-tenant.');
        }

        $this->fill([
            'name' => $this->selectedSubtenant->name,
            'email' => $this->selectedSubtenant->email,
            'phone' => $this->selectedSubtenant->phone ?? '',
            'slug' => $this->selectedSubtenant->slug,
            'status' => $this->selectedSubtenant->status,
        ]);
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . ($this->selectedSubtenant?->id ?? 'NULL'),
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'slug' => $this->slug,
            'status' => $this->status,
            'parent_id' => $this->tenantId,
        ];

        if ($this->selectedSubtenant) {
            $this->selectedSubtenant->update($data);
            session()->flash('message', 'Sub-tenant updated successfully.');
        } else {
            Tenant::create($data);
            session()->flash('message', 'Sub-tenant created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function deleteSubtenant(int $subtenantId): void
    {
        $subtenant = Tenant::findOrFail($subtenantId);

        // Verify this subtenant belongs to the current tenant
        if ($subtenant->parent_id !== $this->tenantId) {
            abort(403, 'You do not have access to this sub-tenant.');
        }

        $this->selectedSubtenant = $subtenant;
        $this->showDeleteModal = true;
    }

    public function confirmDelete(): void
    {
        if (!$this->selectedSubtenant) {
            return;
        }

        // Verify this subtenant belongs to the current tenant
        if ($this->selectedSubtenant->parent_id !== $this->tenantId) {
            abort(403, 'You do not have access to this sub-tenant.');
        }

        $this->selectedSubtenant->delete();
        $this->showDeleteModal = false;
        $this->selectedSubtenant = null;
        session()->flash('message', 'Sub-tenant deleted successfully.');
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'email', 'phone', 'slug', 'status']);
        $this->status = 'active';
        $this->selectedSubtenant = null;
    }

    public function render()
    {
        // Only tenant admins can access this page
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Access denied. Only tenant admins can manage sub-tenants.');
        }

        return view('livewire.settings.subtenant-management', [
            'subtenants' => $this->subtenants,
            'parentTenant' => $this->parentTenant,
        ])->layout('layouts.app');
    }
}