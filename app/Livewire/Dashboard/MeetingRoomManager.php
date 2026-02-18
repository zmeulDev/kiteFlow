<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\MeetingRoom;
use App\Models\Location;

class MeetingRoomManager extends Component
{
    public $rooms;
    public $name;
    public $location_id;
    public $capacity = 4;
    public $amenities = [];
    public $is_active = true;

    public $new_amenity;

    #[On('location-added')]
    public function refreshLocations()
    {
        // Refresh component
    }

    public function mount()
    {
        $this->loadRooms();
    }

    public function loadRooms()
    {
        $this->rooms = auth()->user()->tenant->meetingRooms()->with('location')->get();
    }

    public function addAmenity()
    {
        if ($this->new_amenity) {
            $this->amenities[] = $this->new_amenity;
            $this->new_amenity = '';
        }
    }

    public function removeAmenity($index)
    {
        unset($this->amenities[$index]);
        $this->amenities = array_values($this->amenities);
    }

    public function createRoom()
    {
        $this->validate([
            'name' => 'required|string|min:2',
            'location_id' => 'nullable|exists:locations,id',
            'capacity' => 'required|integer|min:1',
        ]);

        auth()->user()->tenant->meetingRooms()->create([
            'name' => $this->name,
            'location_id' => $this->location_id,
            'capacity' => $this->capacity,
            'amenities' => $this->amenities,
            'is_active' => $this->is_active,
        ]);

        $this->reset(['name', 'location_id', 'capacity', 'amenities', 'is_active']);
        $this->loadRooms();
        $this->dispatch('notify', type: 'success', message: 'Meeting room created successfully!');
    }

    public function deleteRoom($id)
    {
        $room = auth()->user()->tenant->meetingRooms()->findOrFail($id);
        $room->delete();
        $this->loadRooms();
        $this->dispatch('notify', type: 'success', message: 'Meeting room deleted.');
    }

    public function render()
    {
        return view('livewire.dashboard.meeting-room-manager', [
            'locations' => auth()->user()->tenant->locations
        ]);
    }
}
