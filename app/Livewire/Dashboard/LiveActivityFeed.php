<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Visit;
use Livewire\Attributes\On;

class LiveActivityFeed extends Component
{
    #[On('visitor-pre-registered')]
    #[On('visitor-updated')]
    public function refresh()
    {
        // Handled by render
    }

    public function render()
    {
        // Relying on TenantScope for automatic isolation
        $activities = Visit::with(['visitor', 'tenant'])
            ->latest()
            ->take(10)
            ->get();

        return view('livewire.dashboard.live-activity-feed', [
            'activities' => $activities
        ]);
    }
}
