<?php

namespace App\Livewire\MeetingRooms;

use App\Models\Building;
use App\Models\AccessPoint;
use App\Models\MeetingRoom;
use Livewire\Component;
use Livewire\WithPagination;

class MeetingRoomList extends Component
{
    use WithPagination;

    public ?int $tenantId = null;
    public string $search = '';
    public bool $showModal = false;
    public ?MeetingRoom $selectedRoom = null;
    public bool $showDeleteConfirm = false;
    
    public string $name = '';
    public string $code = '';
    public ?int $building_id = null;
    public ?int $access_point_id = null;
    public string $location = '';
    public int $capacity = 10;
    public string $description = '';
    public array $amenities = [];
    public string $amenityInput = '';
    public int $is_active = 1;
    
    public $buildings = [];
    public $accessPoints = [];

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
        $this->loadBuildings();
    }

    protected function loadBuildings(): void
    {
        if (!$this->tenantId) {
            return;
        }

        $this->buildings = Building::where('tenant_id', $this->tenantId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function updatedBuildingId(): void
    {
        if ($this->building_id) {
            $this->accessPoints = AccessPoint::where('building_id', $this->building_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
            
            // Update location string
            $building = Building::find($this->building_id);
            $this->location = $building?->name ?? '';
        } else {
            $this->accessPoints = [];
            $this->access_point_id = null;
            $this->location = '';
        }
    }

    public function updatedAccessPointId(): void
    {
        if ($this->access_point_id && $this->building_id) {
            $building = Building::find($this->building_id);
            $accessPoint = AccessPoint::find($this->access_point_id);
            $this->location = trim(($building?->name ?? '') . ' - ' . ($accessPoint?->name ?? ''), ' - ');
        }
    }

    public function addAmenity(): void
    {
        $amenity = trim($this->amenityInput);
        
        if ($amenity && !in_array($amenity, $this->amenities)) {
            $this->amenities[] = $amenity;
        }
        
        $this->amenityInput = '';
    }

    public function removeAmenity(int $index): void
    {
        unset($this->amenities[$index]);
        $this->amenities = array_values($this->amenities);
    }

    public function getMeetingRoomsProperty()
    {
        if (!$this->tenantId) {
            return collect();
        }

        return MeetingRoom::where('tenant_id', $this->tenantId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('location', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->selectedRoom = null;
        $this->showDeleteConfirm = false;
        $this->showModal = true;
    }

    public function openEditModal(int $roomId): void
    {
        $this->selectedRoom = MeetingRoom::findOrFail($roomId);
        $this->fill([
            'name' => $this->selectedRoom->name,
            'code' => $this->selectedRoom->code,
            'location' => $this->selectedRoom->location,
            'capacity' => $this->selectedRoom->capacity,
            'description' => $this->selectedRoom->description,
            'amenities' => $this->selectedRoom->amenities ?? [],
            'is_active' => $this->selectedRoom->is_active ? 1 : 0,
            'building_id' => $this->selectedRoom->building_id,
            'access_point_id' => $this->selectedRoom->access_point_id,
        ]);
        $this->showDeleteConfirm = false;

        // Load access points for the selected building
        if ($this->building_id) {
            $this->accessPoints = AccessPoint::where('building_id', $this->building_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        } else {
            $this->accessPoints = [];
        }

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $data = [
            'name' => $this->name,
            'code' => $this->code,
            'location' => $this->location,
            'capacity' => $this->capacity,
            'description' => $this->description,
            'amenities' => $this->amenities,
            'is_active' => $this->is_active,
            'tenant_id' => $this->tenantId,
            'building_id' => $this->building_id,
            'access_point_id' => $this->access_point_id,
        ];

        if ($this->selectedRoom) {
            $this->selectedRoom->update($data);
            session()->flash('message', 'Meeting room updated successfully.');
        } else {
            MeetingRoom::create($data);
            session()->flash('message', 'Meeting room created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function confirmDelete(): void
    {
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        if ($this->selectedRoom) {
            // Check if there are any scheduled meetings
            $hasUpcomingMeetings = $this->selectedRoom->meetings()
                ->where('start_at', '>', now())
                ->where('status', 'scheduled')
                ->exists();

            if ($hasUpcomingMeetings) {
                $this->addError('delete', 'Cannot delete meeting room with upcoming meetings.');
                $this->showDeleteConfirm = false;
                return;
            }

            $this->selectedRoom->delete();
            session()->flash('message', 'Meeting room deleted successfully.');
        }
        $this->showDeleteConfirm = false;
        $this->showModal = false;
        $this->selectedRoom = null;
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'code', 'building_id', 'access_point_id', 'location', 'capacity', 'description', 'amenities', 'amenityInput', 'is_active']);
        $this->accessPoints = [];
    }

    public function render()
    {
        return view('livewire.meeting-rooms.meeting-room-list', [
            'meetingRooms' => $this->meetingRooms,
        ])->layout('layouts.app');
    }
}