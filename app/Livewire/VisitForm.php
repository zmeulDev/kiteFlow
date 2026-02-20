<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\MeetingRoom;
use App\Models\Building;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VisitForm extends Component
{
    public string $step = 'visitor';
    public string $search = '';
    public ?Visitor $foundVisitor = null;
    public ?int $selectedVisitorId = null;
    
    // Form fields
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    
    // Visit details
    public ?int $host_user_id = '';
    public ?int $meeting_room_id = '';
    public ?int $building_id = '';
    public string $scheduled_date = '';
    public string $scheduled_start = '';
    public string $scheduled_end = '';
    public string $purpose = '';

    public array $visitors = [];
    public array $hosts = [];
    public array $rooms = [];
    public array $buildings = [];

    public function mount()
    {
        $this->loadOptions();
        $this->scheduled_date = now()->format('Y-m-d');
    }

    public function loadOptions()
    {
        $tenantId = Auth::user()->tenant_id;
        
        $this->hosts = User::where('tenant_id', $tenantId)->get(['id', 'name'])->toArray();
        $this->buildings = Building::where('tenant_id', $tenantId)->get(['id', 'name'])->toArray();
        $this->rooms = MeetingRoom::where('tenant_id', $tenantId)->where('is_active', true)->get(['id', 'name', 'building_id'])->toArray();
    }

    public function searchVisitors()
    {
        if (strlen($this->search) < 2) return;
        
        $tenantId = Auth::user()->tenant_id;
        
        $this->visitors = Visitor::where('tenant_id', $tenantId)
            ->where(function($q) {
                $q->where('first_name', 'like', "%{$this->search}%")
                  ->orWhere('last_name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            })
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function selectVisitor($id)
    {
        $this->foundVisitor = Visitor::find($id);
        $this->selectedVisitorId = $id;
        $this->first_name = $this->foundVisitor->first_name;
        $this->last_name = $this->foundVisitor->last_name;
        $this->email = $this->foundVisitor->email ?? '';
        $this->phone = $this->foundVisitor->phone ?? '';
        $this->company = $this->foundVisitor->company ?? '';
        $this->step = 'details';
    }

    public function createNewVisitor()
    {
        $this->step = 'details';
    }

    public function save()
    {
        $tenantId = Auth::user()->tenant_id;
        
        // Create or update visitor
        if ($this->selectedVisitorId) {
            $visitor = Visitor::find($this->selectedVisitorId);
            $visitor->update([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company' => $this->company,
            ]);
        } else {
            $visitor = Visitor::create([
                'tenant_id' => $tenantId,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'company' => $this->company,
            ]);
        }

        // Create visit
        $scheduledStart = "{$this->scheduled_date}T{$this->scheduled_start}";
        $scheduledEnd = "{$this->scheduled_date}T{$this->scheduled_end}";

        Visit::create([
            'visitor_id' => $visitor->id,
            'tenant_id' => $tenantId,
            'host_user_id' => $this->host_user_id ?: null,
            'meeting_room_id' => $this->meeting_room_id ?: null,
            'building_id' => $this->building_id ?: null,
            'visit_code' => Visit::generateVisitCode(),
            'scheduled_start' => $scheduledStart,
            'scheduled_end' => $scheduledEnd,
            'purpose' => $this->purpose,
            'status' => 'pre_registered',
        ]);

        session()->flash('message', 'Visit scheduled successfully!');
        
        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.visit-form');
    }
}
