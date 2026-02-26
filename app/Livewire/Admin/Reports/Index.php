<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Visit;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Index extends Component
{
    public int $todayVisits = 0;
    public int $activeVisits = 0;
    public int $totalVisitors = 0;
    public int $thisMonthVisits = 0;

    public function mount(): void
    {
        $this->loadBusinessReports();
    }

    public function loadBusinessReports(): void
    {
        $user = auth()->user();
        $query = Visit::query();

        // If not a system admin, scope to their company
        if (!$user->isAdmin()) {
            if ($user->role === 'viewer') {
                $query->where('host_id', $user->id);
            } else {
                $query->whereHas('host', function ($q) use ($user) {
                    $q->where('company_id', $user->company_id);
                });
            }
        }

        $this->todayVisits = (clone $query)->whereDate('check_in_at', today())->count();
        $this->activeVisits = (clone $query)->where('status', 'checked_in')->count();
        $this->totalVisitors = (clone $query)->distinct('visitor_id')->count('visitor_id');
        $this->thisMonthVisits = (clone $query)->whereMonth('created_at', now()->month)
                                              ->whereYear('created_at', now()->year)
                                              ->count();
    }

    public function render()
    {
        return view('livewire.admin.reports.index');
    }
}
