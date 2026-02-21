<?php

use function Livewire\Volt\{state, mount, layout};
use App\Models\Tenant;
use App\Models\Visitor;
use App\Models\Visit;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

layout('components.layouts.app');

state(['tenant' => null, 'visitors' => [], 'search' => '', 'selectedVisitor' => null, 'host_id' => null, 'room_id' => null, 'scheduled_at' => '', 'showNewVisitorForm' => false, 'newVisitorName' => '', 'newVisitorEmail' => '', 'newVisitorPhone' => '', 'editingVisitorId' => null, 'editVisitorName' => '', 'editVisitorEmail' => '', 'editVisitorPhone' => '', 'showConfirmModal' => false, 'confirmActionType' => '', 'confirmId' => null, 'confirmMessage' => '']);

mount(function () {
    $this->tenant = Tenant::firstOrCreate(
        ['domain' => 'demo'],
        ['name' => 'Demo Tenant']
    );
    $this->loadVisitors();
});

$loadVisitors = function() {
    $this->visitors = $this->tenant->visitors()
        ->where('name', 'like', '%'.$this->search.'%')
        ->withCount('visits')
        ->get();
};

$updatedSearch = function($value) {
    $this->loadVisitors();
};

$selectVisitor = function($visitorId) {
    if ($this->selectedVisitor && $this->selectedVisitor->id === $visitorId) {
        $this->selectedVisitor = null;
    } else {
        $this->selectedVisitor = Visitor::find($visitorId);
    }
};

$createNewVisitor = function() {
    $this->validate([
        'newVisitorName' => 'required|string|max:255',
        'newVisitorEmail' => 'required|email',
        'newVisitorPhone' => 'nullable|string',
    ]);

    Visitor::create([
        'tenant_id' => $this->tenant->id,
        'name' => $this->newVisitorName,
        'email' => $this->newVisitorEmail,
        'phone_number' => $this->newVisitorPhone,
    ]);

    $this->newVisitorName = '';
    $this->newVisitorEmail = '';
    $this->newVisitorPhone = '';
    $this->showNewVisitorForm = false;
    $this->loadVisitors();
    session()->flash('message', 'New visitor profile created.');
};

$editVisitor = function($id) {
    $visitor = Visitor::findOrFail($id);
    $this->editingVisitorId = $id;
    $this->editVisitorName = $visitor->name;
    $this->editVisitorEmail = $visitor->email;
    $this->editVisitorPhone = $visitor->phone_number;
};

$updateVisitor = function() {
    $this->validate([
        'editVisitorName' => 'required|string|max:255',
        'editVisitorEmail' => 'required|email',
        'editVisitorPhone' => 'nullable|string',
    ]);

    $visitor = Visitor::findOrFail($this->editingVisitorId);
    $visitor->update([
        'name' => $this->editVisitorName,
        'email' => $this->editVisitorEmail,
        'phone_number' => $this->editVisitorPhone,
    ]);

    $this->editingVisitorId = null;
    $this->loadVisitors();
    session()->flash('message', 'Visitor profile updated.');
};

$cancelEditVisitor = function() {
    $this->editingVisitorId = null;
};

$deleteVisitor = function($id) {
    $visitor = Visitor::findOrFail($id);
    $visitor->delete();
    // Clear selection if deleted
    if ($this->selectedVisitor && $this->selectedVisitor->id === $id) {
        $this->selectedVisitor = null;
    }
    $this->loadVisitors();
    session()->flash('message', 'Visitor profile permanently deleted.');
};

$quickCheckIn = function() {
    $this->validate([
        'host_id' => 'required',
        'room_id' => 'required',
        'scheduled_at' => 'required|date'
    ]);
    
    $inviteCode = Str::random(8);
    $qrFileName = 'qrcodes/' . $inviteCode . '.svg';
    Storage::disk('public')->put($qrFileName, QrCode::format('svg')->size(300)->generate($inviteCode));
    
    $visit = Visit::create([
        'tenant_id' => $this->tenant->id,
        'visitor_id' => $this->selectedVisitor->id,
        'meeting_room_id' => $this->room_id,
        'host_user_id' => $this->host_id,
        'scheduled_at' => $this->scheduled_at,
        'status' => 'checked_in',
        'check_in_time' => now(),
        'invite_code' => $inviteCode,
        'qr_code_path' => $qrFileName,
    ]);
    
    session()->flash('message', 'Visitor checked in successfully. QR Code generated.');
    $this->selectedVisitor = null;
    $this->loadVisitors();
};

