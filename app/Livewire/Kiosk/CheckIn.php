<?php

namespace App\Livewire\Kiosk;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\Location;
use App\Notifications\VisitorArrived;
use App\Notifications\SecurityAlert;
use Illuminate\Support\Str;

class CheckIn extends Component
{
    public Tenant $tenant;
    
    public $first_name;
    public $last_name;
    public $selected_company;
    public $selected_location;
    public $purpose;
    public $photo;
    public $currentStep = 1;

    public function mount(Tenant $tenant)
    {
        $this->tenant = $tenant;
        
        // If not a hub, auto-select the company
        if (!$this->tenant->is_hub) {
            $this->selected_company = $this->tenant->id;
        }
    }

    public function nextStep()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'selected_company' => 'required|exists:tenants,id',
            'selected_location' => 'nullable|exists:locations,id',
            'purpose' => 'required|string',
        ]);

        $targetTenant = Tenant::find($this->selected_company);
        if ($targetTenant?->settings['require_photo'] ?? false) {
            $this->currentStep = 2;
        } else {
            $this->submit();
        }
    }

    public function submit()
    {
        $this->validate([
            'selected_company' => 'required|exists:tenants,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'purpose' => 'required|string',
        ]);

        $targetTenant = Tenant::findOrFail($this->selected_company);

        if ($targetTenant->hasReachedLimit()) {
            $this->dispatch('notify', type: 'error', message: 'This office has reached its visitor limit for this month.');
            $this->currentStep = 1;
            return;
        }

        $visitor = Visitor::updateOrCreate(
            ['email' => strtolower($this->first_name . '.' . $this->last_name) . '@example.com'],
            [
                'tenant_id' => $this->selected_company,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
            ]
        );

        $host = $targetTenant->users()->first();

        $photoPath = null;
        if ($this->photo) {
            $photoPath = 'photos/' . Str::random(10) . '.png';
        }

        $visit = Visit::create([
            'tenant_id' => $this->selected_company,
            'location_id' => $this->selected_location,
            'check_in_token' => Str::random(32),
            'visitor_id' => $visitor->id,
            'user_id' => $host?->id,
            'purpose' => $this->purpose,
            'photo_path' => $photoPath,
            'checked_in_at' => now(),
        ]);

        if ($host) {
            $host->notify(new VisitorArrived($visit));
            if ($visitor->is_flagged) {
                $host->notify(new SecurityAlert($visit));
            }
        }

        $this->reset(['first_name', 'last_name', 'selected_location', 'purpose', 'photo', 'currentStep']);
        if ($this->tenant->is_hub) {
            $this->reset('selected_company');
        }
        
        $this->dispatch('notify', type: 'success', message: __('messages.success') . " " . ($host->name ?? 'Staff') . " " . __('messages.notified'));
    }

    public function render()
    {
        $companies = $this->tenant->is_hub 
            ? $this->tenant->children 
            : collect([$this->tenant]);

        return view('livewire.kiosk.check-in', [
            'companies' => $companies,
            'locations' => $this->selected_company ? Location::where('tenant_id', $this->selected_company)->get() : collect(),
            'selectedTenantModel' => $this->selected_company ? Tenant::find($this->selected_company) : $this->tenant
        ]);
    }
}
