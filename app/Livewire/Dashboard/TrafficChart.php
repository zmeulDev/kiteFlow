<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;

class TrafficChart extends Component
{
    public $readyToLoad = false;
    public $hourlyData = [];

    public function loadChart()
    {
        // Get visitor count per hour for the last 12 hours
        $data = Visit::whereNotNull('checked_in_at')
            ->where('checked_in_at', '>=', now()->subHours(12))
            ->select(DB::raw('strftime("%H", checked_in_at) as hour'), DB::raw('count(*) as count'))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Format for ApexCharts
        $this->hourlyData = $data->pluck('count')->toArray();
        $this->readyToLoad = true;
    }

    public function render()
    {
        return view('livewire.dashboard.traffic-chart');
    }
}
