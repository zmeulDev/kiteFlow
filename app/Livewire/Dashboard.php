<?php

namespace App\Livewire;

use App\Models\Meeting;
use App\Models\Tenant;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    use WithPagination;

    public ?int $tenantId = null;
    public string $search = '';
    public string $period = 'today';

    protected $queryString = ['search', 'period'];

    public function mount(?int $tenantId = null): void
    {
        $tenantId = $tenantId
            ?? request()->attributes->get('tenant_id')
            ?? auth()->user()?->getCurrentTenant()?->id;

        // Verify user has access to this tenant
        if ($tenantId && auth()->check()) {
            $user = auth()->user();
            if (!$user->belongsToOneOfTenants([$tenantId])) {
                abort(403, 'You do not have access to this tenant data.');
            }
        }

        $this->tenantId = $tenantId;
    }

    public function getStatsProperty(): array
    {
        if (!$this->tenantId) {
            return [];
        }

        $period = match($this->period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->startOfDay(),
        };

        return [
            'total_visitors' => VisitorVisit::where('tenant_id', $this->tenantId)
                ->where('check_in_at', '>=', $period)
                ->count(),
            'currently_in' => VisitorVisit::where('tenant_id', $this->tenantId)
                ->whereNull('check_out_at')
                ->where('status', 'checked_in')
                ->count(),
            'meetings_today' => Meeting::where('tenant_id', $this->tenantId)
                ->whereDate('start_at', today())
                ->count(),
            'expected' => Meeting::where('tenant_id', $this->tenantId)
                ->whereDate('start_at', today())
                ->where('start_at', '>', now())
                ->where('status', 'scheduled')
                ->count(),
            'trend' => [
                'visitors' => $this->calculateTrend('visitors'),
                'checked_in' => $this->calculateTrend('checked_in'),
            ],
        ];
    }

    private function calculateTrend(string $type): float
    {
        $currentPeriod = now()->subDays(7);
        $previousPeriod = now()->subDays(14);

        $current = match($type) {
            'visitors' => VisitorVisit::where('tenant_id', $this->tenantId)
                ->where('check_in_at', '>=', $currentPeriod)->count(),
            'checked_in' => VisitorVisit::where('tenant_id', $this->tenantId)
                ->where('check_in_at', '>=', $currentPeriod)->whereNull('check_out_at')->count(),
            default => 0,
        };

        $previous = match($type) {
            'visitors' => VisitorVisit::where('tenant_id', $this->tenantId)
                ->whereBetween('check_in_at', [$previousPeriod, $currentPeriod])->count(),
            'checked_in' => VisitorVisit::where('tenant_id', $this->tenantId)
                ->whereBetween('check_in_at', [$previousPeriod, $currentPeriod])->whereNull('check_out_at')->count(),
            default => 0,
        };

        if ($previous === 0) return $current > 0 ? 100 : 0;
        return round((($current - $previous) / $previous) * 100, 1);
    }

    public function getRecentVisitsProperty()
    {
        if (!$this->tenantId) return collect();

        return VisitorVisit::with(['visitor', 'host'])
            ->where('tenant_id', $this->tenantId)
            ->when($this->search, function ($query) {
                $query->whereHas('visitor', fn($q) => 
                    $q->where('first_name', 'like', "%{$this->search}%")
                      ->orWhere('last_name', 'like', "%{$this->search}%")
                );
            })
            ->orderBy('check_in_at', 'desc')
            ->limit(8)
            ->get();
    }

    public function getUpcomingMeetingsProperty()
    {
        if (!$this->tenantId) return collect();

        return Meeting::with(['meetingRoom', 'host'])
            ->where('tenant_id', $this->tenantId)
            ->where('start_at', '>=', now())
            ->where('status', 'scheduled')
            ->orderBy('start_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'stats' => $this->stats,
            'recentVisits' => $this->recentVisits,
            'upcomingMeetings' => $this->upcomingMeetings,
        ]);
    }
}