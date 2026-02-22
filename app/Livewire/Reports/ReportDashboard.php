<?php

namespace App\Livewire\Reports;

use App\Models\Meeting;
use App\Models\Tenant;
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
            ->get();

        return [
            'total' => $visits->count(),
            'checked_in' => $visits->whereNull('check_out_at')->count(),
            'avg_duration' => $visits->whereNotNull('check_out_at')->avg(fn ($v) => $v->getDurationInMinutes()) ?? 0,
            'by_method' => $visits->groupBy('check_in_method')->map->count(),
            'by_day' => $visits->groupBy(fn ($v) => $v->check_in_at->format('Y-m-d'))->map->count(),
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
            ->get();

        return [
            'total' => $meetings->count(),
            'completed' => $meetings->where('status', 'completed')->count(),
            'cancelled' => $meetings->where('status', 'cancelled')->count(),
            'avg_duration' => $meetings->where('status', 'completed')->avg(fn ($m) => $m->getDurationInMinutes()) ?? 0,
            'by_status' => $meetings->groupBy('status')->map->count(),
        ];
    }

    public function render()
    {
        return view('livewire.reports.report-dashboard', [
            'visitorStats' => $this->visitorStats,
            'meetingStats' => $this->meetingStats,
        ])->layout('layouts.app');
    }
}