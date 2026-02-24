<?php

namespace App\Livewire\Admin;

use App\Models\Visit;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Dashboard extends Component
{
    public int $todayVisits = 0;
    public int $activeVisits = 0;
    public int $totalVisitors = 0;
    public int $checkedOutToday = 0;

    // Trend data (comparison to yesterday)
    public ?int $visitsTrend = null;
    public ?int $visitorsTrend = null;

    // Peak hours data
    public string $peakHour = '-';

    public function mount(): void
    {
        $this->loadStats();
    }

    protected function loadStats(): void
    {
        // Today's stats
        $this->todayVisits = Visit::whereDate('check_in_at', today())->count();
        $this->activeVisits = Visit::where('status', 'checked_in')->count();
        $this->totalVisitors = Visit::whereDate('check_in_at', today())
            ->distinct('visitor_id')
            ->count('visitor_id');
        $this->checkedOutToday = Visit::where('status', 'checked_out')
            ->whereDate('check_out_at', today())
            ->count();

        // Calculate trends (comparison to yesterday)
        $yesterdayVisits = Visit::whereDate('check_in_at', Carbon::yesterday())->count();
        $yesterdayVisitors = Visit::whereDate('check_in_at', Carbon::yesterday())
            ->distinct('visitor_id')
            ->count('visitor_id');

        if ($yesterdayVisits > 0) {
            $this->visitsTrend = (int) round((($this->todayVisits - $yesterdayVisits) / $yesterdayVisits) * 100);
        }

        if ($yesterdayVisitors > 0) {
            $this->visitorsTrend = (int) round((($this->totalVisitors - $yesterdayVisitors) / $yesterdayVisitors) * 100);
        }

        // Find peak hour today
        $peakHourData = Visit::whereDate('check_in_at', today())
            ->selectRaw("strftime('%H', check_in_at) as hour, COUNT(*) as count")
            ->whereNotNull('check_in_at')
            ->groupBy('hour')
            ->orderByDesc('count')
            ->first();

        if ($peakHourData) {
            $this->peakHour = sprintf('%02d:00', $peakHourData->hour);
        }
    }

    public function render()
    {
        $recentVisits = Visit::with(['visitor.company', 'entrance.building', 'host'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('livewire.admin.dashboard', compact('recentVisits'));
    }
}
