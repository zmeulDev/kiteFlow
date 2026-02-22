<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use App\Models\Tenant;
use App\Models\MeetingRoom;
use App\Models\Visit;
use App\Models\Location;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public ?Tenant $tenant = null;
    public string $nda_text = '';
    public int $data_retention_days = 180;
    public $logo = null;
    
    public $locations = [];
    public string $newLocationName = '';
    public ?int $editingLocationId = null;
    public string $editLocationName = '';

    public $rooms = [];
    public $subTenants = [];
    public string $newSubTenantName = '';
    public string $newSubTenantDomain = '';
    public string $activeTab = 'settings';
    public array $stats = [];
    
    public string $newRoomName = '';
    public int $newRoomCapacity = 1;
    public $newRoomLocationId = '';
    public bool $newRoomIsAvailable = true;
    public array $newRoomAmenities = ['tv' => false, 'whiteboard' => false, 'ac' => false, 'video' => false];
    
    public ?int $editingRoomId = null;
    public string $editRoomName = '';
    public int $editRoomCapacity = 1;
    public $editRoomLocationId = '';
    public bool $editRoomIsAvailable = true;
    public array $editRoomAmenities = ['tv' => false, 'whiteboard' => false, 'ac' => false, 'video' => false];
    
    public ?int $editingSubTenantId = null;
    public string $editSubTenantName = '';
    public string $editSubTenantDomain = '';
    public string $editSubTenantContractStart = '';
    public string $editSubTenantContractEnd = '';
    public string $newSubTenantContractStart = '';
    public string $newSubTenantContractEnd = '';

    public bool $isSubTenant = false;

    // User Management
    public $users = [];
    public $roles = [];
    public $newUser = ['name' => '', 'email' => '', 'password' => '', 'role' => ''];
    public bool $showCreateUserModal = false;
    public bool $showEditUserModal = false;
    public ?int $editingUserId = null;
    public array $editUserData = ['name' => '', 'email' => '', 'role' => '', 'is_active' => true];

    public bool $showConfirmModal = false;
    public string $confirmActionType = '';
    public ?int $confirmId = null;
    public string $confirmMessage = '';

    public function mount() {
        if (request()->has('tenant_id')) {
            $this->tenant = Tenant::findOrFail(request('tenant_id'));
        } else {
            $this->tenant = Tenant::firstOrCreate(
                ['domain' => 'demo'],
                ['name' => 'Demo Tenant', 'nda_text' => 'Demo NDA']
            );
        }

        $this->isSubTenant = $this->tenant->parent_id !== null;

        // Set default tab based on tenant type
        if ($this->isSubTenant) {
            $this->activeTab = 'details';
        }

        $this->nda_text = $this->tenant->nda_text ?? '';
        $this->data_retention_days = $this->tenant->data_retention_days ?? 180;
        
        $this->loadLocations();
        $this->loadRooms();
        $this->loadSubTenants();
        $this->loadStats();
        $this->loadUsers();
    }

    public function loadLocations() {
        if (!$this->tenant) {
            return;
        }
        $this->locations = $this->tenant->locations()->get();
    }

    public function loadRooms() {
        if (!$this->tenant) {
            return;
        }
        $this->rooms = $this->tenant->meetingRooms()->get();
    }

    public function loadSubTenants() {
        if (!$this->tenant) {
            return;
        }
        $this->subTenants = $this->tenant->subTenants()
            ->with(['contacts' => fn($query) => $query->where('is_primary', true)])
            ->withCount(['users', 'visits'])
            ->get();
    }

    public function loadStats() {
        if (!$this->tenant) {
            return;
        }

        $this->stats = $this->tenant->visits()
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->take(7)
            ->get()
            ->toArray();

        $this->stats['totalLocations'] = $this->tenant->locations()->count();
        $this->stats['totalRooms'] = $this->tenant->meetingRooms()->count();
        $this->stats['totalVisitors'] = $this->tenant->visits()->count();
    }

    public function loadUsers() {
        if (!$this->tenant) {
            return;
        }
        $this->users = $this->tenant->users()->with('allTenantRoles')->get();
        $this->roles = Role::where('name', '!=', 'SuperAdmin')->get();
    }

    // User Management Methods
    public function createUser() {
        if (!$this->tenant) {
            return;
        }

        $this->validate([
            'newUser.name' => 'required|string|max:255',
            'newUser.email' => 'required|email|unique:users,email',
            'newUser.password' => 'required|min:8',
            'newUser.role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $this->newUser['name'],
            'email' => $this->newUser['email'],
            'password' => Hash::make($this->newUser['password']),
            'tenant_id' => $this->tenant->id,
        ]);

        setPermissionsTeamId($this->tenant->id);
        $user->assignRole($this->newUser['role']);

        session()->flash('user_message', 'User created successfully.');
        $this->newUser = ['name' => '', 'email' => '', 'password' => '', 'role' => ''];
        $this->showCreateUserModal = false;
        $this->loadUsers();
    }

    public function openCreateUserModal() {
        $this->newUser = ['name' => '', 'email' => '', 'password' => '', 'role' => ''];
        $this->showCreateUserModal = true;
    }

    public function closeCreateUserModal() {
        $this->showCreateUserModal = false;
    }

    public function openEditUserModal($id) {
        $user = User::with('allTenantRoles')->findOrFail($id);
        $this->editingUserId = $id;
        $this->editUserData = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->allTenantRoles->first()?->name ?? '',
            'is_active' => $user->is_active,
        ];
        $this->showEditUserModal = true;
    }

    public function closeEditUserModal() {
        $this->showEditUserModal = false;
        $this->editingUserId = null;
    }

    public function updateUser() {
        if (!$this->tenant) {
            return;
        }

        $this->validate([
            'editUserData.name' => 'required|string|max:255',
            'editUserData.email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'editUserData.role' => 'required|exists:roles,name',
            'editUserData.is_active' => 'boolean',
        ]);

        $user = User::findOrFail($this->editingUserId);
        $user->update([
            'name' => $this->editUserData['name'],
            'email' => $this->editUserData['email'],
            'is_active' => $this->editUserData['is_active'],
        ]);

        setPermissionsTeamId($this->tenant->id);
        $user->syncRoles([$this->editUserData['role']]);

        session()->flash('user_message', 'User updated successfully.');
        $this->showEditUserModal = false;
        $this->editingUserId = null;
        $this->loadUsers();
    }

    public function deleteUser($id) {
        if (!$this->tenant) {
            return;
        }

        if (auth()->id() === $id) {
            session()->flash('user_message', 'You cannot delete yourself.');
            return;
        }

        $user = User::findOrFail($id);
        $user->delete();
        session()->flash('user_message', 'User deleted successfully.');
        $this->loadUsers();
    }

    public function saveSettings() {
        $this->validate([
            'logo' => 'nullable|image|max:2048', // 2MB Max
        ]);

        $data = [
            'nda_text' => $this->nda_text,
            'data_retention_days' => $this->data_retention_days,
        ];

        if ($this->logo) {
            if ($this->tenant->logo_path) {
                Storage::disk('public')->delete($this->tenant->logo_path);
            }
            $path = $this->logo->store('tenant-logos', 'public');
            $data['logo_path'] = $path;
        }

        $this->tenant->update($data);
        $this->tenant->refresh();
        session()->flash('message', 'Facility configuration saved successfully.');
    }

    public function addLocation() {
        $this->validate(['newLocationName' => 'required|string|max:255']);
        $this->tenant->locations()->create(['name' => $this->newLocationName]);
        $this->newLocationName = '';
        $this->loadLocations();
        session()->flash('location_message', 'Building/Location added successfully.');
    }

    public function editLocation($id) {
        $location = Location::findOrFail($id);
        $this->editingLocationId = $id;
        $this->editLocationName = $location->name;
    }

    public function updateLocation() {
        $this->validate(['editLocationName' => 'required|string|max:255']);
        $location = Location::findOrFail($this->editingLocationId);
        $location->update(['name' => $this->editLocationName]);
        $this->editingLocationId = null;
        $this->loadLocations();
        session()->flash('location_message', 'Building/Location updated.');
    }

    public function cancelEditLocation() {
        $this->editingLocationId = null;
    }

    public function deleteLocation($id) {
        $location = Location::findOrFail($id);
        $location->delete();
        $this->loadLocations();
        $this->loadRooms(); // Rooms cascade, so refresh
        session()->flash('location_message', 'Location permanently deleted.');
    }

    public function addRoom() {
        $this->validate([
            'newRoomName' => 'required|string', 
            'newRoomCapacity' => 'required|integer|min:1', 
            'newRoomLocationId' => 'required|exists:locations,id'
        ]);
        
        $this->tenant->meetingRooms()->create([
            'location_id' => $this->newRoomLocationId,
            'name' => $this->newRoomName,
            'capacity' => $this->newRoomCapacity,
            'is_available' => $this->newRoomIsAvailable,
            'amenities' => $this->newRoomAmenities,
        ]);
        
        $this->newRoomName = '';
        $this->newRoomCapacity = 1;
        $this->newRoomLocationId = '';
        $this->newRoomIsAvailable = true;
        $this->newRoomAmenities = ['tv' => false, 'whiteboard' => false, 'ac' => false, 'video' => false];
        
        $this->loadRooms();
        session()->flash('room_message', 'Room added successfully.');
    }

    public function editRoom($id) {
        $room = MeetingRoom::findOrFail($id);
        $this->editingRoomId = $id;
        $this->editRoomName = $room->name;
        $this->editRoomCapacity = $room->capacity;
        $this->editRoomLocationId = $room->location_id;
        $this->editRoomIsAvailable = $room->is_available;
        $this->editRoomAmenities = array_merge(['tv' => false, 'whiteboard' => false, 'ac' => false, 'video' => false], $room->amenities ?? []);
    }

    public function updateRoom() {
        $this->validate([
            'editRoomName' => 'required|string|max:255',
            'editRoomCapacity' => 'required|integer|min:1',
            'editRoomLocationId' => 'required|exists:locations,id',
        ]);

        $room = MeetingRoom::findOrFail($this->editingRoomId);
        $room->update([
            'name' => $this->editRoomName,
            'capacity' => $this->editRoomCapacity,
            'location_id' => $this->editRoomLocationId,
            'is_available' => $this->editRoomIsAvailable,
            'amenities' => $this->editRoomAmenities,
        ]);

        $this->editingRoomId = null;
        $this->loadRooms();
        session()->flash('room_message', 'Room updated successfully.');
    }

    public function cancelEditRoom() {
        $this->editingRoomId = null;
    }

    public function deleteRoom($id) {
        $room = MeetingRoom::findOrFail($id);
        $room->delete();
        $this->loadRooms();
        session()->flash('room_message', 'Room deleted permanently.');
    }

    public function addSubTenant() {
        $this->validate([
            'newSubTenantName' => 'required|string|max:255',
            'newSubTenantDomain' => 'nullable|string|max:255|unique:tenants,domain',
            'newSubTenantContractStart' => 'nullable|date',
            'newSubTenantContractEnd' => 'nullable|date|after_or_equal:newSubTenantContractStart',
        ]);
        $this->tenant->subTenants()->create([
            'name' => $this->newSubTenantName,
            'domain' => $this->newSubTenantDomain,
            'contract_start_date' => $this->newSubTenantContractStart ? Carbon::parse($this->newSubTenantContractStart) : null,
            'contract_end_date' => $this->newSubTenantContractEnd ? Carbon::parse($this->newSubTenantContractEnd) : null,
            'nda_text' => $this->tenant->nda_text, // Inherit NDA text by default
            'data_retention_days' => $this->tenant->data_retention_days,
        ]);
        $this->newSubTenantName = '';
        $this->newSubTenantDomain = '';
        $this->newSubTenantContractStart = '';
        $this->newSubTenantContractEnd = '';
        $this->loadSubTenants();
        session()->flash('subtenant_message', 'Sub-Tenant workspace created successfully.');
    }

    public function editSubTenant($id) {
        $subTenant = Tenant::findOrFail($id);
        $this->editingSubTenantId = $id;
        $this->editSubTenantName = $subTenant->name;
        $this->editSubTenantDomain = $subTenant->domain ?? '';
        $this->editSubTenantContractStart = $subTenant->contract_start_date ? $subTenant->contract_start_date->format('Y-m-d') : '';
        $this->editSubTenantContractEnd = $subTenant->contract_end_date ? $subTenant->contract_end_date->format('Y-m-d') : '';
    }

    public function updateSubTenant() {
        $this->validate([
            'editSubTenantName' => 'required|string|max:255',
            'editSubTenantDomain' => ['nullable', 'string', 'max:255', Rule::unique('tenants', 'domain')->ignore($this->editingSubTenantId)],
            'editSubTenantContractStart' => 'nullable|date',
            'editSubTenantContractEnd' => 'nullable|date|after_or_equal:editSubTenantContractStart',
        ]);

        $subTenant = Tenant::findOrFail($this->editingSubTenantId);
        $subTenant->update([
            'name' => $this->editSubTenantName,
            'domain' => $this->editSubTenantDomain,
            'contract_start_date' => $this->editSubTenantContractStart ? Carbon::parse($this->editSubTenantContractStart) : null,
            'contract_end_date' => $this->editSubTenantContractEnd ? Carbon::parse($this->editSubTenantContractEnd) : null,
        ]);

        $this->editingSubTenantId = null;
        $this->loadSubTenants();
        session()->flash('subtenant_message', 'Sub-Tenant data updated successfully.');
    }

    public function cancelEditSubTenant() {
        $this->editingSubTenantId = null;
    }

    public function deleteSubTenant($id) {
        $subTenant = Tenant::findOrFail($id);
        $subTenant->delete();
        $this->loadSubTenants();
        session()->flash('subtenant_message', 'Sub-Tenant deleted permanently.');
    }

    public function setTab($tab) {
        $this->activeTab = $tab;

        // Ensure data is loaded for the selected tab
        if ($tab === 'users') {
            $this->loadUsers();
        }
    }

    public function confirmAction($actionType, $id, $message) {
        $this->confirmActionType = $actionType;
        $this->confirmId = $id;
        $this->confirmMessage = $message;
        $this->showConfirmModal = true;
    }

    public function executeAction() {
        if ($this->confirmActionType === 'deleteRoom' && $this->confirmId) {
            $this->deleteRoom($this->confirmId);
        } elseif ($this->confirmActionType === 'deleteSubTenant' && $this->confirmId) {
            $this->deleteSubTenant($this->confirmId);
        } elseif ($this->confirmActionType === 'deleteUser' && $this->confirmId) {
            $this->deleteUser($this->confirmId);
        }

        $this->showConfirmModal = false;
        $this->confirmActionType = '';
        $this->confirmId = null;
        $this->confirmMessage = '';
    }

    public function closeConfirmModal() {
        $this->showConfirmModal = false;
        $this->confirmActionType = '';
        $this->confirmId = null;
        $this->confirmMessage = '';
    }
};
?>

