<?php

namespace App\Livewire\Kiosk;

use Livewire\Component;
use App\Models\Visit;
use App\Notifications\VisitorArrived;

class FastPass extends Component
{
    public $token;
    public $visit;
    public $isProcessed = false;

    public function mount($token)
    {
        $this->token = $token;
        
        // CRITICAL: Must bypass TenantScope for ALL relationships because this is a public pass
        // which might be viewed while authenticated as a different tenant, or not authenticated at all.
        $this->visit = Visit::withoutGlobalScopes()
            ->with([
                'visitor' => fn($q) => $q->withoutGlobalScopes(),
                'host' => fn($q) => $q->withoutGlobalScopes(),
                'tenant'
            ])
            ->where('check_in_token', $token)
            ->first();

        if ($this->visit) {
            // Task 4: Fast Pass expiration (24h after scheduled time)
            if ($this->visit->scheduled_at && $this->visit->scheduled_at->addHours(24)->isPast()) {
                $this->visit = null;
                return;
            }

            // Task 4: Already checked in state
            if ($this->visit->checked_in_at) {
                $this->isProcessed = true;
                session()->flash('status', 'already_checked_in');
            }
        }
    }

    public function confirmCheckIn()
    {
        if (!$this->visit) return;

        $this->visit->update([
            'checked_in_at' => now(),
        ]);

        // Re-notify host
        if ($this->visit->host) {
            $this->visit->host->notify(new VisitorArrived($this->visit));
        }

        $this->isProcessed = true;
        $this->dispatch('notify', type: 'success', message: "Fast Pass active! " . ($this->visit->host->name ?? 'Staff') . " has been notified of your arrival.");
    }

    public function render()
    {
        return view('livewire.kiosk.fast-pass');
    }
}
