<x-slot name="title">Dashboard</x-slot>

<div x-data="{
    time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false }),
    date: new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' })
}" x-init="setInterval(() => { time = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false }) }, 1000)">

    <!-- Hero Section -->
    <div class="dashboard-hero dashboard-animate activity-section">
        <div class="px-6">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div>
                    <p class="text-secondary font-semibold text-sm uppercase tracking-wider mb-2">Overview</p>
                    <h1 class="text-3xl font-extrabold tracking-tight">Dashboard</h1>
                    <p class="text-secondary mt-2 max-w-md">Monitor visitor activity and manage check-ins across all your buildings.</p>
                </div>
                <div class="text-right">
                    <div class="dashboard-time" x-text="time"></div>
                    <div class="dashboard-date" x-text="date"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stat-grid">
        <!-- Today's Visitors -->
        <div class="stat-card stat-card--blue dashboard-animate dashboard-delay-1">
            <div class="stat-indicator">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="stat-value">{{ $todayVisits }}</div>
            <div class="stat-label">Today's Visitors</div>
            @if($visitsTrend !== null)
            <div class="stat-trend {{ $visitsTrend >= 0 ? 'stat-trend--up' : 'stat-trend--down' }}">
                @if($visitsTrend >= 0)
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                </svg>
                @else
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
                @endif
                {{ abs($visitsTrend) }}% vs yesterday
            </div>
            @endif
        </div>

        <!-- Active Visits -->
        <div class="stat-card stat-card--green dashboard-animate dashboard-delay-2">
            <div class="stat-indicator">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="stat-value">{{ $activeVisits }}</div>
            <div class="stat-label">Currently Active</div>
            @if($activeVisits > 0)
            <div class="stat-trend stat-trend--live">
                <span class="stat-live-dot"></span>
                Live
            </div>
            @endif
        </div>

        <!-- Unique Visitors -->
        <div class="stat-card stat-card--amber dashboard-animate dashboard-delay-3">
            <div class="stat-indicator">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
            <div class="stat-value">{{ $totalVisitors }}</div>
            <div class="stat-label">Unique Visitors</div>
            @if($visitorsTrend !== null)
            <div class="stat-trend {{ $visitorsTrend >= 0 ? 'stat-trend--up' : 'stat-trend--down' }}">
                @if($visitorsTrend >= 0)
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                </svg>
                @else
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
                @endif
                {{ abs($visitorsTrend) }}% vs yesterday
            </div>
            @endif
        </div>

        <!-- Checked Out -->
        <div class="stat-card stat-card--violet dashboard-animate dashboard-delay-4">
            <div class="stat-indicator">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </div>
            <div class="stat-value">{{ $checkedOutToday }}</div>
            <div class="stat-label">Checked Out</div>
            @if($peakHour !== '-')
            <div class="stat-trend stat-trend--info">
                Peak: {{ $peakHour }}
            </div>
            @endif
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="activity-section dashboard-animate dashboard-delay-5">
        <div class="activity-header">
            <div>
                <h2 class="activity-title">Recent Activity</h2>
                <p class="text-secondary text-sm mt-0.5">Latest visitor check-ins and check-outs</p>
            </div>
            <div class="quick-actions">
                @can('viewVisits', App\Models\User::class)
                <a href="{{ route('admin.visits') }}" class="quick-action-btn">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    View All Visits
                </a>
                @endcan
            </div>
        </div>

        @if($recentVisits->count() > 0)
        <div class="activity-list">
            @foreach($recentVisits as $visit)
            <div class="activity-item">
                <div class="activity-avatar {{ $visit->status === 'checked_out' ? 'activity-avatar--checked-out' : '' }}">
                    {{ strtoupper(substr($visit->visitor->first_name ?? 'V', 0, 1) . substr($visit->visitor->last_name ?? '', 0, 1)) }}
                </div>
                <div class="activity-content">
                    <div class="activity-visitor">
                        {{ $visit->visitor->full_name ?? 'Unknown Visitor' }}
                        @if($visit->visitor->company)
                        <span class="text-secondary font-normal">from {{ $visit->visitor->company->name }}</span>
                        @endif
                    </div>
                    <div class="activity-meta">
                        <span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            {{ $visit->host?->name ?? $visit->host_name ?? 'No host' }}
                        </span>
                        <span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $visit->entrance->name }} · {{ $visit->entrance->building->name }}
                        </span>
                    </div>
                </div>
                <div class="activity-time">
                    @if($visit->status === 'checked_in')
                    <span class="status-badge status-badge--active">Checked In</span>
                    @elseif($visit->status === 'checked_out')
                    <span class="status-badge status-badge--completed">Completed</span>
                    @else
                    <span class="status-badge status-badge--pending">Pending</span>
                    @endif
                    <div class="mt-2">
                        <div class="activity-time-value">{{ $visit->check_in_at?->format('g:i A') ?? '—' }}</div>
                        <div class="activity-time-label">{{ $visit->check_in_at?->format('M j') ?? '' }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="activity-empty">
            <div class="activity-empty-icon">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <p class="activity-empty-text">No visitor activity yet today</p>
        </div>
        @endif
    </div>
</div>
