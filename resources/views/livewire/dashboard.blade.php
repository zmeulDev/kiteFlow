<div class="space-y-6" wire:key="dashboard">
    <!-- Quick Actions -->
    <div class="bg-white rounded-2xl p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="flex flex-wrap gap-3">
            <button wire:click="$dispatch('openCheckInModal')" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
                <i class="fa-solid fa-plus"></i>
                Check In Visitor
            </button>
            <a href="{{ route('visitors.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-user-plus"></i>
                Add Visitor
            </a>
            <a href="{{ route('meetings.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-calendar-plus"></i>
                Schedule Meeting
            </a>
            <a href="/meeting-rooms" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-door-open"></i>
                Meeting Rooms
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-5">
        <!-- Total Visitors -->
        <div class="bg-white rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-100">
                    <i class="fa-solid fa-users text-blue-600 text-lg"></i>
                </div>
                <span class="text-xs font-semibold {{ $stats['trend']['visitors'] > 0 ? 'text-green-600 bg-green-50' : ($stats['trend']['visitors'] < 0 ? 'text-red-600 bg-red-50' : 'text-gray-600 bg-gray-50') }} px-2 py-1 rounded-full">
                    {{ $stats['trend']['visitors'] > 0 ? '+' : '' }}{{ $stats['trend']['visitors'] ?? 0 }}%
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_visitors'] ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Visitors</p>
        </div>

        <!-- Currently In -->
        <div class="bg-white rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-green-100">
                    <i class="fa-solid fa-user-check text-green-600 text-lg"></i>
                </div>
                <span class="flex items-center gap-1 text-xs font-semibold text-green-600">
                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                    Live
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['currently_in'] ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">Currently In</p>
        </div>

        <!-- Meetings Today -->
        <div class="bg-white rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-purple-100">
                    <i class="fa-solid fa-calendar-days text-purple-600 text-lg"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['meetings_today'] ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">Meetings Today</p>
        </div>

        <!-- Expected -->
        <div class="bg-white rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-orange-100">
                    <i class="fa-solid fa-clock text-orange-600 text-lg"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['expected'] ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">Expected Today</p>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <!-- Recent Visitors Table -->
        <div class="lg:col-span-2 bg-white rounded-2xl">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                <a href="{{ route('visitors.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                    View all
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Visitor</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Purpose</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentVisits as $visit)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-semibold text-sm">
                                        {{ strtoupper(substr($visit->visitor?->first_name ?? '?', 0, 1) . substr($visit->visitor?->last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $visit->visitor?->full_name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-400">{{ $visit->visitor?->company ?? 'No Company' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $visit->check_in_at->format('g:i A') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($visit->check_out_at)
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                    Checked Out
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    Active
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-600">{{ $visit->purpose ?? 'General Visit' }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-users text-gray-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">No recent activity</p>
                                    <p class="text-sm text-gray-400 mt-1">Check-ins will appear here</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upcoming Meetings -->
        <div class="bg-white rounded-2xl">
            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Upcoming Meetings</h2>
                <a href="{{ route('meetings.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                    View all
                </a>
            </div>

            <div class="p-4 space-y-3">
                @forelse($upcomingMeetings as $meeting)
                <a href="{{ route('meetings.show', $meeting) }}" class="block p-4 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer">
                    <div class="flex items-start gap-3">
                        <div class="flex flex-col items-center justify-center w-12 h-12 rounded-xl bg-blue-100 text-center shrink-0">
                            <span class="text-xs font-semibold text-blue-600">{{ $meeting->start_at->format('M') }}</span>
                            <span class="text-lg font-bold text-blue-700">{{ $meeting->start_at->format('d') }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $meeting->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $meeting->start_at->format('g:i A') }} - {{ $meeting->end_at->format('g:i A') }}</p>
                            @if($meeting->meetingRoom)
                            <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-location-dot"></i>
                                {{ $meeting->meetingRoom->name }}
                            </p>
                            @endif
                        </div>
                    </div>
                </a>
                @empty
                <div class="py-10 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-calendar text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-500">No upcoming meetings</p>
                    <a href="{{ route('meetings.create') }}" class="inline-flex items-center gap-1 mt-2 text-sm font-medium text-brand-600 hover:text-brand-700">
                        <i class="fa-solid fa-plus text-xs"></i>
                        Schedule one
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>