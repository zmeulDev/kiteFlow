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
                <option value="access">Access Control</option>
                <option value="parking">Parking</option>
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
            <p class="text-xs text-gray-400 mt-1">{{ $visitorStats['unique_visitors'] ?? 0 }} unique visitors</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-50">
                    <i class="fa-solid fa-user-check text-green-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Checked In</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $visitorStats['checked_in'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $visitorStats['checked_out'] ?? 0 }} checked out</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-50">
                    <i class="fa-solid fa-clock text-purple-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Avg Duration</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $visitorStats['avg_duration'] ?? 0 }}<span class="text-sm font-normal text-gray-500">min</span></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-50">
                    <i class="fa-solid fa-ban text-red-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Blacklisted</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $visitorStats['blacklisted_count'] ?? 0 }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $visitorStats['cancelled'] ?? 0 }} cancelled</p>
        </div>
    </div>

    <!-- By Method & Daily Trend -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">By Check-in Method</h3>
            <div class="space-y-2">
                @foreach(($visitorStats['by_method'] ?? []) as $method => $count)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 capitalize">{{ $method }}</span>
                    <div class="flex items-center gap-2">
                        <div class="w-32 bg-gray-100 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: {{ ($count / ($visitorStats['total'] ?? 1)) * 100 }}%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-900 w-8 text-right">{{ $count }}</span>
                    </div>
                </div>
                @endforeach
                @if(empty($visitorStats['by_method']))
                <p class="text-sm text-gray-400">No data available</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Daily Trend</h3>
            <div class="flex items-end justify-between h-32 gap-1">
                @foreach(($visitorStats['by_day'] ?? []) as $day)
                <div class="flex-1 flex flex-col items-center gap-1">
                    <div class="w-full bg-blue-500 rounded-t hover:bg-blue-600 transition-colors"
                         style="height: {{ max(($day['count'] / max(collect($visitorStats['by_day'])->pluck('count')->max() ?? 1, 1)) * 100, 5) }}%">
                    </div>
                    <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($day['date'])->format('M j') }}</span>
                    <span class="text-xs font-medium text-gray-900">{{ $day['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Visits -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Recent Visits</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-100">
                        <th class="pb-2 font-medium">Visitor</th>
                        <th class="pb-2 font-medium">Host</th>
                        <th class="pb-2 font-medium">Method</th>
                        <th class="pb-2 font-medium">Check-in</th>
                        <th class="pb-2 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($visitorStats['recent_visits'] ?? []) as $visit)
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="py-3">
                            <div class="font-medium text-gray-900">{{ $visit->visitor?->full_name ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-400">{{ $visit->visitor?->email ?? '' }}</div>
                        </td>
                        <td class="py-3 text-gray-600">{{ $visit->host?->name ?? '-' }}</td>
                        <td class="py-3 text-gray-600 capitalize">{{ $visit->check_in_method ?? '-' }}</td>
                        <td class="py-3 text-gray-600">{{ $visit->check_in_at?->format('M j, H:i') ?? '-' }}</td>
                        <td class="py-3">
                            @if($visit->status === 'checked_in' && $visit->check_out_at === null)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Checked In</span>
                            @elseif($visit->status === 'checked_out')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">Checked Out</span>
                            @elseif($visit->status === 'cancelled')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Cancelled</span>
                            @elseif($visit->status === 'no_show')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">No Show</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">{{ $visit->status }}</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if(empty($visitorStats['recent_visits']))
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">No recent visits</td>
                    </tr>
                    @endif
                </tbody>
            </table>
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
            <p class="text-2xl lg:text-3xl font-bold text-green-600">{{ $meetingStats['by_status']['completed'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-yellow-50">
                    <i class="fa-solid fa-clock text-yellow-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Scheduled</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $meetingStats['by_status']['scheduled'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-50">
                    <i class="fa-solid fa-xmark text-red-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Cancelled</p>
            <p class="text-2xl lg:text-3xl font-bold text-red-600">{{ $meetingStats['by_status']['cancelled'] ?? 0 }}</p>
        </div>
    </div>

    <!-- By Status, Room & Host -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">By Status</h3>
            <div class="space-y-2">
                @foreach(($meetingStats['by_status'] ?? []) as $status => $count)
                @if($count > 0)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 capitalize">{{ $status }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                </div>
                @endif
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Top Rooms</h3>
            <div class="space-y-2">
                @foreach(($meetingStats['by_room'] ?? []) as $room)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ $room['room'] }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $room['count'] }}</span>
                </div>
                @endforeach
                @if(empty($meetingStats['by_room']))
                <p class="text-sm text-gray-400">No data available</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Top Hosts</h3>
            <div class="space-y-2">
                @foreach(($meetingStats['by_host'] ?? []) as $host)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ $host['host'] }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $host['count'] }}</span>
                </div>
                @endforeach
                @if(empty($meetingStats['by_host']))
                <p class="text-sm text-gray-400">No data available</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Upcoming Meetings -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Upcoming Meetings</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-100">
                        <th class="pb-2 font-medium">Title</th>
                        <th class="pb-2 font-medium">Room</th>
                        <th class="pb-2 font-medium">Host</th>
                        <th class="pb-2 font-medium">Date & Time</th>
                        <th class="pb-2 font-medium">Duration</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($meetingStats['upcoming'] ?? []) as $meeting)
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="py-3">
                            <div class="font-medium text-gray-900">{{ $meeting->title }}</div>
                            @if($meeting->description)
                            <div class="text-xs text-gray-400 truncate max-w-xs">{{ $meeting->description }}</div>
                            @endif
                        </td>
                        <td class="py-3 text-gray-600">{{ $meeting->meetingRoom?->name ?? '-' }}</td>
                        <td class="py-3 text-gray-600">{{ $meeting->host?->name ?? '-' }}</td>
                        <td class="py-3 text-gray-600">{{ $meeting->start_at?->format('M j, H:i') ?? '-' }}</td>
                        <td class="py-3 text-gray-600">{{ $meeting->getDurationFormatted() ?? '-' }}</td>
                    </tr>
                    @endforeach
                    @if(empty($meetingStats['upcoming']))
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">No upcoming meetings</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @elseif($reportType === 'access')
    <!-- Access Control Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50">
                    <i class="fa-solid fa-door-open text-blue-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Total Access Attempts</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $accessStats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-50">
                    <i class="fa-solid fa-check-circle text-green-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Granted</p>
            <p class="text-2xl lg:text-3xl font-bold text-green-600">{{ $accessStats['by_result']['granted'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-50">
                    <i class="fa-solid fa-times-circle text-red-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Denied</p>
            <p class="text-2xl lg:text-3xl font-bold text-red-600">{{ $accessStats['by_result']['denied'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-50">
                    <i class="fa-solid fa-arrow-right-arrow-left text-purple-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Entry/Exit</p>
            <p class="text-xl lg:text-2xl font-bold text-gray-900">{{ $accessStats['by_direction']['entry'] ?? 0 }}<span class="text-sm font-normal text-gray-500"> / {{ $accessStats['by_direction']['exit'] ?? 0 }}</span></p>
            <p class="text-xs text-gray-400">entries / exits</p>
        </div>
    </div>

    <!-- By Access Point & Denial Reasons -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">By Access Point</h3>
            <div class="space-y-3">
                @foreach(($accessStats['by_access_point'] ?? []) as $point)
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-900">{{ $point['access_point'] }}</span>
                        <span class="text-sm text-gray-600">{{ $point['count'] }} attempts</span>
                    </div>
                    <div class="flex gap-2">
                        <div class="flex-1 bg-green-100 rounded h-2" style="width: {{ ($point['granted'] / $point['count']) * 100 }}%">
                            <div class="bg-green-500 h-2 rounded-full"></div>
                        </div>
                        <div class="flex-1 bg-red-100 rounded h-2" style="width: {{ ($point['denied'] / $point['count']) * 100 }}%">
                            <div class="bg-red-500 h-2 rounded-full"></div>
                        </div>
                    </div>
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>{{ $point['granted'] }} granted</span>
                        <span>{{ $point['denied'] }} denied</span>
                    </div>
                </div>
                @endforeach
                @if(empty($accessStats['by_access_point']))
                <p class="text-sm text-gray-400">No data available</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Denial Reasons</h3>
            <div class="space-y-2">
                @foreach(($accessStats['denial_reasons'] ?? []) as $reason => $count)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">{{ $reason }}</span>
                    <span class="text-sm font-medium text-red-600">{{ $count }}</span>
                </div>
                @endforeach
                @if(empty($accessStats['denial_reasons']))
                <p class="text-sm text-gray-400">No denials recorded</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Access Logs -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Recent Access Logs</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-100">
                        <th class="pb-2 font-medium">Subject</th>
                        <th class="pb-2 font-medium">Access Point</th>
                        <th class="pb-2 font-medium">Direction</th>
                        <th class="pb-2 font-medium">Result</th>
                        <th class="pb-2 font-medium">Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($accessStats['recent'] ?? []) as $log)
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="py-3">
                            <div class="font-medium text-gray-900">{{ $log->subject?->full_name ?? class_basename($log->subject_type) }}</div>
                            <div class="text-xs text-gray-400">{{ class_basename($log->subject_type) }}</div>
                        </td>
                        <td class="py-3 text-gray-600">{{ $log->accessPoint?->name ?? '-' }}</td>
                        <td class="py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium @if($log->direction === 'entry') bg-blue-100 text-blue-800 @else bg-gray-100 text-gray-800 @endif capitalize">
                                {{ $log->direction }}
                            </span>
                        </td>
                        <td class="py-3">
                            @if($log->result === 'granted')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Granted</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Denied</span>
                            <div class="text-xs text-gray-400 mt-1">{{ $log->denial_reason ?? '' }}</div>
                            @endif
                        </td>
                        <td class="py-3 text-gray-600">{{ $log->accessed_at?->format('M j, H:i:s') ?? '-' }}</td>
                    </tr>
                    @endforeach
                    @if(empty($accessStats['recent']))
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">No recent access logs</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @elseif($reportType === 'parking')
    <!-- Parking Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-50">
                    <i class="fa-solid fa-car text-blue-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Total Entries</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $parkingStats['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-50">
                    <i class="fa-solid fa-square-parking text-green-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Currently Parked</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $parkingStats['currently_parked'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-50">
                    <i class="fa-solid fa-clock text-purple-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Avg Duration</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $parkingStats['avg_duration'] ?? 0 }}<span class="text-sm font-normal text-gray-500">min</span></p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <div class="flex items-center gap-3 mb-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-yellow-50">
                    <i class="fa-solid fa-dollar-sign text-yellow-600"></i>
                </div>
            </div>
            <p class="text-xs lg:text-sm text-gray-500">Total Revenue</p>
            <p class="text-2xl lg:text-3xl font-bold text-gray-900">${{ number_format($parkingStats['total_revenue'] ?? 0, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">${{ number_format($parkingStats['pending_revenue'] ?? 0, 2) }} pending</p>
        </div>
    </div>

    <!-- By Vehicle Type & Recent Records -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">By Vehicle Type</h3>
            <div class="space-y-2">
                @foreach(($parkingStats['by_vehicle_type'] ?? []) as $type => $count)
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600 capitalize">{{ $type }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $count }}</span>
                </div>
                @endforeach
                @if(empty($parkingStats['by_vehicle_type']))
                <p class="text-sm text-gray-400">No data available</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Payment Summary</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <span class="text-sm text-gray-600">Paid Records</span>
                    <span class="text-lg font-bold text-green-600">{{ $parkingStats['paid_records'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                    <span class="text-sm text-gray-600">Pending Payment</span>
                    <span class="text-lg font-bold text-yellow-600">${{ number_format($parkingStats['pending_revenue'] ?? 0, 2) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                    <span class="text-sm text-gray-600">Total Collected</span>
                    <span class="text-lg font-bold text-blue-600">${{ number_format($parkingStats['total_revenue'] ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Parking Records -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
        <h3 class="text-sm font-semibold text-gray-900 mb-4">Recent Parking Records</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="text-gray-500 border-b border-gray-100">
                        <th class="pb-2 font-medium">Vehicle</th>
                        <th class="pb-2 font-medium">License Plate</th>
                        <th class="pb-2 font-medium">Spot</th>
                        <th class="pb-2 font-medium">Entry</th>
                        <th class="pb-2 font-medium">Exit</th>
                        <th class="pb-2 font-medium">Fee</th>
                        <th class="pb-2 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(($parkingStats['recent'] ?? []) as $record)
                    <tr class="border-b border-gray-50 last:border-0">
                        <td class="py-3">
                            <div class="font-medium text-gray-900 capitalize">{{ $record->vehicle_make ?? '' }} {{ $record->vehicle_model ?? '' }}</div>
                            <div class="text-xs text-gray-400 capitalize">{{ $record->vehicle_type }}</div>
                        </td>
                        <td class="py-3 text-gray-600 font-mono">{{ $record->license_plate ?? '-' }}</td>
                        <td class="py-3 text-gray-600">{{ $record->parkingSpot?->name ?? '-' }}</td>
                        <td class="py-3 text-gray-600">{{ $record->entry_at?->format('M j, H:i') ?? '-' }}</td>
                        <td class="py-3 text-gray-600">{{ $record->exit_at?->format('M j, H:i') ?? '-' }}</td>
                        <td class="py-3 text-gray-600">{{ $record->fee ? '$' . number_format($record->fee, 2) : '-' }}</td>
                        <td class="py-3">
                            @if($record->exit_at === null)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Parked</span>
                            @elseif($record->is_paid)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Paid</span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Unpaid</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if(empty($parkingStats['recent']))
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400">No recent parking records</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if(empty($visitorStats) && empty($meetingStats) && empty($accessStats) && empty($parkingStats))
    <div class="bg-white rounded-xl border border-gray-200 p-8 lg:p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-chart-line text-2xl text-gray-400"></i>
        </div>
        <p class="text-sm font-medium text-gray-900">No data available</p>
        <p class="text-sm text-gray-500 mt-1">There's no data for the selected period</p>
    </div>
    @endif
</div>