$confirmAction = function(string $actionType, ?int $id, string $message) {
    $this->confirmActionType = $actionType;
    $this->confirmId = $id;
    $this->confirmMessage = $message;
    $this->showConfirmModal = true;
};

$executeAction = function() {
    if ($this->confirmActionType === 'deleteVisitor' && $this->confirmId) {
        $this->deleteVisitor($this->confirmId);
    }
    
    $this->showConfirmModal = false;
    $this->confirmActionType = '';
    $this->confirmId = null;
    $this->confirmMessage = '';
};

$closeConfirmModal = function() {
    $this->showConfirmModal = false;
    $this->confirmActionType = '';
    $this->confirmId = null;
    $this->confirmMessage = '';
};

?>

<div>
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border); margin-bottom: 2rem; padding-bottom: 1rem;">
        <h1 style="margin: 0;">Visitor Profiles</h1>
        <a href="/admin/dashboard" class="btn btn-outline" style="text-decoration: none;">&larr; Back to Dashboard</a>
    </div>

    @if(session()->has('message'))
        <div style="color: #10B981; padding: 1rem; border: 2px solid #10B981; margin-bottom: 1rem; font-weight: bold; background: #ECFDF5;">
            {{ session('message') }}
        </div>
    @endif

    <div style="display: flex; gap: 2rem; flex-wrap: wrap;">
        
        <div class="card" style="flex: 2; min-width: 300px;">
            <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="input-group" style="flex: 1; margin-bottom: 0;">
                    <input wire:model.live="search" type="text" class="input" placeholder="Search visitors...">
                </div>
                <button wire:click="$toggle('showNewVisitorForm')" class="btn {{ $showNewVisitorForm ? 'btn-outline' : '' }}">
                    {{ $showNewVisitorForm ? 'Cancel Creation' : 'New Visitor' }}
                </button>
            </div>
            
            @if($showNewVisitorForm)
                <div style="padding: 1.5rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); background: #f9fafb; margin-bottom: 1.5rem;">
                    <h3 style="margin-top: 0; margin-bottom: 1rem;">Register New Visitor</h3>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                        <div class="input-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                            <label>Full Name</label>
                            <input wire:model="newVisitorName" type="text" class="input" placeholder="John Doe">
                        </div>
                        <div class="input-group" style="flex: 1; min-width: 200px; margin-bottom: 0;">
                            <label>Email</label>
                            <input wire:model="newVisitorEmail" type="email" class="input" placeholder="john@example.com">
                        </div>
                    </div>
                    <div class="input-group" style="max-width: 300px;">
                        <label>Phone Number</label>
                        <input wire:model="newVisitorPhone" type="text" class="input" placeholder="+1 (555) 000-0000">
                    </div>
                    <button wire:click="createNewVisitor" class="btn">Register Profile</button>
                </div>
            @endif

            <ul style="list-style: none; padding: 0;">
                @foreach($visitors as $visitor)
                    <li style="padding: 1rem; border: 2px solid var(--border-light); border-radius: var(--radius-sm); margin-bottom: 0.5rem; display: flex; flex-direction: column; gap: 1rem; {{ $selectedVisitor && $selectedVisitor->id === $visitor->id ? 'border-color: var(--primary); background: #fff5f5;' : '' }}">
                        
                        @if($editingVisitorId === $visitor->id)
                            <div style="display: flex; flex-direction: column; gap: 1rem;">
                                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                    <div class="input-group" style="flex: 1; margin-bottom: 0;">
                                        <label>Name</label>
                                        <input wire:model="editVisitorName" type="text" class="input">
                                    </div>
                                    <div class="input-group" style="flex: 1; margin-bottom: 0;">
                                        <label>Email</label>
                                        <input wire:model="editVisitorEmail" type="email" class="input">
                                    </div>
                                    <div class="input-group" style="flex: 1; margin-bottom: 0;">
                                        <label>Phone</label>
                                        <input wire:model="editVisitorPhone" type="text" class="input">
                                    </div>
                                </div>
                                <div style="display: flex; gap: 0.5rem;">
                                    <button wire:click="updateVisitor" class="btn">Save Changes</button>
                                    <button wire:click="cancelEditVisitor" class="btn btn-outline">Cancel</button>
                                </div>
                            </div>
                        @else
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <div style="flex: 1;">
                                    <strong style="font-size: 1.1rem; display: block; margin-bottom: 0.25rem;">{{ $visitor->name }}</strong>
                                    <div style="color: var(--text-secondary); font-size: 0.9rem; display: flex; flex-direction: column; gap: 0.25rem;">
                                        <span>📧 {{ $visitor->email }}</span>
                                        @if($visitor->phone_number)<span>📱 {{ $visitor->phone_number }}</span>@endif
                                        <span style="font-weight: 600; color: var(--text-primary); margin-top: 0.25rem;">Past Visits: <span style="color: var(--primary);">{{ $visitor->visits_count }}</span></span>
                                    </div>
                                </div>
                                <div style="display: flex; flex-direction: column; gap: 0.5rem; align-items: flex-end;">
                                    <button wire:click="selectVisitor({{ $visitor->id }})" class="btn {{ $selectedVisitor && $selectedVisitor->id === $visitor->id ? 'btn-outline' : '' }}" style="width: 160px; justify-content: center;">
                                        {{ $selectedVisitor && $selectedVisitor->id === $visitor->id ? 'Cancel Action' : '1-Click Check-in' }}
                                    </button>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <button wire:click="editVisitor({{ $visitor->id }})" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Edit</button>
                                        <button wire:click="confirmAction('deleteVisitor', {{ $visitor->id }}, 'Delete this visitor? This removes their history.')" class="btn btn-outline" style="padding: 0.5rem 1rem; font-size: 0.875rem; border-color: #ef4444; color: #ef4444;">Delete</button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </li>
                @endforeach
                @if(count($visitors) === 0 && !$search)
                    <div style="padding: 3rem; text-align: center; background: var(--bg-main); border-radius: var(--radius-md); border: 1px dashed var(--border-light);">
                        <p style="color: var(--text-secondary); font-weight: 600; margin: 0;">No visitor profiles exist yet.</p>
                    </div>
                @elseif(count($visitors) === 0 && $search)
                    <div style="padding: 2rem; text-align: center;">
                        <p style="color: var(--text-secondary); margin: 0;">No visitors found matching "{{ $search }}".</p>
                    </div>
                @endif
            </ul>
        </div>
        
        @if($selectedVisitor)
        <div class="card" style="flex: 1; min-width: 250px; background: #FAFAFA;">
            <h3>Quick Check-in: {{ $selectedVisitor->name }}</h3>
            <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Fill details to immediately check this returning visitor in.</p>
            
            <div class="input-group">
                <label>Host User ID</label>
                <input wire:model="host_id" type="number" class="input">
            </div>
            <div class="input-group">
                <label>Room ID</label>
                <input wire:model="room_id" type="number" class="input">
            </div>
            <div class="input-group">
                <label>Time</label>
                <input wire:model="scheduled_at" type="datetime-local" class="input">
            </div>
            <button wire:click="quickCheckIn" class="btn" style="width: 100%;">Complete 1-Click Check-in</button>
        </div>
        @endif
        
    </div>

    <!-- Global Action Confirmation Modal -->
    @if($showConfirmModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm" wire:click="$set('showConfirmModal', false)" style="animation: fade-in-up 0.3s ease-out forwards;">
            <div class="bg-white rounded-[24px] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 m-4 relative" wire:click.stop>
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-[#FF4B4B]/10 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-[#FF4B4B]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-extrabold" style="color: #111827; margin-bottom: 0.5rem;">Are you certain?</h3>
                    <p style="color: #6B7280; margin-bottom: 2rem; font-size: 0.875rem;">{{ $confirmMessage }}</p>
                    <div style="display: flex; justify-content: center; gap: 0.75rem;">
                        <button wire:click="closeConfirmModal" class="btn btn-outline" style="border: none; color: #4B5563;">Cancel</button>
                        <button wire:click="executeAction" class="btn" style="background: #FF4B4B; color: white;">Yes, Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>