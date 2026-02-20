<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function visitSummary(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $dateFrom = $request->date_from ?? Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ?? Carbon::now()->endOfDay();

        $visits = Visit::where('tenant_id', $tenantId)
            ->whereBetween('scheduled_start', [$dateFrom, $dateTo]);

        $total = $visits->count();
        
        $summary = [
            'total' => $total,
            'pre_registered' => (clone $visits)->where('status', 'pre_registered')->count(),
            'checked_in' => (clone $visits)->where('status', 'checked_in')->count(),
            'checked_out' => (clone $visits)->where('status', 'checked_out')->count(),
            'cancelled' => (clone $visits)->where('status', 'cancelled')->count(),
            'no_show' => (clone $visits)->where('status', 'no_show')->count(),
        ];

        // Average visit duration
        $checkedOut = Visit::where('tenant_id', $tenantId)
            ->whereBetween('scheduled_start', [$dateFrom, $dateTo])
            ->whereNotNull('checked_out_at')
            ->whereNotNull('checked_in_at')
            ->get();

        $avgDuration = $checkedOut->avg(fn($v) => $v->checked_in_at->diffInMinutes($v->checked_out_at));
        $summary['avg_duration_minutes'] = round($avgDuration ?? 0);

        // Daily trend
        $daily = Visit::where('tenant_id', $tenantId)
            ->whereBetween('scheduled_start', [$dateFrom, $dateTo])
            ->selectRaw('DATE(scheduled_start) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $summary['daily_trend'] = $daily;

        return response()->json($summary);
    }

    public function peakHours(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $dateFrom = $request->date_from ?? Carbon::now()->subMonths(3);
        $dateTo = $request->date_to ?? Carbon::now();

        $visits = Visit::where('tenant_id', $tenantId)
            ->whereBetween('scheduled_start', [$dateFrom, $dateTo])
            ->whereNotNull('checked_in_at')
            ->get();

        $hourlyCounts = array_fill(0, 24, 0);
        foreach ($visits as $visit) {
            $hour = $visit->checked_in_at->hour;
            $hourlyCounts[$hour]++;
        }

        $distribution = array_map(fn($count, $hour) => [
            'hour' => $hour,
            'label' => sprintf('%02d:00', $hour),
            'count' => $count,
        ], $hourlyCounts, array_keys($hourlyCounts));

        $peakHours = collect($hourlyCounts)
            ->map(fn($count, $hour) => ['hour' => $hour, 'label' => sprintf('%02d:00', $hour), 'count' => $count])
            ->sortByDesc('count')
            ->take(5)
            ->values();

        return response()->json([
            'hourly_distribution' => $distribution,
            'peak_hours' => $peakHours,
        ]);
    }

    public function visitorFrequency(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $topVisitors = Visit::where('tenant_id', $tenantId)
            ->selectRaw('visitor_id, COUNT(*) as visit_count')
            ->groupBy('visitor_id')
            ->orderByDesc('visit_count')
            ->limit(10)
            ->with('visitor')
            ->get()
            ->map(fn($v) => [
                'visitor' => $v->visitor,
                'visit_count' => $v->visit_count,
            ]);

        $returningCount = Visit::where('tenant_id', $tenantId)
            ->selectRaw('visitor_id')
            ->groupBy('visitor_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        $totalVisitors = Visit::where('tenant_id', $tenantId)
            ->distinct('visitor_id')
            ->count('visitor_id');

        return response()->json([
            'total_unique_visitors' => $totalVisitors,
            'returning_visitors' => $returningCount,
            'first_time_visitors' => $totalVisitors - $returningCount,
            'top_visitors' => $topVisitors,
        ]);
    }

    public function quickStats(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $today = Carbon::today();

        return response()->json([
            'today_visits' => Visit::where('tenant_id', $tenantId)->whereDate('scheduled_start', $today)->count(),
            'checked_in' => Visit::where('tenant_id', $tenantId)->where('status', 'checked_in')->count(),
            'scheduled' => Visit::where('tenant_id', $tenantId)->where('status', 'pre_registered')->count(),
            'this_week' => Visit::where('tenant_id', $tenantId)
                ->whereBetween('scheduled_start', [$today->startOfWeek(), $today->endOfWeek()])->count(),
            'this_month' => Visit::where('tenant_id', $tenantId)
                ->whereBetween('scheduled_start', [$today->startOfMonth(), $today->endOfMonth()])->count(),
        ]);
    }
}
