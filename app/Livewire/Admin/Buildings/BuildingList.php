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
    public ?int $editingBuildingId = null;

    // Building fields
    public string $building_name = '';
    public string $building_address = '';
    public bool $building_is_active = true;

    protected function rules(): array
    {
        return [
            'building_name' => 'required|string|max:255',
            'building_address' => 'nullable|string|max:500',
            'building_is_active' => 'boolean',
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

    public function saveBuilding()
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
            $this->showBuildingModal = false;
            $this->resetBuildingForm();
        } else {
            $building = Building::create([
                'name' => $this->building_name,
                'address' => $this->building_address,
                'is_active' => $this->building_is_active,
            ]);
            $this->showBuildingModal = false;
            $this->resetBuildingForm();
            return $this->redirectRoute('admin.buildings.edit', ['building' => $building->id]);
        }
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

    public function resetBuildingForm(): void
    {
        $this->editingBuildingId = null;
        $this->building_name = '';
        $this->building_address = '';
        $this->building_is_active = true;
    }

    public function render()
    {
        $buildings = Building::with(['entrances', 'spaces'])
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