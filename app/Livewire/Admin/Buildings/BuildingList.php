<?php

namespace App\Livewire\Admin\Buildings;

use App\Models\Building;
use App\Models\Entrance;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

#[Layout('layouts.admin')]
class BuildingList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showBuildingModal = false;
    public bool $showEntranceModal = false;
    public ?int $editingBuildingId = null;
    public ?int $editingEntranceId = null;

    // Building fields
    public string $building_name = '';
    public string $building_address = '';
    public bool $building_is_active = true;

    // Entrance fields
    public ?int $entrance_building_id = null;
    public string $entrance_name = '';
    public string $entrance_kiosk_identifier = '';
    public bool $entrance_is_active = true;

    protected function rules(): array
    {
        return [
            'building_name' => 'required|string|max:255',
            'building_address' => 'nullable|string|max:500',
            'building_is_active' => 'boolean',
            'entrance_building_id' => 'required|exists:buildings,id',
            'entrance_name' => 'required|string|max:255',
            'entrance_kiosk_identifier' => 'required|string|max:100|unique:entrances,kiosk_identifier',
            'entrance_is_active' => 'boolean',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function createBuilding(): void
    {
        $this->resetBuildingForm();
        $this->showBuildingModal = true;
    }

    public function editBuilding(int $buildingId): void
    {
        $building = Building::findOrFail($buildingId);
        $this->editingBuildingId = $building->id;
        $this->building_name = $building->name;
        $this->building_address = $building->address ?? '';
        $this->building_is_active = $building->is_active;
        $this->showBuildingModal = true;
    }

    public function saveBuilding(): void
    {
        $this->validate([
            'building_name' => 'required|string|max:255',
            'building_address' => 'nullable|string|max:500',
            'building_is_active' => 'boolean',
        ]);

        if ($this->editingBuildingId) {
            Building::findOrFail($this->editingBuildingId)->update([
                'name' => $this->building_name,
                'address' => $this->building_address,
                'is_active' => $this->building_is_active,
            ]);
            session()->flash('message', 'Building updated successfully.');
        } else {
            Building::create([
                'name' => $this->building_name,
                'address' => $this->building_address,
                'is_active' => $this->building_is_active,
            ]);
            session()->flash('message', 'Building created successfully.');
        }

        $this->showBuildingModal = false;
        $this->resetBuildingForm();
    }

    #[On('confirmDeleteBuilding')]
    public function confirmDeleteBuilding(int $buildingId): void
    {
        $this->deleteBuilding($buildingId);
    }

    public function showDeleteBuildingConfirm(int $buildingId, string $buildingName): void
    {
        $building = Building::find($buildingId);
        if ($building && $building->entrances()->exists()) {
            session()->flash('error', 'Cannot delete building with entrances. Delete entrances first.');
            return;
        }
        $this->dispatch('showConfirmModal', [
            'modalId' => 'delete-building',
            'title' => 'Delete Building',
            'message' => "Are you sure you want to delete \"{$buildingName}\"? This action cannot be undone.",
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmMethod' => 'confirmDeleteBuilding',
            'confirmColor' => 'danger',
            'params' => ['buildingId' => $buildingId],
        ]);
    }

    public function deleteBuilding(int $buildingId): void
    {
        $building = Building::findOrFail($buildingId);
        if ($building->entrances()->exists()) {
            session()->flash('error', 'Cannot delete building with entrances. Delete entrances first.');
            return;
        }
        $building->delete();
        session()->flash('message', 'Building deleted successfully.');
    }

    #[On('confirmDeleteEntrance')]
    public function confirmDeleteEntrance(int $entranceId): void
    {
        $this->deleteEntrance($entranceId);
    }

    public function showDeleteEntranceConfirm(int $entranceId, string $entranceName): void
    {
        $this->dispatch('showConfirmModal', [
            'modalId' => 'delete-entrance',
            'title' => 'Delete Entrance',
            'message' => "Are you sure you want to delete \"{$entranceName}\"? This action cannot be undone.",
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmMethod' => 'confirmDeleteEntrance',
            'confirmColor' => 'danger',
            'params' => ['entranceId' => $entranceId],
        ]);
    }

    public function createEntrance(int $buildingId): void
    {
        $this->resetEntranceForm();
        $this->entrance_building_id = $buildingId;
        $this->entrance_kiosk_identifier = Str::slug(Building::find($buildingId)->name . '-' . Str::random(6));
        $this->showEntranceModal = true;
    }

    public function editEntrance(int $entranceId): void
    {
        $entrance = Entrance::findOrFail($entranceId);
        $this->editingEntranceId = $entrance->id;
        $this->entrance_building_id = $entrance->building_id;
        $this->entrance_name = $entrance->name;
        $this->entrance_kiosk_identifier = $entrance->kiosk_identifier;
        $this->entrance_is_active = $entrance->is_active;
        $this->showEntranceModal = true;
    }

    public function saveEntrance(): void
    {
        $rules = [
            'entrance_building_id' => 'required|exists:buildings,id',
            'entrance_name' => 'required|string|max:255',
            'entrance_kiosk_identifier' => 'required|string|max:100|unique:entrances,kiosk_identifier',
            'entrance_is_active' => 'boolean',
        ];

        if ($this->editingEntranceId) {
            $rules['entrance_kiosk_identifier'] = 'required|string|max:100|unique:entrances,kiosk_identifier,' . $this->editingEntranceId;
        }

        $this->validate($rules);

        if ($this->editingEntranceId) {
            Entrance::findOrFail($this->editingEntranceId)->update([
                'building_id' => $this->entrance_building_id,
                'name' => $this->entrance_name,
                'kiosk_identifier' => $this->entrance_kiosk_identifier,
                'is_active' => $this->entrance_is_active,
            ]);
            session()->flash('message', 'Entrance updated successfully.');
        } else {
            $entrance = Entrance::create([
                'building_id' => $this->entrance_building_id,
                'name' => $this->entrance_name,
                'kiosk_identifier' => $this->entrance_kiosk_identifier,
                'is_active' => $this->entrance_is_active,
            ]);

            // Create default kiosk settings
            $entrance->kioskSetting()->create([
                'welcome_message' => 'Welcome! Please check in below.',
                'background_color' => '#ffffff',
                'primary_color' => '#3b82f6',
                'require_photo' => false,
                'require_signature' => true,
                'show_nda' => false,
            ]);

            session()->flash('message', 'Entrance created successfully.');
        }

        $this->showEntranceModal = false;
        $this->resetEntranceForm();
    }

    public function deleteEntrance(int $entranceId): void
    {
        Entrance::findOrFail($entranceId)->delete();
        session()->flash('message', 'Entrance deleted successfully.');
    }

    public function resetBuildingForm(): void
    {
        $this->editingBuildingId = null;
        $this->building_name = '';
        $this->building_address = '';
        $this->building_is_active = true;
    }

    public function resetEntranceForm(): void
    {
        $this->editingEntranceId = null;
        $this->entrance_building_id = null;
        $this->entrance_name = '';
        $this->entrance_kiosk_identifier = '';
        $this->entrance_is_active = true;
    }

    public function render()
    {
        $buildings = Building::with('entrances')
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('address', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->paginate(10);

        // Stats for header
        $totalBuildings = Building::count();
        $totalEntrances = Entrance::count();
        $activeEntrances = Entrance::where('is_active', true)->count();

        return view('livewire.admin.buildings.building-list', compact(
            'buildings',
            'totalBuildings',
            'totalEntrances',
            'activeEntrances'
        ));
    }
}