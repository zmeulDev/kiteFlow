<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Tenant;
use App\Models\MeetingRoom;
use App\Models\Building;
use App\Models\User;
use App\Models\SubTenant;
use App\Jobs\SendPreRegistrationNotification;
use Illuminate\Support\Facades\Auth;

class VisitForm extends Component
{
    public ?Tenant $tenant;
    public ?Visit $visit = null;
    
    // Form fields
    public string $mode = 'create'; // create, edit
    public string $step = 'visitor'; // visitor, details, review
    
    // Visitor fields
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public ?Visitor $existingVisitor = null;
    
    // Visit details
    public ?int $host_user_id = null;
    public ?int $sub_tenant_id = null;
    public ?int $meeting_room_id = null;
    public ?int $building_id = null;
    public string $scheduled_date = '';
    public string $scheduled_start_time = '';
    public string $scheduled_end_time = '';
    public string $purpose = '';
    
    // Options
    public array $hosts = [];
    public array $subTenants = [];
    public array $buildings = [];
    public array $meetingRooms = [];
    
    // UI
    public string $error = '';
    public string $successMessage = '';

    public function mount(?Visit $visit = null)
    {
        $this->tenant = Auth::user()->tenant;
        
        if ($visit && $visit->id) {
            $this->visit = $visit;
            $this->mode = 'edit';
            $this->loadVisitData();
        } else {
            // Set default date/time
            $this->scheduled_date = now()->addDay()->format('Y-m-d');
            $this->scheduled_start_time = '09:00';
            $this->scheduled_end_time = '10:00';
        }
        
        $this->loadOptions();
    }

    protected function loadOptions()
    {
        // Load hosts (users who can be hosts)
        $this->hosts = User::where('tenant_id', $this->tenant->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($u) => ['id' => $u->id, 'name' => $u->name])
            ->toArray();
        
        // Load sub-tenants
        $this->subTenants = $this->tenant->subTenants()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($s) => ['id' => $s->id, 'name' => $s->name])
            ->toArray();
        
        // Load buildings
        $this->buildings = $this->tenant->buildings()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($b) => ['id' => $b->id, 'name' => $b->name])
            ->toArray();
        
        // Load meeting rooms
        $this->meetingRooms = $this->tenant->meetingRooms()
            ->with('building')
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn($r) => [
                'id' => $r->id, 
                'name' => $r->name . ($r->building ? ' (' . $r->building->name . ')' : '')
            ])
            ->toArray();
    }

    protected function loadVisitData()
    {
        if (!$this->visit) return;
        
        $visitor = $this->visit->visitor;
        $this->first_name = $visitor->first_name;
        $this->last_name = $visitor->last_name;
        $this->email = $visitor->email ?? '';
        $this->phone = $visitor->phone ?? '';
        $this->company = $visitor->company ?? '';
        
        $this->host_user_id = $this->visit->host_user_id;
        $this->sub_tenant_id = $this->visit->sub_tenant_id;
        $this->meeting_room_id = $this->visit->meeting_room_id;
        $this->building_id = $this->visit->building_id;
        $this->scheduled_date = $this->visit->scheduled_start->format('Y-m-d');
        $this->scheduled_start_time = $this->visit->scheduled_start->format('H:i');
        $this->scheduled_end_time = $this->visit->scheduled_end->format('H:i');
        $this->purpose = $this->visit->purpose ?? '';
    }

    public function render()
    {
        return view('livewire.visit-form');
    }

    // ========== STEP NAVIGATION ==========

    public function nextStep()
    {
        if ($this->step === 'visitor') {
            $this->validateVisitor();
            $this->step = 'details';
        } elseif ($this->step === 'details') {
            $this->validateDetails();
            $this->step = 'review';
        }
    }

    public function prevStep()
    {
        if ($this->step === 'details') {
            $this->step = 'visitor';
        } elseif ($this->step === 'review') {
            $this->step = 'details';
        }
    }

    // ========== VALIDATION ==========

    protected function validateVisitor()
    {
        $this->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:200',
        ]);
        
        // Check for existing visitor
        if ($this->email) {
            $this->existingVisitor = Visitor::where('tenant_id', $this->tenant->id)
                ->where('email', $this->email)
                ->first();
        }
    }

    protected function validateDetails()
    {
        $this->validate([
            'scheduled_date' => 'required|date|after_or_equal:today',
            'scheduled_start_time' => 'required',
            'scheduled_end_time' => 'required|after:scheduled_start_time',
            'purpose' => 'nullable|string|max:500',
        ]);
    }

    // ========== SAVE ==========

    public function save()
    {
        $this->validate();
        
        try {
            // Find or create visitor
            $visitor = $this->findOrCreateVisitor();
            
            // Prepare visit data
            $scheduledStart = \Carbon\Carbon::parse($this->scheduled_date . ' ' . $this->scheduled_start_time);
            $scheduledEnd = \Carbon\Carbon::parse($this->scheduled_date . ' ' . $this->scheduled_end_time);
            
            $visitData = [
                'visitor_id' => $visitor->id,
                'tenant_id' => $this->tenant->id,
                'host_user_id' => $this->host_user_id,
                'sub_tenant_id' => $this->sub_tenant_id,
                'meeting_room_id' => $this->meeting_room_id,
                'building_id' => $this->building_id,
                'scheduled_start' => $scheduledStart,
                'scheduled_end' => $scheduledEnd,
                'purpose' => $this->purpose,
                'status' => 'pre_registered',
            ];
            
            if ($this->mode === 'create') {
                $visitData['visit_code'] = Visit::generateVisitCode();
                $visit = Visit::create($visitData);
                
                // Dispatch notification
                SendPreRegistrationNotification::dispatch($visit);
                
                $this->successMessage = 'Visit scheduled successfully!';
            } else {
                $this->visit->update($visitData);
                $this->successMessage = 'Visit updated successfully!';
            }
            
            // Emit event and redirect
            $this->emit('visitSaved');
            
        } catch (\Exception $e) {
            $this->error = 'Failed to save visit: ' . $e->getMessage();
        }
    }

    protected function findOrCreateVisitor(): Visitor
    {
        // Try to find existing visitor
        $visitor = Visitor::where('tenant_id', $this->tenant->id)
            ->where(function ($q) {
                if ($this->email) {
                    $q->orWhere('email', $this->email);
                }
                if ($this->phone) {
                    $q->orWhere('phone', $this->phone);
                }
            })
            ->first();
        
        if ($visitor) {
            // Update existing visitor
            $visitor->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'company' => $this->company,
            ]);
            return $visitor;
        }
        
        // Create new visitor
        return Visitor::create([
            'tenant_id' => $this->tenant->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
        ]);
    }
}
