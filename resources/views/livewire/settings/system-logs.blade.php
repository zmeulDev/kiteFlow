<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Audit Logs</h1>
            <p class="text-sm text-gray-500">Track all system activities for security and compliance</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="clearFilters" class="px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                <i class="fa-solid fa-filter-circle-xmark mr-2"></i>Clear
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl border border-gray-100 p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <i class="absolute left-3 top-1/2 -translate-y-1/2 fa-solid fa-magnifying-glass text-gray-400"></i>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search logs..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20">
            </div>
            <div>
                <select wire:model="filterAction" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20">
                    <option value="">All Actions</option>
                    @foreach($actionTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <input type="date" wire:model="filterDateFrom" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20" placeholder="From Date">
            </div>
            <div>
                <input type="date" wire:model="filterDateTo" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20" placeholder="To Date">
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Timestamp</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Action</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">
                                {{ $log->created_at->format('M d, Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($log->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-[#FF6B6B] to-[#FF4B4B] text-white flex items-center justify-center text-xs font-semibold">
                                            {{ strtoupper($log->user->name[0]) }}
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $log->user->name }}</span>
                                    </div>
                                @else
                                    <span class="text-sm text-gray-400">System</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $actionColors = [
                                        'login' => 'bg-green-100 text-green-700',
                                        'logout' => 'bg-gray-100 text-gray-600',
                                        'create' => 'bg-blue-100 text-blue-700',
                                        'update' => 'bg-amber-100 text-amber-700',
                                        'delete' => 'bg-red-100 text-red-700',
                                        'check_in' => 'bg-purple-100 text-purple-700',
                                        'check_out' => 'bg-indigo-100 text-indigo-700',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $actionColors[$log->action] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($log->action) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-md truncate">
                                {{ $log->description ?? $log->metadata ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-14 h-14 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-clipboard-list text-xl text-gray-400"></i>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">No logs found</p>
                                    <p class="text-xs text-gray-500 mt-1">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>