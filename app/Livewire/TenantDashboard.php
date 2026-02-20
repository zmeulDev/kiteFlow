<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Tenant;
use App\Models\Building;
use App\Models\MeetingRoom;
use App\Models\SubTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TenantDashboard extends Component
{
    use WithFileUploads;

    public ?Tenant $tenant;
    public string $tab = 'overview';
    public array $stats = [];
    
    // Settings form
    public string $name = '';
    public string $address = '';
    public string $city = '';
    public string $country = '';
    public string $phone = '';
    public string $email = '';
    public string $contact_person = '';
    public int $gdpr_retention_months = 6;
    public string $nda_text = '';
    public string $terms_text = '';
    public $logo; // For file upload
    
    // Meeting Rooms
    public $rooms = [];
    public $buildings = [];
    public $room_id = null;
    public $room_building_id = null;
    public $room_name = '';
    public $room_capacity = 10;
    public $room_floor = '';
    public $room_is_active = true;
    public $isEditingRoom = false;

    // Buildings
    public $building_id = null;
    public $building_name = '';
    public $building_address = '';
    public $isEditingBuilding = false;

    // Sub-Tenants
    public $subtenants = [];
    public $subtenant_id = null;
    public $subtenant_name = '';
    public $subtenant_slug = '';
    public $subtenant_contact_person = '';
    public $subtenant_email = '';
    public $subtenant_phone = '';
    public $subtenant_is_active = true;
    public $isEditingSubTenant = false;

    public function mount()
    {
        $this->tenant = Auth::user()->tenant;
        if ($this->tenant) {
            $this->loadStats();
            $this->loadSettings();
            $this->loadBuildings();
            $this->loadRooms();
            $this->loadSubTenants();
        }
    }

    public function loadStats()
    {
        if (!$this->tenant) return;

        $peakHours = $this->tenant->visits()
            ->selectRaw('HOUR(scheduled_start) as hour, COUNT(*) as count')
            ->whereDate('scheduled_start', '>=', now()->subDays(30))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->mapWithKeys(function ($item) {
                $formattedHour = str_pad($item->hour, 2, '0', STR_PAD_LEFT) . ':00';
                return [$formattedHour => $item->count];
            })
            ->toArray();

        $this->stats = [
            'total_visits_today' => $this->tenant->visits()
                ->whereDate('scheduled_start', today())->count(),
            'checked_in' => $this->tenant->visits()
                ->where('status', 'checked_in')->count(),
            'total_visitors' => $this->tenant->visitors()->count(),
            'total_rooms' => $this->tenant->meetingRooms()->count(),
            'peak_hours' => $peakHours,
            'total_buildings' => $this->tenant->buildings()->count(),
        ];
    }

    public function loadSettings()
    {
        if (!$this->tenant) return;

        $this->name = $this->tenant->name;
        $this->address = $this->tenant->address ?? '';
        $this->city = $this->tenant->city ?? '';
        $this->country = $this->tenant->country ?? '';
        $this->phone = $this->tenant->phone ?? '';
        $this->email = $this->tenant->email ?? '';
        $this->contact_person = $this->tenant->contact_person ?? '';
        $this->gdpr_retention_months = $this->tenant->gdpr_retention_months ?? 6;
        $this->nda_text = $this->tenant->nda_text ?? '';
        $this->terms_text = $this->tenant->terms_text ?? '';
    }

    public function saveSettings()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'gdpr_retention_months' => 'required|integer|min:1',
            'nda_text' => 'nullable|string',
            'terms_text' => 'nullable|string',
            'logo' => 'nullable|image|max:2048', // optional max 2MB
        ]);

        $data = [
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'contact_person' => $this->contact_person,
            'gdpr_retention_months' => $this->gdpr_retention_months,
            'nda_text' => $this->nda_text,
            'terms_text' => $this->terms_text,
        ];

        if ($this->logo) {
            $path = $this->logo->store('tenant-logos', 'public');
            $data['logo_path'] = $path;
        }

        $this->tenant->update($data);

        session()->flash('message', 'Settings saved successfully');
    }

    public function loadBuildings()
    {
        $this->buildings = $this->tenant->buildings()->get();
        if ($this->buildings->isNotEmpty() && !$this->room_building_id) {
            $this->room_building_id = $this->buildings->first()->id;
        }
    }

    public function loadRooms()
    {
        $this->rooms = $this->tenant->meetingRooms()->with('building')->get();
    }

    public function editRoom($id)
    {
        $room = MeetingRoom::findOrFail($id);
        if ($room->tenant_id !== $this->tenant->id) return;
        
        $this->isEditingRoom = true;
        $this->room_id = $room->id;
        $this->room_building_id = $room->building_id;
        $this->room_name = $room->name;
        $this->room_capacity = $room->capacity;
        $this->room_floor = $room->floor;
        $this->room_is_active = $room->is_active;
    }

    public function saveRoom()
    {
        $this->validate([
            'room_name' => 'required|string|max:255',
            'room_capacity' => 'required|integer|min:1',
            'room_building_id' => 'required|exists:buildings,id'
        ]);

        if ($this->room_id) {
            $room = MeetingRoom::findOrFail($this->room_id);
            if ($room->tenant_id === $this->tenant->id) {
                $room->update([
                    'building_id' => $this->room_building_id,
                    'name' => $this->room_name,
                    'capacity' => $this->room_capacity,
                    'floor' => $this->room_floor,
                    'is_active' => $this->room_is_active,
                ]);
            }
        } else {
            MeetingRoom::create([
                'tenant_id' => $this->tenant->id,
                'building_id' => $this->room_building_id,
                'name' => $this->room_name,
                'capacity' => $this->room_capacity,
                'floor' => $this->room_floor,
                'is_active' => $this->room_is_active,
            ]);
        }

        $this->loadRooms();
        $this->resetRoomForm();
        session()->flash('message', 'Room saved successfully');
    }

    public function deleteRoom($id)
    {
        $room = MeetingRoom::findOrFail($id);
        if ($room->tenant_id === $this->tenant->id) {
            $room->delete();
            $this->loadRooms();
            session()->flash('message', 'Room deleted successfully');
        }
    }

    public function resetRoomForm()
    {
        $this->isEditingRoom = false;
        $this->room_id = null;
        $this->room_name = '';
        $this->room_capacity = 10;
        $this->room_floor = '';
        $this->room_is_active = true;
        if ($this->buildings->isNotEmpty()) {
            $this->room_building_id = $this->buildings->first()->id;
        }
    }

    public function editBuilding($id)
    {
        $building = Building::findOrFail($id);
        if ($building->tenant_id !== $this->tenant->id) return;
        
        $this->isEditingBuilding = true;
        $this->building_id = $building->id;
        $this->building_name = $building->name;
        $this->building_address = $building->address ?? '';
    }

    public function saveBuilding()
    {
        $this->validate([
            'building_name' => 'required|string|max:255',
            'building_address' => 'nullable|string|max:500',
        ]);

        if ($this->building_id) {
            $building = Building::findOrFail($this->building_id);
            if ($building->tenant_id === $this->tenant->id) {
                $building->update([
                    'name' => $this->building_name,
                    'address' => $this->building_address,
                ]);
            }
        } else {
            Building::create([
                'tenant_id' => $this->tenant->id,
                'name' => $this->building_name,
                'address' => $this->building_address,
            ]);
        }

        $this->loadBuildings();
        $this->resetBuildingForm();
        session()->flash('message', 'Building saved successfully');
    }

    public function deleteBuilding($id)
    {
        $building = Building::findOrFail($id);
        if ($building->tenant_id === $this->tenant->id) {
            $building->delete();
            $this->loadBuildings();
            
            // Reload rooms since a building was deleted
            $this->loadRooms();
            session()->flash('message', 'Building deleted successfully');
        }
    }

    public function resetBuildingForm()
    {
        $this->isEditingBuilding = false;
        $this->building_id = null;
        $this->building_name = '';
        $this->building_address = '';
    }

    public function loadSubTenants()
    {
        $this->subtenants = $this->tenant->subTenants()->get();
    }

    public function editSubTenant($id)
    {
        $sub = SubTenant::findOrFail($id);
        if ($sub->tenant_id !== $this->tenant->id) return;

        $this->isEditingSubTenant = true;
        $this->subtenant_id = $sub->id;
        $this->subtenant_name = $sub->name;
        $this->subtenant_slug = $sub->slug;
        $this->subtenant_contact_person = $sub->contact_person;
        $this->subtenant_email = $sub->email;
        $this->subtenant_phone = $sub->phone;
        $this->subtenant_is_active = $sub->is_active;
    }

    public function saveSubTenant()
    {
        $this->validate([
            'subtenant_name' => 'required|string|max:255',
            'subtenant_slug' => 'required|string|max:255|alpha_dash',
            'subtenant_email' => 'nullable|email|max:255',
        ]);

        if ($this->subtenant_id) {
            $sub = SubTenant::findOrFail($this->subtenant_id);
            if ($sub->tenant_id === $this->tenant->id) {
                $sub->update([
                    'name' => $this->subtenant_name,
                    'slug' => $this->subtenant_slug,
                    'contact_person' => $this->subtenant_contact_person,
                    'email' => $this->subtenant_email,
                    'phone' => $this->subtenant_phone,
                    'is_active' => $this->subtenant_is_active,
                ]);
            }
        } else {
            SubTenant::create([
                'tenant_id' => $this->tenant->id,
                'name' => $this->subtenant_name,
                'slug' => $this->subtenant_slug,
                'contact_person' => $this->subtenant_contact_person,
                'email' => $this->subtenant_email,
                'phone' => $this->subtenant_phone,
                'is_active' => $this->subtenant_is_active,
            ]);
        }

        $this->loadSubTenants();
        $this->resetSubTenantForm();
        session()->flash('message', 'Sub-tenant saved successfully');
    }

    public function deleteSubTenant($id)
    {
        $sub = SubTenant::findOrFail($id);
        if ($sub->tenant_id === $this->tenant->id) {
            $sub->delete();
            $this->loadSubTenants();
            session()->flash('message', 'Sub-tenant deleted successfully');
        }
    }

    public function resetSubTenantForm()
    {
        $this->isEditingSubTenant = false;
        $this->subtenant_id = null;
        $this->subtenant_name = '';
        $this->subtenant_slug = '';
        $this->subtenant_contact_person = '';
        $this->subtenant_email = '';
        $this->subtenant_phone = '';
        $this->subtenant_is_active = true;
    }

    public function render()
    {
        return view('livewire.tenant-dashboard');
    }
}
