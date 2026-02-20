<?php

use function Livewire\Volt\{state, mount, on, layout};
use App\Models\Tenant;
use App\Models\MeetingRoom;
use App\Models\Visit;
use App\Models\Location;
use Carbon\Carbon;

layout('components.layouts.app');

state(['tenant' => null, 'nda_text' => '', 'data_retention_days' => 180, 'rooms' => [], 'subTenants' => [], 'newRoomName' => '', 'newRoomCapacity' => 1, 'newSubTenantName' => '', 'newSubTenantDomain' => '', 'activeTab' => 'settings', 'stats' => [], 'editingRoomId' => null, 'editRoomName' => '', 'editRoomCapacity' => 1, 'editingSubTenantId' => null, 'editSubTenantName' => '', 'editSubTenantDomain' => '']);

mount(function () {
    if (request()->has('tenant_id')) {
        $this->tenant = Tenant::findOrFail(request('tenant_id'));
    } else {
        $this->tenant = Tenant::firstOrCreate(
            ['domain' => 'demo'],
            ['name' => 'Demo Tenant', 'nda_text' => 'Demo NDA']
        );
    }
    $this->nda_text = $this->tenant->nda_text;
    $this->data_retention_days = $this->tenant->data_retention_days;
    $this->loadRooms();
    $this->loadSubTenants();
    $this->loadStats();
});

$loadRooms = function() {
    $this->rooms = $this->tenant->meetingRooms()->get();
};

$loadSubTenants = function() {
    $this->subTenants = $this->tenant->subTenants()->get();
};

$loadStats = function() {
    $this->stats = $this->tenant->visits()
        ->selectRaw('DATE(created_at) as date, count(*) as count')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->take(7)
        ->get()
        ->toArray();
};

$saveSettings = function() {
    $this->tenant->update([
        'nda_text' => $this->nda_text,
        'data_retention_days' => $this->data_retention_days,
    ]);
    session()->flash('message', 'Settings saved successfully.');
};

$addRoom = function() {
    $this->validate(['newRoomName' => 'required|string', 'newRoomCapacity' => 'required|integer|min:1']);
    $location = Location::firstOrCreate(['tenant_id' => $this->tenant->id, 'name' => 'Main Office']);
    $this->tenant->meetingRooms()->create([
        'location_id' => $location->id,
        'name' => $this->newRoomName,
        'capacity' => $this->newRoomCapacity,
    ]);
    $this->newRoomName = '';
    $this->newRoomCapacity = 1;
    $this->loadRooms();
    session()->flash('room_message', 'Room added successfully.');
};

$editRoom = function($id) {
    $room = MeetingRoom::findOrFail($id);
    $this->editingRoomId = $id;
    $this->editRoomName = $room->name;
    $this->editRoomCapacity = $room->capacity;
};

$updateRoom = function() {
    $this->validate([
        'editRoomName' => 'required|string|max:255',
        'editRoomCapacity' => 'required|integer|min:1',
    ]);

    $room = MeetingRoom::findOrFail($this->editingRoomId);
    $room->update([
        'name' => $this->editRoomName,
        'capacity' => $this->editRoomCapacity,
    ]);

    $this->editingRoomId = null;
    $this->loadRooms();
    session()->flash('room_message', 'Room updated successfully.');
};

$cancelEditRoom = function() {
    $this->editingRoomId = null;
};

$deleteRoom = function($id) {
    $room = MeetingRoom::findOrFail($id);
    $room->delete();
    $this->loadRooms();
    session()->flash('room_message', 'Room deleted permanently.');
};

$addSubTenant = function() {
    $this->validate([
        'newSubTenantName' => 'required|string|max:255',
        'newSubTenantDomain' => 'nullable|string|max:255|unique:tenants,domain',
    ]);
    $this->tenant->subTenants()->create([
        'name' => $this->newSubTenantName,
        'domain' => $this->newSubTenantDomain,
        'nda_text' => $this->tenant->nda_text, // Inherit NDA text by default
        'data_retention_days' => $this->tenant->data_retention_days,
    ]);
    $this->newSubTenantName = '';
    $this->newSubTenantDomain = '';
    $this->loadSubTenants();
    session()->flash('subtenant_message', 'Sub-Tenant added successfully.');
};

$editSubTenant = function($id) {
    $subTenant = Tenant::findOrFail($id);
    $this->editingSubTenantId = $id;
    $this->editSubTenantName = $subTenant->name;
    $this->editSubTenantDomain = $subTenant->domain;
};

$updateSubTenant = function() {
    $this->validate([
        'editSubTenantName' => 'required|string|max:255',
        'editSubTenantDomain' => ['nullable', 'string', 'max:255', \Illuminate\Validation\Rule::unique('tenants', 'domain')->ignore($this->editingSubTenantId)],
    ]);

    $subTenant = Tenant::findOrFail($this->editingSubTenantId);
    $subTenant->update([
        'name' => $this->editSubTenantName,
        'domain' => $this->editSubTenantDomain,
    ]);

    $this->editingSubTenantId = null;
    $this->loadSubTenants();
    session()->flash('subtenant_message', 'Sub-Tenant updated successfully.');
};

$cancelEditSubTenant = function() {
    $this->editingSubTenantId = null;
};

$deleteSubTenant = function($id) {
    $subTenant = Tenant::findOrFail($id);
    $subTenant->delete();
    $this->loadSubTenants();
    session()->flash('subtenant_message', 'Sub-Tenant deleted permanently.');
};

