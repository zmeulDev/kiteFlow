<?php

namespace App\Livewire\Kiosk;

use Livewire\Component;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\Tenant;

class CheckOut extends Component
{
    public $email;
    public Tenant $tenant;

    public function mount(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    public function submit()
    {
        $this->validate([
            'email' => 'required|email',
        ]);

        $visitor = Visitor::where('email', $this->email)->first();

        if (!$visitor) {
            $this->dispatch('notify', type: 'error', message: 'Visitor not found with that email.');
            return;
        }

        // Only allow checkout for visits in this Hub/Office
        $tenantIds = $this->tenant->is_hub 
            ? $this->tenant->children->pluck('id')->push($this->tenant->id)
            : [$this->tenant->id];

        $visit = Visit::where('visitor_id', $visitor->id)
            ->whereIn('tenant_id', $tenantIds)
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->latest()
            ->first();

        if (!$visit) {
            $this->dispatch('notify', type: 'error', message: 'No active visit found for this email in this building.');
            return;
        }

        $visit->update([
            'checked_out_at' => now(),
        ]);

        $this->reset('email');
        $this->dispatch('notify', type: 'success', message: 'Thank you! You have been checked out successfully.');
    }

    public function render()
    {
        return view('livewire.kiosk.check-out');
    }
}
