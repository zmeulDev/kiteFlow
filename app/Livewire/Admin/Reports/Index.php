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

    public array $visitsByCompany = [];
    public array $visitsByHost = [];
    public array $visitsByStatus = [];
    public array $visitsByEntrance = [];

    public function mount(): void
    {
        $this->loadBusinessReports();
    }

    public function loadBusinessReports(): void
    {
        $user = auth()->user();
        $query = Visit::query();

        // If not a system admin or authorized global manager, scope to their company
        if (!$user->canManageAllTenants()) {
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

        // Advanced Stats
        $this->visitsByStatus = (clone $query)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get()
            ->toArray();

        // Load entrances securely without needing joins by eager loading entrance or just grouping by entrance_id
        $this->visitsByEntrance = (clone $query)
            ->selectRaw('entrance_id, count(*) as count')
            ->whereNotNull('entrance_id')
            ->groupBy('entrance_id')
            ->with('entrance')
            ->orderByDesc('count')
            ->take(5)
            ->get()
            ->map(function ($visit) {
                return [
                    'name' => optional($visit->entrance)->name ?? 'Unknown',
                    'count' => $visit->count,
                ];
            })->toArray();

        // Load hosts securely
        $this->visitsByHost = (clone $query)
            ->selectRaw('host_id, count(*) as count')
            ->groupBy('host_id')
            ->with('host')
            ->orderByDesc('count')
            ->take(5)
            ->get()
            ->map(function ($visit) {
                return [
                    'name' => optional($visit->host)->name ?? 'Unknown',
                    'count' => $visit->count,
                ];
            })->toArray();

        // Load companies (Global roles only)
        if ($user->canManageAllTenants()) {
            $this->visitsByCompany = (clone $query)
                ->selectRaw('companies.name as company_name, count(visits.id) as count')
                ->join('users', 'visits.host_id', '=', 'users.id')
                ->join('companies', 'users.company_id', '=', 'companies.id')
                ->groupBy('companies.name')
                ->orderByDesc('count')
                ->take(5)
                ->get()
                ->map(function ($row) {
                    return [
                        'name' => $row->company_name ?? 'Unknown',
                        'count' => $row->count,
                    ];
                })->toArray();
        }
    }

    public function render()
    {
        return view('livewire.admin.reports.index');
    }
}
