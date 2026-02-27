<x-slot name="title">Reports</x-slot>

<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
        <div class="flex items-center space-x-4">
            <div class="p-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-lg">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="20" x2="18" y2="10"></line>
                    <line x1="12" y1="20" x2="12" y2="4"></line>
                    <line x1="6" y1="20" x2="6" y2="14"></line>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reports</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">View visitation metrics and analytics</p>
            </div>
        </div>
    </div>

    {{-- Business Reports Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Today's Visits --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center text-gray-500 dark:text-gray-400 mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span class="text-sm font-medium">Expected Today</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $todayVisits }}</div>
        </div>

        {{-- Active Visits --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center text-blue-500 dark:text-blue-400 mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-medium">Currently On-Site</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $activeVisits }}</div>
        </div>

        {{-- Total Unique Visitors --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center text-purple-500 dark:text-purple-400 mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <span class="text-sm font-medium">Total Unique Visitors</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $totalVisitors }}</div>
        </div>

        {{-- Monthly Visits --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
            <div class="flex items-center text-green-500 dark:text-green-400 mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span class="text-sm font-medium">Visits This Month</span>
            </div>
            <div class="text-3xl font-bold text-gray-900 dark:text-white">{{ $thisMonthVisits }}</div>
        </div>
    </div>

    {{-- Advanced Statistics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        
        {{-- Top Hosts --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center text-gray-900 dark:text-white mb-6">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold">Top Hosts</h3>
            </div>
            @if(count($visitsByHost) > 0)
                @php $maxHost = max(array_column($visitsByHost, 'count')); @endphp
                <div class="space-y-4">
                    @foreach($visitsByHost as $host)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $host['name'] }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $host['count'] }} visits</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ ($host['count'] / max($maxHost, 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">No data available.</p>
            @endif
        </div>

        {{-- Entrance Popularity --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center text-gray-900 dark:text-white mb-6">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="text-lg font-semibold">Entrance Usage</h3>
            </div>
            @if(count($visitsByEntrance) > 0)
                @php $maxEntrance = max(array_column($visitsByEntrance, 'count')); @endphp
                <div class="space-y-4">
                    @foreach($visitsByEntrance as $entrance)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $entrance['name'] }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $entrance['count'] }} check-ins</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($entrance['count'] / max($maxEntrance, 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">No data available.</p>
            @endif
        </div>

        {{-- Visits by Status --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center text-gray-900 dark:text-white mb-6">
                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-lg font-semibold">Visit Status Breakdown</h3>
            </div>
            @if(count($visitsByStatus) > 0)
                @php $maxStatus = max(array_column($visitsByStatus, 'count')); @endphp
                <div class="space-y-4">
                    @foreach($visitsByStatus as $status)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $status['status']) }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $status['count'] }}</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ ($status['count'] / max($maxStatus, 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">No data available.</p>
            @endif
        </div>

        {{-- Top Companies (God Mode Only) --}}
        @if(auth()->user()->isAdmin())
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center text-gray-900 dark:text-white mb-6">
                <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="text-lg font-semibold">Top Companies</h3>
            </div>
            @if(count($visitsByCompany) > 0)
                @php $maxCompany = max(array_column($visitsByCompany, 'count')); @endphp
                <div class="space-y-4">
                    @foreach($visitsByCompany as $company)
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ $company['name'] }}</span>
                                <span class="text-gray-500 dark:text-gray-400">{{ $company['count'] }} visits</span>
                            </div>
                            <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" style="width: {{ ($company['count'] / max($maxCompany, 1)) * 100 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm text-center py-4">No data available.</p>
            @endif
        </div>
        @endif
    </div>
</div>
