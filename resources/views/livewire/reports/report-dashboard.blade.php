<div class="space-y-4 lg:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Reports</h1>
            <p class="mt-1 text-sm text-gray-500">Analytics and insights</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-3 lg:p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <select wire:model.live="dateRange" 
                    class="flex-1 sm:flex-none px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="week">Last 7 Days</option>
                <option value="month">Last 30 Days</option>
                <option value="quarter">Last 90 Days</option>
                <option value="year">Last Year</option>
            </select>
            <select wire:model.live="reportType" 
                    class="flex-1 sm:flex-none px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="visitors">Visitor Stats</option>
                <option value="meetings">Meeting Stats</option>
            </select>
        </div>
    </div>

    @if($reportType === 'visitors')
    <!-- Visitor Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50">
                    <i class="fa-solid fa-users text-blue-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Total Visits</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $visitorStats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-50">
                    <i class="fa-solid fa-user-check text-green-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Checked In</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $visitorStats['checked_in'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-50">
                    <i class="fa-solid fa-clock text-purple-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Avg Duration</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ round($visitorStats['avg_duration'] ?? 0) }}<span class="text-sm font-normal text-gray-500">min</span></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <p class="text-xs lg:text-sm text-gray-500 mb-2">By Method</p>
            <div class="space-y-1">
                @foreach(($visitorStats['by_method'] ?? []) as $method => $count)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">{{ $method }}</span>
                    <span class="font-medium text-gray-900">{{ $count }}</span>
                </div>
                @endforeach
                @if(empty($visitorStats['by_method']))
                <p class="text-sm text-gray-400">No data</p>
                @endif
            </div>
        </div>
    </div>

    @elseif($reportType === 'meetings')
    <!-- Meeting Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50">
                    <i class="fa-solid fa-calendar text-blue-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Total Meetings</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $meetingStats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-50">
                    <i class="fa-solid fa-check text-green-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Completed</p>
            <p class="text-2xl lg:text-3xl font-bold text-green-600">{{ $meetingStats['completed'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-50">
                    <i class="fa-solid fa-xmark text-red-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Cancelled</p>
            <p class="text-2xl lg:text-3xl font-bold text-red-600">{{ $meetingStats['cancelled'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-50">
                    <i class="fa-solid fa-clock text-purple-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Avg Duration</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ round($meetingStats['avg_duration'] ?? 0) }}<span class="text-sm font-normal text-gray-500">min</span></p>
        </div>
    </div>
    @endif

    @if(empty($visitorStats) && empty($meetingStats))
    <div class="bg-white rounded-xl border border-gray-200 p-8 lg:p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-chart-line text-2xl text-gray-400"></i>
        </div>
        <p class="text-sm font-medium text-gray-900">No data available</p>
        <p class="text-sm text-gray-500 mt-1">There's no data for the selected period</p>
    </div>
    @endif
</div>