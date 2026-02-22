<?php

namespace App\Livewire\Reports;

use App\Models\AccessLog;
use App\Models\AccessPoint;
use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\ParkingRecord;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Livewire\Component;

class ReportDashboard extends Component
{
    public ?int $tenantId = null;
    public string $dateRange = 'week';
    public string $reportType = 'visitors';

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

    public function getVisitorStatsProperty(): array
    {
        if (!$this->tenantId) {
            return [];
        }

        $days = match($this->dateRange) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
            default => 7,
        };

        $visits = VisitorVisit::where('tenant_id', $this->tenantId)
            ->where('check_in_at', '>=', now()->subDays($days))
            ->with(['visitor', 'host'])
            ->get();

        $uniqueVisitors = $visits->pluck('visitor_id')->unique()->count();
        $blacklistedCount = Visitor::where('tenant_id', $this->tenantId)
            ->where('is_blacklisted', true)
            ->count();

        // Generate daily trend data for charts
        $dailyTrend = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayVisits = $visits->filter(fn ($v) => $v->check_in_at->format('Y-m-d') === $date);
            $dailyTrend[] = [
                'date' => $date,
                'count' => $dayVisits->count(),
                'checked_in' => $dayVisits->whereNull('check_out_at')->count(),
            ];
        }

        return [
            'total' => $visits->count(),
            'unique_visitors' => $uniqueVisitors,
            'checked_in' => $visits->whereNull('check_out_at')->where('status', 'checked_in')->count(),
            'checked_out' => $visits->whereNotNull('check_out_at')->count(),
            'no_show' => $visits->where('status', 'no_show')->count(),
            'cancelled' => $visits->where('status', 'cancelled')->count(),
            'avg_duration' => round($visits->whereNotNull('check_out_at')->avg(fn ($v) => $v->getDurationInMinutes()) ?? 0),
            'by_method' => $visits->groupBy('check_in_method')->map->count()->sortDesc(),
            'by_day' => $dailyTrend,
            'blacklisted_count' => $blacklistedCount,
            'recent_visits' => VisitorVisit::where('tenant_id', $this->tenantId)
                ->with(['visitor', 'host'])
                ->orderBy('check_in_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    public function getMeetingStatsProperty(): array
    {
        if (!$this->tenantId) {
            return [];
        }

        $days = match($this->dateRange) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
            default => 7,
        };

        $meetings = Meeting::where('tenant_id', $this->tenantId)
            ->where('start_at', '>=', now()->subDays($days))
            ->with(['meetingRoom', 'host'])
            ->get();

        // Stats by status
        $byStatus = [
            'scheduled' => $meetings->where('status', 'scheduled')->count(),
            'in_progress' => $meetings->where('status', 'in_progress')->count(),
            'completed' => $meetings->where('status', 'completed')->count(),
            'cancelled' => $meetings->where('status', 'cancelled')->count(),
            'no_show' => $meetings->where('status', 'no_show')->count(),
        ];

        // Stats by meeting room
        $byRoom = $meetings->groupBy('meeting_room_id')
            ->map(fn ($group) => [
                'room' => $group->first()->meetingRoom?->name ?? 'Unknown',
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        // Stats by host
        $byHost = $meetings->groupBy('host_id')
            ->map(fn ($group) => [
                'host' => $group->first()->host?->name ?? 'Unknown',
                'count' => $group->count(),
            ])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        return [
            'total' => $meetings->count(),
            'by_status' => $byStatus,
            'avg_duration' => round($meetings->where('status', 'completed')->avg(fn ($m) => $m->getDurationInMinutes()) ?? 0),
            'by_room' => $byRoom,
            'by_host' => $byHost,
            'upcoming' => Meeting::where('tenant_id', $this->tenantId)
                ->where('start_at', '>', now())
                ->where('status', 'scheduled')
                ->orderBy('start_at', 'asc')
                ->limit(5)
                ->get(),
        ];
    }

    public function getAccessStatsProperty(): array
    {
        if (!$this->tenantId) {
            return [];
        }

        $days = match($this->dateRange) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
            default => 7,
        };

        $logs = AccessLog::where('tenant_id', $this->tenantId)
            ->where('accessed_at', '>=', now()->subDays($days))
            ->with(['accessPoint'])
            ->get();

        // Stats by result
        $byResult = [
            'granted' => $logs->where('result', 'granted')->count(),
            'denied' => $logs->where('result', 'denied')->count(),
        ];

        // Stats by direction
        $byDirection = [
            'entry' => $logs->where('direction', 'entry')->count(),
            'exit' => $logs->where('direction', 'exit')->count(),
        ];

        // Stats by access point
        $byAccessPoint = $logs->groupBy('access_point_id')
            ->map(fn ($group) => [
                'access_point' => $group->first()->accessPoint?->name ?? 'Unknown',
                'count' => $group->count(),
                'granted' => $group->where('result', 'granted')->count(),
                'denied' => $group->where('result', 'denied')->count(),
            ])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        // Denial reasons
        $denialReasons = $logs->where('result', 'denied')
            ->pluck('denial_reason')
            ->filter()
            ->groupBy(fn ($reason) => $reason)
            ->map->count()
            ->sortDesc()
            ->take(5);

        return [
            'total' => $logs->count(),
            'by_result' => $byResult,
            'by_direction' => $byDirection,
            'by_access_point' => $byAccessPoint,
            'denial_reasons' => $denialReasons,
            'recent' => AccessLog::where('tenant_id', $this->tenantId)
                ->with(['accessPoint', 'subject'])
                ->orderBy('accessed_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    public function getParkingStatsProperty(): array
    {
        if (!$this->tenantId) {
            return [];
        }

        $days = match($this->dateRange) {
            'week' => 7,
            'month' => 30,
            'quarter' => 90,
            'year' => 365,
            default => 7,
        };

        $records = ParkingRecord::where('tenant_id', $this->tenantId)
            ->where('entry_at', '>=', now()->subDays($days))
            ->with(['parkingSpot'])
            ->get();

        $totalRevenue = $records->where('is_paid', true)->sum('fee');
        $pendingRevenue = $records->where('is_paid', false)->whereNotNull('exit_at')->sum('fee');

        // Stats by vehicle type
        $byVehicleType = $records->groupBy('vehicle_type')
            ->map->count()
            ->sortDesc();

        return [
            'total' => $records->count(),
            'currently_parked' => ParkingRecord::where('tenant_id', $this->tenantId)
                ->whereNull('exit_at')
                ->count(),
            'avg_duration' => round($records->whereNotNull('exit_at')->avg(fn ($r) => $r->getDurationInMinutes()) ?? 0),
            'total_revenue' => $totalRevenue,
            'pending_revenue' => $pendingRevenue,
            'paid_records' => $records->where('is_paid', true)->count(),
            'by_vehicle_type' => $byVehicleType,
            'recent' => ParkingRecord::where('tenant_id', $this->tenantId)
                ->with(['parkingSpot'])
                ->orderBy('entry_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    public function render()
    {
        return view('livewire.reports.report-dashboard', [
            'visitorStats' => $this->visitorStats,
            'meetingStats' => $this->meetingStats,
            'accessStats' => $this->accessStats,
            'parkingStats' => $this->parkingStats,
        ])->layout('layouts.app');
    }
}