$setTab = function($tab) {
    // Reset specific states when navigating tabs if needed
    $this->activeTab = $tab;
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
                <button wire:click="setTab('settings')" class="w-full text-left px-4 py-3 text-sm font-semibold transition-all duration-150 rounded-lg {{ $activeTab === 'settings' ? 'bg-[#FF4B4B]/10 text-[#FF4B4B]' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    Settings & Rooms
                </button>
                <button wire:click="setTab('subtenants')" class="w-full text-left px-4 py-3 text-sm font-semibold transition-all duration-150 rounded-lg {{ $activeTab === 'subtenants' ? 'bg-[#FF4B4B]/10 text-[#FF4B4B]' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    Sub-Tenants
                </button>
                <button wire:click="setTab('analytics')" class="w-full text-left px-4 py-3 text-sm font-semibold transition-all duration-150 rounded-lg {{ $activeTab === 'analytics' ? 'bg-[#FF4B4B]/10 text-[#FF4B4B]' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100' }}">
                    Analytics
                </button>
                <a href="/admin/visitors" class="block w-full text-left px-4 py-3 text-sm font-semibold transition-all duration-150 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100">
                    Visitor Profiles
                </a>
            </nav>
        </div>
        
        <div class="p-4 border-t border-[#374151]">
            <a href="/kiosk/{{ $tenant->id }}" target="_blank" class="w-full btn btn-outline flex justify-center text-xs">Launch Kiosk</a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 overflow-y-auto p-8 lg:p-12 relative animate-fade-in-up">
        
        <!-- Tab Content Routing -->
        @if($activeTab === 'settings')
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
                            <div class="input-group mb-0">
                                <label class="text-sm font-bold text-gray-900 mb-3">Legal Disclaimer / NDA Text</label>
                                <textarea wire:model="nda_text" class="input h-32 resize-none" placeholder="Enter terms guests must accept..."></textarea>
                            </div>
                            <div class="input-group mb-0">
                                <label class="text-sm font-bold text-gray-900 mb-3">GDPR Visitor Data Retention (Days)</label>
                                <input wire:model="data_retention_days" type="number" class="input">
                            </div>
                            <button type="submit" class="btn w-full mt-2">Apply Policies</button>
                        </form>
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
                                <table class="data-grid">
                                    <thead>
                                        <tr>
                                            <th>Room Designation</th>
                                            <th class="w-24 text-center">Capacity</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($rooms as $room)
                                            <tr>
                                                @if($editingRoomId === $room->id)
                                                    <td colspan="3" class="p-2">
                                                        <div class="flex gap-2">
                                                            <input wire:model="editRoomName" type="text" class="input flex-grow text-sm py-1 px-2 h-8">
                                                            <input wire:model="editRoomCapacity" type="number" class="input w-16 text-center text-sm py-1 px-2 h-8">
                                                            <button wire:click="updateRoom" class="text-green-600 hover:text-green-800 font-medium">Save</button>
                                                            <button wire:click="cancelEditRoom" class="text-gray-500 hover:text-gray-700 font-medium">Cancel</button>
                                                        </div>
                                                    </td>
                                                @else
                                                    <td class="font-medium text-gray-900">{{ $room->name }}</td>
                                                    <td class="text-center text-gray-500">{{ $room->capacity }}</td>
                                                    <td class="text-right">
                                                        <button wire:click="editRoom({{ $room->id }})" class="text-blue-500 hover:text-blue-700 mr-2 text-sm font-medium">Edit</button>
                                                        <button wire:click="deleteRoom({{ $room->id }})" wire:confirm="Delete this meeting room?" class="text-red-500 hover:text-red-700 text-sm font-medium">Delete</button>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="bg-gray-400 border border-gray-500 text-gray-500 text-center py-12 mb-8 font-medium font-sans">
                                No meeting rooms established.
                            </div>
                        @endif
                        
                        <div class="p-6 rounded-[24px] border border-gray-100/60 shadow-sm mb-2">
                            <h4 class="text-sm font-bold text-gray-900 mb-4">Provision New Room</h4>
                            <div class="flex gap-4 mb-6">
                                <input wire:model="newRoomName" type="text" class="input flex-grow" placeholder="Room Title">
                                <input wire:model="newRoomCapacity" type="number" class="input w-24 text-center" placeholder="1">
                            </div>
                            <button wire:click="addRoom" class="btn w-full">Provision</button>
                        </div>
                    </div>
                </div>
            </div>

        @elseif($activeTab === 'subtenants')
            <div class="max-w-5xl mx-auto space-y-8 animate-fade-in-up">
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight mb-2">Sub-Tenant Infrastructure</h2>
                    <p class="text-gray-500">Manage isolated workspaces for branch offices or distinct organizational silos.</p>
                </div>
                
                <div class="card p-0 overflow-hidden">
                    @if(session()->has('subtenant_message'))
                        <div class="bg-green-900/30 border-b border-green-500/50 text-green-400 p-4 text-sm font-medium">
                            {{ session('subtenant_message') }}
                        </div>
                    @endif
                    
                    @if(count($subTenants) > 0)
                        <table class="data-grid w-full">
                            <thead>
                                <tr>
                                    <th>Child Entity Name</th>
                                    <th>Domain Identity</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subTenants as $subTenant)
                                    <tr class="group">
                                        <td class="font-bold text-gray-900 group-hover:text-[#FF4B4B] transition-colors">{{ $subTenant->name }}</td>
                                        <td class="text-gray-500">{{ $subTenant->domain ?? 'N/A' }}</td>
                                        <td class="text-right">
                                            <a href="/admin/dashboard?tenant_id={{ $subTenant->id }}" class="text-[#FF4B4B] font-bold text-sm bg-[#FF4B4B]/10 px-4 py-2 rounded-full inline-block hover:bg-[#FF4B4B]/20 transition-colors">Inspect</a>
                                        </td>
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
    </main>
</div>