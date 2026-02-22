<?php

namespace App\Livewire\Kiosk;

use App\Models\AccessPoint;
use App\Models\Meeting;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Livewire\Component;
use Livewire\WithFileUploads;

class KioskMode extends Component
{
    use WithFileUploads;

    public ?Tenant $tenant = null;
    public ?AccessPoint $accessPoint = null;
    
    // Step tracking
    public int $step = 1; // 1: Welcome, 2: Lookup/Create, 3: Details, 4: Host Selection, 5: Complete
    
    // Visitor info
    public string $searchQuery = '';
    public ?Visitor $selectedVisitor = null;
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public $photo;
    public $id_document;
    
    // Visit info
    public string $purpose = '';
    public ?int $host_id = null;
    public ?int $meeting_id = null;
    
    // Results
    public ?VisitorVisit $visit = null;
    public string $badgeNumber = '';
    public string $message = '';

    protected $listeners = ['resetKiosk' => 'resetAll'];

    public function mount(string $tenantSlug, string $accessPointUuid): void
    {
        // Find tenant by slug
        $this->tenant = Tenant::where('slug', $tenantSlug)->firstOrFail();

        // Find access point by UUID and verify it belongs to the tenant
        $this->accessPoint = AccessPoint::where('uuid', $accessPointUuid)
            ->where('tenant_id', $this->tenant->id)
            ->where('is_kiosk_mode', true)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function lookupVisitor(): void
    {
        $this->validate(['searchQuery' => 'required|string|min:2']);
        
        $this->selectedVisitor = Visitor::where('tenant_id', $this->tenant->id)
            ->where('is_blacklisted', false)
            ->where(function ($q) {
                $q->where('email', $this->searchQuery)
                    ->orWhere('phone', $this->searchQuery)
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$this->searchQuery}%"]);
            })
            ->first();

        if ($this->selectedVisitor) {
            $this->fill([
                'first_name' => $this->selectedVisitor->first_name,
                'last_name' => $this->selectedVisitor->last_name,
                'email' => $this->selectedVisitor->email,
                'phone' => $this->selectedVisitor->phone,
                'company' => $this->selectedVisitor->company,
            ]);
            $this->step = 3;
        } else {
            $this->step = 2;
        }
    }

    public function createNewVisitor(): void
    {
        $this->selectedVisitor = null;
        $this->reset(['first_name', 'last_name', 'email', 'phone', 'company', 'photo']);
        $this->step = 2;
    }

    public function saveVisitorDetails(): void
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:5120',
        ]);

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('visitors/photos', 'public');
        }

        if ($this->selectedVisitor) {
            $this->selectedVisitor->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company' => $this->company,
                'photo' => $photoPath ?? $this->selectedVisitor->photo,
            ]);
        } else {
            $this->selectedVisitor = $this->tenant->visitors()->create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company' => $this->company,
                'photo' => $photoPath,
            ]);
        }

        $this->step = 4;
    }

    public function getHostsProperty()
    {
        return User::whereHas('tenants', fn ($q) => $q->where('tenant_id', $this->tenant->id))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getMeetingsProperty()
    {
        return Meeting::where('tenant_id', $this->tenant->id)
            ->whereDate('start_at', today())
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderBy('start_at')
            ->get();
    }

    public function completeCheckIn(): void
    {
        if ($this->selectedVisitor->is_blacklisted) {
            $this->message = 'Access denied. Please see reception.';
            $this->step = 5;
            return;
        }

        $this->visit = $this->selectedVisitor->visits()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host_id,
            'meeting_id' => $this->meeting_id,
            'purpose' => $this->purpose,
            'check_in_method' => 'kiosk',
            'check_in_at' => now(),
            'status' => 'checked_in',
        ]);

        $this->accessPoint->logAccess($this->selectedVisitor, 'entry', 'granted', [
            'visit_id' => $this->visit->id,
        ]);

        // TODO: Send notification to host

        $this->badgeNumber = $this->visit->badge_number;
        $this->message = 'Check-in successful! Please proceed to your destination.';
        $this->step = 5;
    }

    public function resetAll(): void
    {
        $this->reset([
            'step', 'searchQuery', 'selectedVisitor', 'first_name', 'last_name',
            'email', 'phone', 'company', 'photo', 'id_document',
            'purpose', 'host_id', 'meeting_id', 'visit', 'badgeNumber', 'message'
        ]);
        $this->step = 1;
    }

    public function render()
    {
        return view('livewire.kiosk.kiosk-mode', [
            'hosts' => $this->hosts,
            'meetings' => $this->meetings,
        ])->layout('layouts.kiosk');
    }
}