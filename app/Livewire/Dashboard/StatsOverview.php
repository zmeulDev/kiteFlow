<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Visit;
use Livewire\Attributes\On;

class StatsOverview extends Component
{
    public $activeCount = 0;
    public $todayCount = 0;
    public $expectedCount = 0;
    public $percentageIncrease = 12;
    public $readyToLoad = true;

    public function mount()
    {
        $this->loadStats();
    }

    #[On('visitor-pre-registered')]
    #[On('visitor-updated')]
    #[On('visitor-deleted')]
    public function loadStats()
    {
        // Relying on TenantScope for automatic isolation
        $this->activeCount = Visit::whereNull('checked_out_at')->whereNotNull('checked_in_at')->count();
        $this->todayCount = Visit::whereDate('created_at', today())->count();
        $this->expectedCount = Visit::whereDate('scheduled_at', today())->whereNull('checked_in_at')->count();
        
        $this->percentageIncrease = 12; // Placeholder
        $this->readyToLoad = true;
    }

    public function render()
    {
        return view('livewire.dashboard.stats-overview');
    }
}