<div class="flex h-screen w-full overflow-hidden antialiased">
    <!-- Professional Sidebar -->
    <aside class="w-64 border-r border-gray-200 flex flex-col justify-between hidden md:flex bg-white">
        <div>
            <div class="p-6 border-b border-gray-200">
                <h1 class="text-xl font-extrabold tracking-tight mb-1 text-gray-900">VisiFlow Admin</h1>
                <p class="text-xs text-[#9CA3AF] uppercase font-semibold letter-spacing-wide">{{ $tenant->name }}</p>
                @if($tenant->domain)
                    <p class="text-xs text-[#6B7280] mt-1">{{ $tenant->domain }}</p>
                @endif
            </div>
            
            <nav class="p-4 space-y-2">
                @if($isSubTenant)
                    <button wire:click="setTab('details')" class="w-full text-left px-4 py-3 text-sm font-semibold transition-all duration-150 rounded-lg {{ $activeTab === 'details' ? 'bg-[#FF4B4B]/10 text-[#FF4B4B]' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                        Business Details
                    </button>
                @else
                    <button wire:click="setTab('settings')" class="w-full text-left px-4 py-3 text-sm font-semibold transition-all duration-150 rounded-lg {{ $activeTab === 'settings' ? 'bg-[#FF4B4B]/10 text-[#FF4B4B]' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                        Settings & Rooms
                    </button>
                    <button wire:click="setTab('users')" class="w-full text-left px-4 py-3 text-sm font-semibold transition-all duration-150 rounded-lg {{ $activeTab === 'users' ? 'bg-[#FF4B4B]/10 text-[#FF4B4B]' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                        Users
                    </button>
                    <button wire:click="setTab('subtenants')" class="w-full text-left px-4 py-3 text-sm font-semibold transition-all duration-150 rounded-lg {{ $activeTab === 'subtenants' ? 'bg-[#FF4B4B]/10 text-[#FF4B4B]' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                        Sub-Tenants
                    </button>
                @endif
                <button wire:click="setTab('analytics')" class="w-full text-left px-4 py-3 text-sm font-semibold transition-all duration-150 rounded-lg {{ $activeTab === 'analytics' ? 'bg-[#FF4B4B]/10 text-[#FF4B4B]' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    Analytics
                </button>
                <a href="/admin/visitors" class="block w-full text-left px-4 py-3 text-sm font-semibold transition-all duration-150 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100">
                    Visitor Profiles
                </a>
            </nav>
            
            <!-- Real-Time Facility Metrics -->
            <div class="px-6 py-4 mt-4 border-t border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Facility Metrics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center group">
                        <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors">Physical Sites</span>
                        <span class="text-sm font-bold text-gray-900 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ number_format($stats['totalLocations'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center group">
                        <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors">Managed Rooms</span>
                        <span class="text-sm font-bold text-gray-900 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ number_format($stats['totalRooms'] ?? 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center group">
                        <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900 transition-colors">Total Check-Ins</span>
                        <span class="text-sm font-bold text-gray-900 bg-gray-100 px-2.5 py-0.5 rounded-full">{{ number_format($stats['totalVisitors'] ?? 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="p-4 border-t border-[#374151]">
            <a href="/kiosk/{{ $tenant->id }}" target="_blank" class="w-full btn btn-outline flex justify-center text-xs">Launch Kiosk</a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-8 lg:p-12 relative animate-fade-in-up">

        <!-- Tab Content Routing -->
        @if($activeTab === 'details')
            <div class="max-w-5xl mx-auto space-y-8 animate-fade-in-up">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight mb-2">Business Details</h2>
                    <p class="text-gray-500">View your organization details and contract information.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Business Information Card -->
                    <div class="card p-8">
                        <h3 class="text-xl font-bold mb-6 border-b border-gray-200 pb-4 text-gray-900">Organization Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Business Name</label>
                                <p class="text-lg font-semibold text-gray-900 mt-1">{{ $tenant->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Domain</label>
                                <p class="text-gray-700 mt-1">{{ $tenant->domain ?? 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Status</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $tenant->status === 'Active' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $tenant->status === 'Active' ? 'bg-green-500' : 'bg-yellow-500' }}"></span>
                                        {{ $tenant->status }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contract Details Card -->
                    <div class="card p-8">
                        <h3 class="text-xl font-bold mb-6 border-b border-gray-200 pb-4 text-gray-900">Contract Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Contract Start</label>
                                <p class="text-gray-700 mt-1">{{ $tenant->contract_start_date ? $tenant->contract_start_date->format('F j, Y') : 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Contract End</label>
                                <p class="text-gray-700 mt-1">{{ $tenant->contract_end_date ? $tenant->contract_end_date->format('F j, Y') : 'Not set' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Data Retention</label>
                                <p class="text-gray-700 mt-1">{{ $tenant->data_retention_days ?? 180 }} days</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Section -->
                <div class="card p-8">
                    <h3 class="text-xl font-bold mb-6 border-b border-gray-200 pb-4 text-gray-900">Users</h3>
                    @php $tenantUsers = $tenant->users()->withCount('visits')->get(); @endphp
                    @if(count($tenantUsers) > 0)
                        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
                            <table class="data-grid w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Name</th>
                                        <th class="text-left font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Email</th>
                                        <th class="text-center font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Visits</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($tenantUsers as $user)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="font-bold text-gray-900 p-4">{{ $user->name }}</td>
                                            <td class="text-gray-600 p-4">{{ $user->email }}</td>
                                            <td class="text-center font-bold text-gray-700 p-4">{{ $user->visits_count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-gray-500 text-center py-8">No users assigned to this workspace.</div>
                    @endif
                </div>
            </div>

        @elseif($activeTab === 'settings')
            <div class="max-w-5xl mx-auto space-y-8 animate-fade-in-up">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight mb-2">Facility Settings</h2>
                    <p class="text-gray-500">Configure the operational parameters for {{ $tenant->name }}.</p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Policy Settings Card -->
                    <div class="card p-8">
                        <h3 class="text-xl font-bold mb-6 border-b border-gray-200 pb-4 text-gray-900">Compliance Policy</h3>
                        @if(session()->has('message'))
                            <div class="bg-green-100 border border-green-200 text-green-700 p-3 mb-6 animate-fade-in-up text-sm font-medium rounded-xl">
                                {{ session('message') }}
                            </div>
                        @endif
                        <form wire:submit="saveSettings" class="space-y-6">
                            
                            <!-- Tenant Logo Upload -->
                            <div class="input-group mb-0 border border-dashed border-gray-300 rounded-xl p-4 bg-gray-50/50">
                                <label class="text-sm font-bold text-gray-900 mb-3 block">Tenant Brand Logo</label>
                                <div class="flex items-center gap-4">
                                    @if ($logo)
                                        <img src="{{ $logo->temporaryUrl() }}" class="h-16 w-auto object-contain rounded border bg-white p-1">
                                    @elseif ($tenant->logo_path)
                                        <img src="{{ Storage::url($tenant->logo_path) }}" class="h-16 w-auto object-contain rounded border bg-white p-1">
                                    @else
                                        <div class="h-16 w-16 bg-gray-200 rounded flex items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    @endif
                                    <div class="flex-grow">
                                        <input type="file" wire:model="logo" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                                        <div wire:loading wire:target="logo" class="text-xs text-blue-500 mt-1 font-bold">Uploading...</div>
                                        @error('logo') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="input-group mb-0">
                                <label class="text-sm font-bold text-gray-900 mb-3">Legal Disclaimer / NDA Text</label>
                                <textarea wire:model="nda_text" class="input h-32 resize-none" placeholder="Enter terms guests must accept..."></textarea>
                            </div>
                            <div class="input-group mb-0">
                                <label class="text-sm font-bold text-gray-900 mb-3 block">GDPR Visitor Data Retention (Days)</label>
                                <input wire:model="data_retention_days" type="number" class="input">
                            </div>
                            <button type="submit" class="btn w-full mt-2">Apply Facility Configuration</button>
                        </form>
                    </div>

                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                     <!-- Locations Management Card -->
                     <div class="card p-8">
                        <h3 class="text-xl font-bold mb-6 text-gray-900 border-b border-gray-200 pb-4">Facility Buildings / Locations</h3>
                        
                        @if(session()->has('location_message'))
                            <div class="bg-green-100 border border-green-200 text-green-700 p-3 mb-6 animate-fade-in-up text-sm font-medium rounded-xl">
                                {{ session('location_message') }}
                            </div>
                        @endif

                        @if(count($locations) > 0)
                            <div class="overflow-x-auto mb-8 rounded-xl border border-gray-200 bg-white">
                                <table class="data-grid w-full">
                                    <thead>
                                        <tr>
                                            <th class="text-left font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Location Designation</th>
                                            <th class="w-24 border-b border-gray-200 bg-gray-50 p-4"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($locations as $location)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                @if($editingLocationId === $location->id)
                                                    <td colspan="2" class="p-4">
                                                        <div class="flex gap-2">
                                                            <input wire:model="editLocationName" type="text" class="input flex-grow text-sm py-1 px-3 h-9">
                                                            <button wire:click="updateLocation" class="text-white bg-green-500 hover:bg-green-600 px-3 py-1.5 rounded-lg font-bold text-xs transition-colors">Save</button>
                                                            <button wire:click="cancelEditLocation" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 px-3 py-1.5 rounded-lg font-bold text-xs transition-colors">Cancel</button>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="font-bold text-gray-900 p-4">{{ $location->name }}</td>
                                                    <td class="text-right p-4 whitespace-nowrap">
                                                        <button wire:click="editLocation({{ $location->id }})" class="text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded text-xs font-bold transition-colors mr-2">Edit</button>
                                                        <button wire:click="confirmAction('deleteLocation', {{ $location->id }}, 'Delete this building and ALL associated rooms?')" class="text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded text-xs font-bold transition-colors">Drop</button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50/50 border border-dashed border-gray-300 rounded-2xl text-gray-500 text-center py-10 mb-8 font-medium text-sm">
                                No buildings added yet.
                            </div>
                        @endif
                        
                        <div class="p-6 rounded-2xl border border-gray-100/60 shadow-sm bg-gray-50/30">
                            <h4 class="text-sm font-bold text-gray-900 mb-4">Add New Facility Building</h4>
                            <div class="flex flex-col gap-4">
                                <input wire:model="newLocationName" type="text" class="input" placeholder="e.g. North Tower, Floor 4">
                                @error('newLocationName') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                                <button wire:click="addLocation" class="btn w-full">Create Location</button>
                            </div>
                        </div>
                    </div>

                     <!-- Rooms Management Card -->
                     <div class="card p-8 delay-100">
                        <h3 class="text-xl font-bold mb-6 text-gray-900 border-b border-gray-200 pb-4">Meeting Rooms</h3>
                        
                        @if(session()->has('room_message'))
                            <div class="bg-green-100 border border-green-200 text-green-700 p-3 mb-6 animate-fade-in-up text-sm font-medium rounded-xl">
                                {{ session('room_message') }}
                            </div>
                        @endif

                        @if(count($rooms) > 0)
                            <div class="overflow-x-auto mb-8 rounded-xl border border-gray-200 bg-white">
                                <table class="data-grid w-full">
                                    <thead>
                                        <tr>
                                            <th class="text-left font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Room / Building</th>
                                            <th class="text-center font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Cap.</th>
                                            <th class="w-24 border-b border-gray-200 bg-gray-50 p-4"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($rooms as $room)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                @if($editingRoomId === $room->id)
                                                    <td colspan="3" class="p-4">
                                                        <div class="flex flex-col gap-3">
                                                            <div class="flex gap-2">
                                                                <input wire:model="editRoomName" type="text" class="input flex-grow text-sm py-1.5 px-3 h-auto">
                                                                <input wire:model="editRoomCapacity" type="number" class="input w-20 text-center text-sm py-1.5 px-3 h-auto" placeholder="Cap">
                                                            </div>
                                                            <div class="flex gap-2">
                                                                <select wire:model="editRoomLocationId" class="input flex-grow text-sm py-1.5 px-3 h-auto">
                                                                    <option value="">-- Switch Building --</option>
                                                                    @foreach($locations as $loc)
                                                                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            
                                                            <!-- Edit Room Amenities & Availability -->
                                                            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 flex flex-col gap-2">
                                                                <label class="flex items-center gap-2 text-sm font-bold text-gray-800 cursor-pointer">
                                                                    <input type="checkbox" wire:model="editRoomIsAvailable" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                                                                    Room is Available (Bookable)
                                                                </label>
                                                                
                                                                <div class="border-t border-gray-200 mt-1 pt-2">
                                                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Included Amenities</p>
                                                                    <div class="grid grid-cols-2 gap-2">
                                                                        <label class="flex items-center gap-2 text-xs font-medium text-gray-700 cursor-pointer">
                                                                            <input type="checkbox" wire:model="editRoomAmenities.tv" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Display / TV
                                                                        </label>
                                                                        <label class="flex items-center gap-2 text-xs font-medium text-gray-700 cursor-pointer">
                                                                            <input type="checkbox" wire:model="editRoomAmenities.whiteboard" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Whiteboard
                                                                        </label>
                                                                        <label class="flex items-center gap-2 text-xs font-medium text-gray-700 cursor-pointer">
                                                                            <input type="checkbox" wire:model="editRoomAmenities.ac" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> A/C
                                                                        </label>
                                                                        <label class="flex items-center gap-2 text-xs font-medium text-gray-700 cursor-pointer">
                                                                            <input type="checkbox" wire:model="editRoomAmenities.video" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Video Conf.
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="flex justify-end gap-2 mt-1 border-t border-gray-100 pt-3">
                                                                <button wire:click="cancelEditRoom" class="text-gray-500 hover:text-gray-700 hover:bg-gray-100 px-3 py-1.5 rounded-lg font-bold text-xs transition-colors">Cancel</button>
                                                                <button wire:click="updateRoom" class="text-white bg-green-500 hover:bg-green-600 px-4 py-1.5 rounded-lg font-bold text-xs transition-colors shadow-sm">Save Changes</button>
                                                            </div>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="p-4">
                                                        <div class="flex items-center gap-2">
                                                            <div class="w-2.5 h-2.5 rounded-full {{ $room->is_available ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                                            <p class="font-bold text-gray-900">{{ $room->name }}</p>
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-0.5 font-medium">{{ $room->location->name ?? 'Unassigned' }}</p>
                                                        
                                                        @if(!empty($room->amenities))
                                                            <div class="flex gap-1 mt-2">
                                                                @if($room->amenities['tv'] ?? false) <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-1.5 py-0.5 rounded border border-gray-200">TV</span> @endif
                                                                @if($room->amenities['whiteboard'] ?? false) <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-1.5 py-0.5 rounded border border-gray-200">WB</span> @endif
                                                                @if($room->amenities['video'] ?? false) <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-1.5 py-0.5 rounded border border-gray-200">VID</span> @endif
                                                                @if($room->amenities['ac'] ?? false) <span class="bg-gray-100 text-gray-600 text-[10px] uppercase font-bold px-1.5 py-0.5 rounded border border-gray-200">A/C</span> @endif
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="text-center font-bold text-gray-700 p-4">{{ $room->capacity }}</td>
                                                    <td class="text-right p-4 whitespace-nowrap">
                                                        <button wire:click="editRoom({{ $room->id }})" class="text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded text-xs font-bold transition-colors mr-1">Edit</button>
                                                        <button wire:click="confirmAction('deleteRoom', {{ $room->id }}, 'Delete this meeting room?')" class="text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded text-xs font-bold transition-colors">Drop</button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-50/50 border border-dashed border-gray-300 rounded-2xl text-gray-500 text-center py-10 mb-8 font-medium text-sm">
                                No meeting rooms established.
                            </div>
                        @endif
                        
                        <div class="p-6 rounded-2xl border border-gray-100/60 shadow-sm bg-gray-50/30">
                            <h4 class="text-sm font-bold text-gray-900 mb-4">Provision New Room</h4>
                            <div class="space-y-4 mb-5">
                                <div class="flex gap-4">
                                    <div class="flex-grow">
                                        <input wire:model="newRoomName" type="text" class="input w-full" placeholder="Room Title">
                                        @error('newRoomName') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="w-24">
                                        <input wire:model="newRoomCapacity" type="number" class="input w-full text-center" placeholder="1" min="1">
                                        @error('newRoomCapacity') <span class="text-red-500 text-xs font-bold block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <select wire:model="newRoomLocationId" class="input w-full bg-white">
                                        <option value="">-- Select Building / Location --</option>
                                        @foreach($locations as $loc)
                                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newRoomLocationId') <span class="text-red-500 text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                
                                <!-- New Room Amenities & Availability -->
                                <div class="bg-white rounded-lg p-4 border border-gray-200 flex flex-col gap-3">
                                    <label class="flex items-center gap-2 text-sm font-bold text-gray-800 cursor-pointer">
                                        <input type="checkbox" wire:model="newRoomIsAvailable" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                                        Room is Available (Bookable)
                                    </label>
                                    
                                    <div class="border-t border-gray-100 pt-3">
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Included Amenities</p>
                                        <div class="grid grid-cols-2 gap-3">
                                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                                                <input type="checkbox" wire:model="newRoomAmenities.tv" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Display / TV
                                            </label>
                                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                                                <input type="checkbox" wire:model="newRoomAmenities.whiteboard" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Whiteboard
                                            </label>
                                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                                                <input type="checkbox" wire:model="newRoomAmenities.ac" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> A/C
                                            </label>
                                            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                                                <input type="checkbox" wire:model="newRoomAmenities.video" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"> Video Conf.
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button wire:click="addRoom" class="btn w-full shadow-md hover:shadow-lg transition-shadow" {{ count($locations) === 0 ? 'disabled' : '' }}>
                                {{ count($locations) === 0 ? 'Add a Building First' : 'Provision Room' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'users')
            <div class="max-w-6xl mx-auto space-y-8 animate-fade-in-up" wire:key="users-tab">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-3xl font-extrabold tracking-tight mb-2 text-gray-900">User Management</h2>
                        <p class="text-gray-500">Manage users within your organization.</p>
                    </div>
                    <button wire:click="openCreateUserModal" class="btn shrink-0">+ Add User</button>
                </div>

                @if(session()->has('user_message'))
                    <div class="bg-green-100 border border-green-200 text-green-700 p-4 mb-6 animate-fade-in-up text-sm font-bold rounded-xl flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('user_message') }}
                    </div>
                @endif

                <div class="card p-0 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="data-grid w-full">
                            <thead>
                                <tr>
                                    <th>User Details</th>
                                    <th>Role</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody wire:key="users-tbody">
                                @forelse($users as $user)
                                    <tr class="group hover:bg-gray-50/50 transition-colors" wire:key="user-{{ $user->id }}">
                                        <td>
                                            <div class="font-bold text-gray-900">{{ $user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        </td>
                                        <td>
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-extrabold bg-gray-100 text-gray-800">
                                                {{ $user->allTenantRoles->pluck('name')->join(', ') ?: 'No Role' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($user->is_active)
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button wire:click="openEditUserModal({{ $user->id }})" class="text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">Edit</button>
                                                @if(auth()->id() !== $user->id)
                                                    <button wire:click="confirmAction('deleteUser', {{ $user->id }}, 'Are you sure you want to delete this user?')" class="text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors">Delete</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-16 text-gray-500 font-medium">
                                            No users found. Create your first user to get started.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Create User Modal -->
            @if($showCreateUserModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm animate-fade-in-up" wire:click.self="closeCreateUserModal">
                    <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 m-4">
                        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-xl font-extrabold text-gray-900">Add User</h3>
                            <button wire:click="closeCreateUserModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form wire:submit="createUser" class="p-8 space-y-6">
                            <div class="space-y-4">
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Full Name <span class="text-[#FF4B4B]">*</span></label>
                                    <input wire:model="newUser.name" type="text" class="input py-2" placeholder="John Doe">
                                    @error('newUser.name') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Email Address <span class="text-[#FF4B4B]">*</span></label>
                                    <input wire:model="newUser.email" type="email" class="input py-2" placeholder="john@company.com">
                                    @error('newUser.email') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Password <span class="text-[#FF4B4B]">*</span></label>
                                    <input wire:model="newUser.password" type="password" class="input py-2" placeholder="Minimum 8 characters">
                                    @error('newUser.password') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Role <span class="text-[#FF4B4B]">*</span></label>
                                    <select wire:model="newUser.role" class="input py-2 bg-white">
                                        <option value="">-- Select Role --</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('newUser.role') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                                <button type="button" wire:click="closeCreateUserModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">Cancel</button>
                                <button type="submit" class="btn">Create User</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Edit User Modal -->
            @if($showEditUserModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm animate-fade-in-up" wire:click.self="closeEditUserModal">
                    <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 m-4">
                        <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                            <h3 class="text-xl font-extrabold text-gray-900">Edit User</h3>
                            <button wire:click="closeEditUserModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <form wire:submit="updateUser" class="p-8 space-y-6">
                            <div class="space-y-4">
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Full Name <span class="text-[#FF4B4B]">*</span></label>
                                    <input wire:model="editUserData.name" type="text" class="input py-2">
                                    @error('editUserData.name') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Email Address <span class="text-[#FF4B4B]">*</span></label>
                                    <input wire:model="editUserData.email" type="email" class="input py-2">
                                    @error('editUserData.email') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="input-group mb-0">
                                    <label class="text-sm font-bold text-gray-900 mb-2">Role <span class="text-[#FF4B4B]">*</span></label>
                                    <select wire:model="editUserData.role" class="input py-2 bg-white">
                                        <option value="">-- Select Role --</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('editUserData.role') <span class="text-[#FF4B4B] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex items-center gap-3 pt-2">
                                    <input wire:model="editUserData.is_active" id="editIsActive" type="checkbox" class="w-4 h-4 text-[#FF4B4B] border-gray-300 rounded focus:ring-[#FF4B4B]">
                                    <label for="editIsActive" class="text-sm font-medium text-gray-900">Account Active</label>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                                <button type="button" wire:click="closeEditUserModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">Cancel</button>
                                <button type="submit" class="btn">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        @elseif($activeTab === 'subtenants')
            <div class="max-w-5xl mx-auto space-y-8 animate-fade-in-up">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight mb-2">Sub-Tenant Infrastructure</h2>
                    <p class="text-gray-500">Manage isolated workspaces for branch offices or distinct organizational silos.</p>
                </div>
                
                <div class="card p-0 overflow-hidden">
                    @if(session()->has('subtenant_message'))
                        <div class="bg-green-100 border-b border-green-200 text-green-700 p-4 mb-6 animate-fade-in-up text-sm font-bold rounded-xl flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ session('subtenant_message') }}
                        </div>
                    @endif
                    
                    @if(count($subTenants) > 0)
                            <table class="data-grid w-full">
                                <thead>
                                    <tr>
                                        <th class="text-left font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Child Entity Name</th>
                                        <th class="text-left font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Domain / Contact</th>
                                        <th class="text-center font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Users</th>
                                        <th class="text-center font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Total Visitors</th>
                                        <th class="text-right font-bold text-gray-900 bg-gray-50 uppercase text-xs tracking-wider border-b border-gray-200 p-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($subTenants as $subTenant)
                                        <tr class="hover:bg-gray-50 transition-colors group">
                                            @if($editingSubTenantId === $subTenant->id)
                                                <td colspan="5" class="p-4 bg-gray-50 border border-gray-100 rounded-xl relative shadow-sm">
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Workspace Identity</label>
                                                            <div class="space-y-3">
                                                                <input wire:model="editSubTenantName" type="text" class="input text-sm" placeholder="Entity Name">
                                                                <input wire:model="editSubTenantDomain" type="text" class="input text-sm" placeholder="Domain Identifier">
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Contract Term</label>
                                                            <div class="space-y-3">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="text-xs text-gray-400 w-8">Start</span>
                                                                    <input wire:model="editSubTenantContractStart" type="date" class="input text-sm flex-grow">
                                                                </div>
                                                                <div class="flex items-center gap-2">
                                                                    <span class="text-xs text-gray-400 w-8">End</span>
                                                                    <input wire:model="editSubTenantContractEnd" type="date" class="input text-sm flex-grow">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200">
                                                        <button wire:click="cancelEditSubTenant" class="text-gray-500 hover:text-gray-700 hover:bg-gray-200 px-4 py-2 rounded-lg font-bold text-xs transition-colors">Discard</button>
                                                        <button wire:click="updateSubTenant" class="text-white bg-green-500 hover:bg-green-600 px-5 py-2 rounded-lg font-bold text-xs transition-colors shadow-sm">Commit Changes</button>
                                                    </div>
                                                </td>
                                            @else
                                                <td class="font-bold text-gray-900 p-4 group-hover:text-[#FF4B4B] transition-colors">
                                                    <p>{{ $subTenant->name }}</p>
                                                    @if($subTenant->contract_end_date)
                                                        <p class="text-[10px] font-medium text-gray-400 mt-1 uppercase">Valid via {{ $subTenant->contract_end_date->format('M Y') }}</p>
                                                    @endif
                                                </td>
                                                <td class="p-4">
                                                    <p class="text-gray-500 font-medium text-sm">{{ $subTenant->domain ?? 'N/A' }}</p>
                                                    @php $primary = $subTenant->contacts->first(); @endphp
                                                    @if($primary)
                                                        <p class="text-xs text-gray-400 mt-0.5"><i class="fas fa-user-tie mr-1"></i>{{ $primary->name }}</p>
                                                    @endif
                                                </td>
                                                <td class="text-center font-bold text-gray-700 p-4">{{ $subTenant->users_count }}</td>
                                                <td class="text-center font-bold text-gray-700 p-4">{{ $subTenant->visits_count }}</td>
                                                <td class="text-right p-4 whitespace-nowrap">
                                                    <button wire:click="editSubTenant({{ $subTenant->id }})" class="text-blue-600 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded text-xs font-bold transition-colors mr-2">Config</button>
                                                    <a href="/admin/dashboard?tenant_id={{ $subTenant->id }}" class="text-[#FF4B4B] font-bold text-sm bg-[#FF4B4B]/10 px-4 py-2 rounded border border-[#FF4B4B]/20 inline-block hover:bg-[#FF4B4B] hover:text-white transition-all shadow-sm">Inspect</a>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                    @else
                        <div class="text-center py-16 text-gray-500">
                            No sub-tenants deployed under this hierarchy.
                        </div>
                    @endif
                </div>

                <div class="card max-w-2xl mx-auto delay-100 mt-8">
                    <h3 class="text-xl font-extrabold text-gray-900 mb-6">Deploy New Child Entity</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="space-y-4">
                            <div class="input-group mb-0">
                                <label>Entity Name</label>
                                <input wire:model="newSubTenantName" type="text" class="input" placeholder="e.g. Acme NY Branch">
                                @error('newSubTenantName') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="input-group mb-0">
                                <label>Domain Identifier (Optional)</label>
                                <input wire:model="newSubTenantDomain" type="text" class="input" placeholder="e.g. acme-ny">
                                @error('newSubTenantDomain') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="input-group mb-0">
                                <label>Contract Start Date</label>
                                <input wire:model="newSubTenantContractStart" type="date" class="input">
                                @error('newSubTenantContractStart') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div class="input-group mb-0">
                                <label>Contract End Date</label>
                                <input wire:model="newSubTenantContractEnd" type="date" class="input">
                                @error('newSubTenantContractEnd') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <button wire:click="addSubTenant" class="btn w-full">Initialize Workspace</button>
                </div>
            </div>

        @elseif($activeTab === 'analytics')
            <div class="max-w-5xl mx-auto space-y-8 animate-fade-in-up">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight mb-2">Traffic Telemetry</h2>
                    <p class="text-gray-500">High-level analytics for past 7 days of visit density.</p>
                </div>
                
                <div class="card p-8">
                    @if(count($stats) === 0)
                        <div class="text-center py-16 text-gray-500">
                            Insufficient telemetry data to display graph.
                        </div>
                    @else
                        <div class="flex h-64 items-end gap-4 border-b border-gray-100 pb-2 relative">
                            <!-- Background Grid Lines -->
                            <div class="absolute inset-x-0 bottom-0 h-full w-full pointer-events-none flex flex-col justify-between">
                                <div class="w-full border-t border-gray-100 h-0"></div>
                                <div class="w-full border-t border-gray-100 h-0"></div>
                                <div class="w-full border-t border-gray-100 h-0"></div>
                                <div class="w-full border-t border-gray-100 h-0"></div>
                            </div>
                            
                            @foreach($stats as $stat)
                                <div class="flex-1 flex flex-col items-center justify-end group z-10 relative">
                                    <span class="absolute -top-8 bg-gray-900 text-white font-bold text-xs px-3 py-1 rounded-full transform scale-0 group-hover:scale-100 transition-transform origin-bottom shadow-lg z-20">{{ $stat['count'] }}</span>
                                    
                                    <div class="w-1/2 max-w-[3rem] bg-gray-200 group-hover:bg-[#FF4B4B] rounded-t-lg transition-colors duration-300" style="height: {{ max($stat['count'] * 15, 4) }}px;"></div>
                                    <span class="text-xs text-gray-500 mt-4 font-bold">{{ \Carbon\Carbon::parse($stat['date'])->format('M d') }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Global Action Confirmation Modal -->
        @if($showConfirmModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/60 backdrop-blur-sm animate-fade-in-up" wire:click="$set('showConfirmModal', false)">
                <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 m-4" wire:click.stop>
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 bg-[#FF4B4B]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-[#FF4B4B]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Are you certain?</h3>
                        <p class="text-sm text-gray-500 mb-8">{{ $confirmMessage }}</p>
                        <div class="flex items-center justify-center gap-3">
                            <button wire:click="closeConfirmModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 rounded-xl transition-colors">Cancel</button>
                            <button wire:click="executeAction" class="px-5 py-2.5 text-sm font-bold text-white bg-[#FF4B4B] hover:bg-red-600 shadow-md hover:shadow-lg rounded-xl transition-all">Yes, Proceed</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>