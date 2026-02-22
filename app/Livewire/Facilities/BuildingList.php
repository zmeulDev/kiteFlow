<?php

namespace App\Livewire\Facilities;

use App\Models\Building;
use Livewire\Component;
use Livewire\WithPagination;

class BuildingList extends Component
{
    use WithPagination;

    public ?int $tenantId = null;
    public string $search = '';
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?Building $selectedBuilding = null;
    
    public string $name = '';
    public string $code = '';
    public string $address = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $country = '';
    public bool $is_active = true;

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

    public function getBuildingsProperty()
    {
        if (!$this->tenantId) {
            return collect();
        }

        return Building::withCount(['zones', 'accessPoints', 'parkingSpots'])
            ->where('tenant_id', $this->tenantId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('address', 'like', "%{$this->search}%")
                        ->orWhere('city', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->selectedBuilding = null;
        $this->showModal = true;
    }

    public function openEditModal(int $buildingId): void
    {
        $this->selectedBuilding = Building::findOrFail($buildingId);
        $this->fill([
            'name' => $this->selectedBuilding->name,
            'code' => $this->selectedBuilding->code ?? '',
            'address' => $this->selectedBuilding->address ?? '',
            'city' => $this->selectedBuilding->city ?? '',
            'state' => $this->selectedBuilding->state ?? '',
            'postal_code' => $this->selectedBuilding->postal_code ?? '',
            'country' => $this->selectedBuilding->country ?? '',
            'is_active' => $this->selectedBuilding->is_active ?? true,
        ]);
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
        ]);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'is_active' => $this->is_active,
            'tenant_id' => $this->tenantId,
        ];

        if ($this->selectedBuilding) {
            $this->selectedBuilding->update($data);
            session()->flash('message', 'Building updated successfully.');
        } else {
            Building::create($data);
            session()->flash('message', 'Building created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function openDeleteModal(int $buildingId): void
    {
        $this->selectedBuilding = Building::findOrFail($buildingId);
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->selectedBuilding) {
            $this->selectedBuilding->delete();
            session()->flash('message', 'Building deleted successfully.');
        }
        $this->showDeleteModal = false;
        $this->selectedBuilding = null;
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'code', 'address', 'city', 'state', 'postal_code', 'country', 'is_active']);
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.facilities.building-list', [
            'buildings' => $this->buildings,
        ])->layout('layouts.app');
    }
}