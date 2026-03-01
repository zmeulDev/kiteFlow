<x-slot name="title">Reports</x-slot>

<div class="space-y-6">
    {{-- Hero Header --}}
    <div class="dashboard-hero dashboard-animate activity-section">
        <div class="px-6">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
                <div>
                    <p class="text-secondary font-semibold text-sm uppercase tracking-wider mb-2">Analytics</p>
                    <h1 class="text-3xl font-extrabold tracking-tight">Reports</h1>
                    <p class="text-secondary mt-2 max-w-md">View visitation metrics and analytics across all buildings.</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-extrabold tracking-tight" style="color: var(--text-primary);">📊</div>
                    <p class="text-secondary text-sm mt-1">{{ now()->format('F Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="stat-grid">
        {{-- Today's Visits --}}
        <div class="stat-card stat-card--blue dashboard-animate dashboard-delay-1">
            <div class="stat-indicator">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="stat-value">{{ $todayVisits }}</div>
            <div class="stat-label">Expected Today</div>
        </div>

        {{-- Active Visits --}}
        <div class="stat-card stat-card--green dashboard-animate dashboard-delay-2">
            <div class="stat-indicator">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="stat-value">{{ $activeVisits }}</div>
            <div class="stat-label">Currently On-Site</div>
            @if($activeVisits > 0)
            <div class="stat-trend stat-trend--live">
                <span class="stat-live-dot"></span>
                Live
            </div>
            @endif
        </div>

        {{-- Total Unique Visitors --}}
        <div class="stat-card stat-card--violet dashboard-animate dashboard-delay-3">
            <div class="stat-indicator">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div class="stat-value">{{ $totalVisitors }}</div>
            <div class="stat-label">Unique Visitors</div>
        </div>

        {{-- Monthly Visits --}}
        <div class="stat-card stat-card--amber dashboard-animate dashboard-delay-4">
            <div class="stat-indicator">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <div class="stat-value">{{ $thisMonthVisits }}</div>
            <div class="stat-label">Visits This Month</div>
        </div>
    </div>

    {{-- Analytics Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 dashboard-animate dashboard-delay-5">

        {{-- Top Hosts --}}
        <div class="activity-section">
            <div class="activity-header">
                <div>
                    <h2 class="activity-title">Top Hosts</h2>
                    <p class="text-secondary text-sm mt-0.5">Most frequent visit recipients</p>
                </div>
                <div class="icon-container icon-container--coral" style="width: 40px; height: 40px; border-radius: 10px;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            @if(count($visitsByHost) > 0)
                @php $maxHost = max(array_column($visitsByHost, 'count')); @endphp
                <div class="p-6 space-y-5">
                    @foreach($visitsByHost as $host)
                        <div class="report-bar-item">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-sm" style="color: var(--text-primary);">{{ $host['name'] }}</span>
                                <span class="text-xs font-bold px-2 py-1 rounded-full" style="background: rgba(255, 75, 75, 0.1); color: var(--primary);">{{ $host['count'] }} visits</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ ($host['count'] / max($maxHost, 1)) * 100 }}%"></div>
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
                    <p class="activity-empty-text">No host data available</p>
                </div>
            @endif
        </div>

        {{-- Entrance Usage --}}
        <div class="activity-section">
            <div class="activity-header">
                <div>
                    <h2 class="activity-title">Entrance Usage</h2>
                    <p class="text-secondary text-sm mt-0.5">Check-in distribution by entrance</p>
                </div>
                <div class="icon-container icon-container--blue" style="width: 40px; height: 40px; border-radius: 10px;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            @if(count($visitsByEntrance) > 0)
                @php $maxEntrance = max(array_column($visitsByEntrance, 'count')); @endphp
                <div class="p-6 space-y-5">
                    @foreach($visitsByEntrance as $entrance)
                        <div class="report-bar-item">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-sm" style="color: var(--text-primary);">{{ $entrance['name'] }}</span>
                                <span class="text-xs font-bold px-2 py-1 rounded-full" style="background: rgba(59, 130, 246, 0.1); color: #2563eb;">{{ $entrance['count'] }} check-ins</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ ($entrance['count'] / max($maxEntrance, 1)) * 100 }}%; background: linear-gradient(90deg, #3B82F6 0%, #60A5FA 100%);"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="activity-empty">
                    <div class="activity-empty-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <p class="activity-empty-text">No entrance data available</p>
                </div>
            @endif
        </div>

        {{-- Visits by Status --}}
        <div class="activity-section">
            <div class="activity-header">
                <div>
                    <h2 class="activity-title">Visit Status Breakdown</h2>
                    <p class="text-secondary text-sm mt-0.5">Current visit distribution</p>
                </div>
                <div class="icon-container icon-container--purple" style="width: 40px; height: 40px; border-radius: 10px;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            @if(count($visitsByStatus) > 0)
                @php $maxStatus = max(array_column($visitsByStatus, 'count')); @endphp
                <div class="p-6 space-y-5">
                    @foreach($visitsByStatus as $status)
                        <div class="report-bar-item">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-sm capitalize" style="color: var(--text-primary);">{{ str_replace('_', ' ', $status['status']) }}</span>
                                <span class="text-xs font-bold px-2 py-1 rounded-full" style="background: rgba(168, 85, 247, 0.1); color: #9333ea;">{{ $status['count'] }}</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ ($status['count'] / max($maxStatus, 1)) * 100 }}%; background: linear-gradient(90deg, #8B5CF6 0%, #A78BFA 100%);"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="activity-empty">
                    <div class="activity-empty-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <p class="activity-empty-text">No status data available</p>
                </div>
            @endif
        </div>

        {{-- Top Companies (Admin Only) --}}
        @if(auth()->user()->isAdmin())
        <div class="activity-section">
            <div class="activity-header">
                <div>
                    <h2 class="activity-title">Top Companies</h2>
                    <p class="text-secondary text-sm mt-0.5">Most active organizations</p>
                </div>
                <div class="icon-container icon-container--green" style="width: 40px; height: 40px; border-radius: 10px;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>
            @if(count($visitsByCompany) > 0)
                @php $maxCompany = max(array_column($visitsByCompany, 'count')); @endphp
                <div class="p-6 space-y-5">
                    @foreach($visitsByCompany as $company)
                        <div class="report-bar-item">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-sm" style="color: var(--text-primary);">{{ $company['name'] }}</span>
                                <span class="text-xs font-bold px-2 py-1 rounded-full" style="background: rgba(34, 197, 94, 0.1); color: #16a34a;">{{ $company['count'] }} visits</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-bar-fill" style="width: {{ ($company['count'] / max($maxCompany, 1)) * 100 }}%; background: linear-gradient(90deg, #22C55E 0%, #4ADE80 100%);"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="activity-empty">
                    <div class="activity-empty-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <p class="activity-empty-text">No company data available</p>
                </div>
            @endif
        </div>
        @endif
    </div>
</div>