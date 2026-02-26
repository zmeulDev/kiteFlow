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

    protected function getScopedVisitQuery()
    {
        $query = Visit::query();
        if (!auth()->user()->isAdmin()) {
            if (auth()->user()->role === 'viewer') {
                $query->where('host_id', auth()->id());
            } else {
                $query->whereHas('host', function ($q) {
                    $q->where('company_id', auth()->user()->company_id);
                });
            }
        }
        return $query;
    }

    protected function loadStats(): void
    {
        // Today's stats
        $this->todayVisits = $this->getScopedVisitQuery()->whereDate('check_in_at', today())->count();
        $this->activeVisits = $this->getScopedVisitQuery()->where('status', 'checked_in')->count();
        $this->totalVisitors = $this->getScopedVisitQuery()->whereDate('check_in_at', today())
            ->distinct('visitor_id')
            ->count('visitor_id');
        $this->checkedOutToday = $this->getScopedVisitQuery()->where('status', 'checked_out')
            ->whereDate('check_out_at', today())
            ->count();

        // Calculate trends (comparison to yesterday)
        $yesterdayVisits = $this->getScopedVisitQuery()->whereDate('check_in_at', Carbon::yesterday())->count();
        $yesterdayVisitors = $this->getScopedVisitQuery()->whereDate('check_in_at', Carbon::yesterday())
            ->distinct('visitor_id')
            ->count('visitor_id');

        if ($yesterdayVisits > 0) {
            $this->visitsTrend = (int) round((($this->todayVisits - $yesterdayVisits) / $yesterdayVisits) * 100);
        }

        if ($yesterdayVisitors > 0) {
            $this->visitorsTrend = (int) round((($this->totalVisitors - $yesterdayVisitors) / $yesterdayVisitors) * 100);
        }

        // Find peak hour today
        $peakHourData = $this->getScopedVisitQuery()->whereDate('check_in_at', today())
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
        $recentVisits = $this->getScopedVisitQuery()->with(['visitor.company', 'entrance.building', 'host'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        return view('livewire.admin.dashboard', compact('recentVisits'));
    }
}
