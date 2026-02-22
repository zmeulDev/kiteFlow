<?php

namespace App\Livewire\AccessPoints;

use App\Models\AccessPoint;
use App\Models\Building;
use App\Models\Zone;
use Livewire\Component;
use Livewire\WithPagination;

class AccessPointList extends Component
{
    use WithPagination;

    public ?int $tenantId = null;
    public string $search = '';
    public string $typeFilter = '';
    public ?int $buildingFilter = null; // Filter by building
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public ?AccessPoint $selectedPoint = null;
    public ?int $showKioskUrlFor = null; // Track which access point URL is being shown

    public string $name = '';
    public string $code = '';
    public string $type = 'door';
    public string $direction = 'entry';
    public string $description = '';
    public ?int $building_id = null;
    public ?int $zone_id = null;
    public string $device_id = '';
    public string $ip_address = '';
    public bool $requires_badge = false;
    public bool $is_kiosk_mode = false;
    public bool $is_active = true;

    protected $queryString = ['search', 'typeFilter', 'buildingFilter'];

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

    public function getAccessPointsProperty()
    {
        if (!$this->tenantId) {
            return collect();
        }

        return AccessPoint::with(['building', 'zone'])
            ->where('tenant_id', $this->tenantId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->buildingFilter, function ($query) {
                $query->where('building_id', $this->buildingFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->orderBy('name')
            ->paginate(15);
    }

    public function getBuildingsProperty()
    {
        if (!$this->tenantId) return collect();
        return Building::where('tenant_id', $this->tenantId)->where('is_active', true)->orderBy('name')->get();
    }

    public function getZonesProperty()
    {
        if (!$this->building_id) return collect();
        return Zone::where('building_id', $this->building_id)->where('is_active', true)->orderBy('name')->get();
    }

    public function updatedBuildingId(): void
    {
        $this->zone_id = null;
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->selectedAccessPoint = null;
        $this->showModal = true;
    }

    public function openEditModal(int $pointId): void
    {
        $this->selectedPoint = AccessPoint::findOrFail($pointId);
        $this->fill([
            'name' => $this->selectedPoint->name,
            'code' => $this->selectedPoint->code ?? '',
            'type' => $this->selectedPoint->type ?? 'door',
            'direction' => $this->selectedPoint->direction ?? 'entry',
            'description' => $this->selectedPoint->description ?? '',
            'building_id' => $this->selectedPoint->building_id,
            'zone_id' => $this->selectedPoint->zone_id,
            'device_id' => $this->selectedPoint->device_id ?? '',
            'ip_address' => $this->selectedPoint->ip_address ?? '',
            'requires_badge' => $this->selectedPoint->requires_badge ?? false,
            'is_kiosk_mode' => $this->selectedPoint->is_kiosk_mode ?? false,
            'is_active' => $this->selectedPoint->is_active ?? true,
        ]);
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:door,turnstile,gate,kiosk,other',
            'direction' => 'required|in:entry,exit,both',
        ]);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'direction' => $this->direction,
            'description' => $this->description,
            'building_id' => $this->building_id,
            'zone_id' => $this->zone_id,
            'device_id' => $this->device_id,
            'ip_address' => $this->ip_address,
            'requires_badge' => $this->requires_badge,
            'is_kiosk_mode' => $this->is_kiosk_mode,
            'is_active' => $this->is_active,
            'tenant_id' => $this->tenantId,
        ];

        if ($this->selectedPoint) {
            $this->selectedPoint->update($data);
            session()->flash('message', 'Access point updated successfully.');
        } else {
            AccessPoint::create($data);
            session()->flash('message', 'Access point created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function openDeleteModal(int $pointId): void
    {
        $this->selectedPoint = AccessPoint::findOrFail($pointId);
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->selectedPoint) {
            $this->selectedPoint->delete();
            session()->flash('message', 'Access point deleted successfully.');
        }
        $this->showDeleteModal = false;
        $this->selectedPoint = null;
    }

    public function toggleActive(int $accessPointId): void
    {
        $accessPoint = AccessPoint::findOrFail($accessPointId);
        $accessPoint->update(['is_active' => !$accessPoint->is_active]);
        session()->flash('message', 'Access point ' . ($accessPoint->is_active ? 'activated' : 'deactivated') . '.');
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'code', 'type', 'direction', 'description', 'building_id', 'zone_id', 'device_id', 'ip_address', 'requires_badge', 'is_kiosk_mode', 'is_active']);
        $this->type = 'door';
        $this->direction = 'entry';
        $this->is_active = true;
        $this->is_kiosk_mode = false;
        $this->requires_badge = false;
    }

    /**
     * Get the kiosk URL for an access point
     */
    public function getKioskUrl(AccessPoint $accessPoint): string
    {
        if (!$accessPoint->is_kiosk_mode || !$accessPoint->tenant) {
            return '';
        }

        $tenantSlug = $accessPoint->tenant->slug ?? '';
        return route('kiosk', ['tenantSlug' => $tenantSlug, 'accessPointUuid' => $accessPoint->uuid]);
    }

    /**
     * Get the current tenant for kiosk URL generation
     */
    public function getCurrentTenant(): ?\App\Models\Tenant
    {
        if (!$this->tenantId) {
            return null;
        }
        return \App\Models\Tenant::find($this->tenantId);
    }

    /**
     * Check if the current user can view kiosk
     */
    public function canViewKiosk(AccessPoint $accessPoint): bool
    {
        return $accessPoint->is_kiosk_mode && $accessPoint->is_active;
    }

    /**
     * Toggle the kiosk URL display for an access point
     */
    public function toggleKioskUrl(int $accessPointId): void
    {
        if ($this->showKioskUrlFor === $accessPointId) {
            $this->showKioskUrlFor = null;
        } else {
            $this->showKioskUrlFor = $accessPointId;
        }
    }

    /**
     * Check if kiosk URL is shown for a specific access point
     */
    public function isKioskUrlShown(int $accessPointId): bool
    {
        return $this->showKioskUrlFor === $accessPointId;
    }

    public function render()
    {
        return view('livewire.access-points.access-point-list', [
            'accessPoints' => $this->accessPoints,
            'buildings' => $this->buildings,
            'zones' => $this->zones,
        ])->layout('layouts.app');
    }
}