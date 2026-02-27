<?php

namespace App\Livewire\Admin\Buildings;

use App\Models\Building;
use App\Models\Entrance;
use App\Models\Space;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;

#[Layout('layouts.admin')]
class BuildingEdit extends Component
{
    public Building $building;

    public bool $showEntranceModal = false;
    public bool $showSpaceModal = false;
    public ?int $editingEntranceId = null;
    public ?int $editingSpaceId = null;

    // Building fields
    public string $building_name = '';
    public string $building_address = '';
    public bool $building_is_active = true;

    // Entrance fields
    public string $entrance_name = '';
    public string $entrance_kiosk_identifier = '';
    public bool $entrance_is_active = true;

    // Space fields
    public string $space_name = '';
    public string $space_amenities = '';
    public bool $space_is_active = true;

    public function mount(Building $building): void
    {
        $this->building = $building;
        
        $this->building_name = $building->name;
        $this->building_address = $building->address ?? '';
        $this->building_is_active = $building->is_active;
    }

    protected function rules(): array
    {
        return [
            'building_name' => 'required|string|max:255',
            'building_address' => 'nullable|string|max:500',
            'building_is_active' => 'boolean',
            'entrance_name' => 'required|string|max:255',
            'entrance_kiosk_identifier' => 'required|string|max:100|unique:entrances,kiosk_identifier',
            'entrance_is_active' => 'boolean',
            'space_name' => 'required|string|max:255',
            'space_amenities' => 'nullable|string',
            'space_is_active' => 'boolean',
        ];
    }

    public function saveBuilding(): void
    {
        $this->validate([
            'building_name' => 'required|string|max:255',
            'building_address' => 'nullable|string|max:500',
            'building_is_active' => 'boolean',
        ]);

        $this->building->update([
            'name' => $this->building_name,
            'address' => $this->building_address,
            'is_active' => $this->building_is_active,
        ]);
        
        session()->flash('message', 'Building updated successfully.');
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

    public function createEntrance(): void
    {
        $this->resetEntranceForm();
        $this->entrance_kiosk_identifier = Str::slug($this->building->name . '-' . Str::random(6));
        $this->showEntranceModal = true;
    }

    public function editEntrance(int $entranceId): void
    {
        $entrance = Entrance::findOrFail($entranceId);
        $this->editingEntranceId = $entrance->id;
        $this->entrance_name = $entrance->name;
        $this->entrance_kiosk_identifier = $entrance->kiosk_identifier;
        $this->entrance_is_active = $entrance->is_active;
        $this->showEntranceModal = true;
    }

    public function saveEntrance(): void
    {
        $rules = [
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
                'name' => $this->entrance_name,
                'kiosk_identifier' => $this->entrance_kiosk_identifier,
                'is_active' => $this->entrance_is_active,
            ]);
            session()->flash('message', 'Entrance updated successfully.');
        } else {
            $entrance = Entrance::create([
                'building_id' => $this->building->id,
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
        
        // Refresh building relationships
        $this->building->load('entrances');
    }

    public function deleteEntrance(int $entranceId): void
    {
        Entrance::findOrFail($entranceId)->delete();
        session()->flash('message', 'Entrance deleted successfully.');
        $this->building->load('entrances');
    }

    #[On('confirmDeleteSpace')]
    public function confirmDeleteSpace(int $spaceId): void
    {
        $this->deleteSpace($spaceId);
    }

    public function showDeleteSpaceConfirm(int $spaceId, string $spaceName): void
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        $this->dispatch('showConfirmModal', [
            'modalId' => 'delete-space',
            'title' => 'Delete Space',
            'message' => "Are you sure you want to delete \"{$spaceName}\"? This action cannot be undone.",
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmMethod' => 'confirmDeleteSpace',
            'confirmColor' => 'danger',
            'params' => ['spaceId' => $spaceId],
        ]);
    }

    public function createSpace(): void
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        $this->resetSpaceForm();
        $this->showSpaceModal = true;
    }

    public function editSpace(int $spaceId): void
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        $space = Space::findOrFail($spaceId);
        $this->editingSpaceId = $space->id;
        $this->space_name = $space->name;
        $this->space_amenities = is_array($space->amenities) ? implode(', ', $space->amenities) : '';
        $this->space_is_active = $space->is_active;
        $this->showSpaceModal = true;
    }

    public function saveSpace(): void
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        $this->validate([
            'space_name' => 'required|string|max:255',
            'space_amenities' => 'nullable|string',
            'space_is_active' => 'boolean',
        ]);

        $amenitiesArray = [];
        if (!empty(trim($this->space_amenities))) {
            $amenitiesArray = array_map('trim', explode(',', $this->space_amenities));
            $amenitiesArray = array_filter($amenitiesArray);
        }

        if ($this->editingSpaceId) {
            Space::findOrFail($this->editingSpaceId)->update([
                'name' => $this->space_name,
                'amenities' => empty($amenitiesArray) ? null : array_values($amenitiesArray),
                'is_active' => $this->space_is_active,
            ]);
            session()->flash('message', 'Space updated successfully.');
        } else {
            Space::create([
                'building_id' => $this->building->id,
                'name' => $this->space_name,
                'amenities' => empty($amenitiesArray) ? null : array_values($amenitiesArray),
                'is_active' => $this->space_is_active,
            ]);
            session()->flash('message', 'Space created successfully.');
        }

        $this->showSpaceModal = false;
        $this->resetSpaceForm();
        $this->building->load('spaces');
    }

    public function deleteSpace(int $spaceId): void
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        Space::findOrFail($spaceId)->delete();
        session()->flash('message', 'Space deleted successfully.');
        $this->building->load('spaces');
    }

    public function resetEntranceForm(): void
    {
        $this->editingEntranceId = null;
        $this->entrance_name = '';
        $this->entrance_kiosk_identifier = '';
        $this->entrance_is_active = true;
    }

    public function resetSpaceForm(): void
    {
        $this->editingSpaceId = null;
        $this->space_name = '';
        $this->space_amenities = '';
        $this->space_is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.buildings.building-edit', [
            'entrances' => $this->building->entrances()->get(),
            'spaces' => $this->building->spaces()->get()
        ]);
    }
}
