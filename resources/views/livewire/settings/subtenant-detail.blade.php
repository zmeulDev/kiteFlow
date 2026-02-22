<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.subtenants') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl lg:text-2xl font-bold text-gray-900">{{ $subtenant->name }}</h1>
                    @switch($subtenant->status)
                        @case('active')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Active</span>
                            @break
                        @case('suspended')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Suspended</span>
                            @break
                        @case('inactive')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
                            @break
                    @endswitch
                </div>
                <p class="text-sm text-gray-500">{{ $subtenant->email }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openEditModal" class="px-4 py-2 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                <i class="fa-solid fa-pen mr-2"></i>Edit
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                    <p class="text-xs text-gray-500">Users</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fa-solid fa-user-group"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_visitors'] }}</p>
                    <p class="text-xs text-gray-500">Visitors</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fa-solid fa-calendar"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_meetings'] }}</p>
                    <p class="text-xs text-gray-500">Meetings</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_buildings'] }}</p>
                    <p class="text-xs text-gray-500">Buildings</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex gap-6 -mb-px overflow-x-auto">
            <button wire:click="switchTab('overview')"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $tab === 'overview' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Overview
            </button>
            <button wire:click="switchTab('users')"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $tab === 'users' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Users
            </button>
            <button wire:click="switchTab('contract')"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $tab === 'contract' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Contract
            </button>
            <button wire:click="switchTab('facilities')"
                    class="pb-3 text-sm font-medium border-b-2 transition-colors whitespace-nowrap {{ $tab === 'facilities' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Facilities
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    @switch($tab)
        @case('overview')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Basic Info -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Name</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $subtenant->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $subtenant->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Phone</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $subtenant->phone ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Status</dt>
                            <dd>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                                    {{ $subtenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($subtenant->status) }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Contact Person -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Person</h3>
                    @if($subtenant->settings['contact_person']['name'] ?? null)
                        <dl class="space-y-3">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Name</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $subtenant->settings['contact_person']['name'] }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Email</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $subtenant->settings['contact_person']['email'] ?? '-' }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-500">Phone</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $subtenant->settings['contact_person']['phone'] ?? '-' }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-500">No contact person assigned</p>
                    @endif
                </div>

                <!-- Address -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Address</h3>
                    @if($subtenant->address)
                        <address class="not-italic text-sm text-gray-600">
                            {{ $subtenant->address['line1'] }}<br>
                            @if($subtenant->address['line2']){{ $subtenant->address['line2'] }}<br>@endif
                            {{ $subtenant->address['city'] }}, {{ $subtenant->address['state'] }} {{ $subtenant->address['postal_code'] }}<br>
                            {{ $subtenant->address['country'] }}
                        </address>
                    @else
                        <p class="text-sm text-gray-500">No address provided</p>
                    @endif
                </div>

                <!-- Space Allocation -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Space Allocation</h3>
                    @if($subtenant->settings['space_allocation']['description'] ?? null)
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm text-gray-500 mb-1">Description</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $subtenant->settings['space_allocation']['description'] }}</dd>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm text-gray-500 mb-1">Floor</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $subtenant->settings['space_allocation']['floor'] ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500 mb-1">Zone</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $subtenant->settings['space_allocation']['zone'] ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500 mb-1">Allocated Seats</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $subtenant->settings['space_allocation']['seats'] ?? '-' }}</dd>
                                </div>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-500">No space allocation configured</p>
                    @endif
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-xl border border-gray-100 p-6 lg:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Summary</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_visits'] }}</p>
                            <p class="text-sm text-gray-500">Total Visits</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['checked_in_today'] }}</p>
                            <p class="text-sm text-gray-500">Checked In Today</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_meetings'] }}</p>
                            <p class="text-sm text-gray-500">Total Meetings</p>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                            <p class="text-sm text-gray-500">Active Users</p>
                        </div>
                    </div>
                </div>
            </div>
            @break

        @case('users')
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Users ({{ $users->count() }})</h3>
                </div>
                @if($users->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">User</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Roles</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Joined</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($users as $user)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#FF6B6B] to-[#FF4B4B] text-white flex items-center justify-center text-xs font-semibold">
                                                    {{ strtoupper($user->name[0]) }}
                                                </div>
                                                <div>
                                                    <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($user->roles as $role)
                                                    <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">{{ $role->name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($user->is_active)
                                                <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Active</span>
                                            @else
                                                <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">
                                            {{ $user->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center">
                        <p class="text-gray-500">No users found</p>
                    </div>
                @endif
            </div>
            @break

        @case('contract')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Contract Details -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Details</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">Subscription Plan</dt>
                            <dd class="text-lg font-semibold text-gray-900 capitalize">{{ $subtenant->settings['contract']['subscription_plan'] ?? 'Basic' }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500 mb-1">Monthly Price</dt>
                                <dd class="text-2xl font-bold text-gray-900">${{ number_format($subtenant->settings['contract']['monthly_price'] ?? 0, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 mb-1">Yearly Price</dt>
                                <dd class="text-2xl font-bold text-gray-900">${{ number_format($subtenant->settings['contract']['yearly_price'] ?? 0, 2) }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">Billing Cycle</dt>
                            <dd class="text-sm font-medium text-gray-900 capitalize">{{ $subtenant->settings['contract']['billing_cycle'] ?? 'Monthly' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">Payment Status</dt>
                            <dd>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ ($subtenant->settings['contract']['payment_status'] ?? 'paid') === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($subtenant->settings['contract']['payment_status'] ?? 'Paid') }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Contract Period -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Period</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">Start Date</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $subtenant->settings['contract']['contract_start_date'] ? \Carbon\Carbon::parse($subtenant->settings['contract']['contract_start_date'])->format('F d, Y') : 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">End Date</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $subtenant->settings['contract']['contract_end_date'] ? \Carbon\Carbon::parse($subtenant->settings['contract']['contract_end_date'])->format('F d, Y') : 'Not set' }}</dd>
                        </div>
                        @if($subtenant->settings['contract']['contract_start_date'] && $subtenant->settings['contract']['contract_end_date'])
                            @php
                                $daysRemaining = now()->diffInDays(\Carbon\Carbon::parse($subtenant->settings['contract']['contract_end_date']), false);
                            @endphp
                            <div>
                                <dt class="text-sm text-gray-500 mb-1">Time Remaining</dt>
                                <dd class="text-lg font-semibold {{ $daysRemaining > 30 ? 'text-green-600' : ($daysRemaining > 0 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $daysRemaining > 0 ? $daysRemaining . ' days' : 'Expired' }}
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Notes -->
                @if($subtenant->settings['contract']['notes'] ?? null)
                    <div class="bg-white rounded-xl border border-gray-100 p-6 lg:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                        <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $subtenant->settings['contract']['notes'] }}</p>
                    </div>
                @endif
            </div>
            @break

        @case('facilities')
            <div class="space-y-6">
                <!-- Buildings -->
                @if($buildings->count() > 0)
                    <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                        <div class="p-4 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Assigned Buildings ({{ $buildings->count() }})</h3>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($buildings as $building)
                                <div class="p-4 hover:bg-gray-50">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center text-green-600 flex-shrink-0">
                                            <i class="fa-solid fa-building text-xl"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <h4 class="font-semibold text-gray-900">{{ $building->name }}</h4>
                                                <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded">{{ $building->code }}</span>
                                            </div>
                                            <p class="text-sm text-gray-500 mb-2">{{ $building->description ?? 'No description' }}</p>
                                            <div class="flex flex-wrap gap-3 text-sm text-gray-600">
                                                <span><i class="fa-solid fa-layer-group mr-1"></i>{{ $building->floors }} Floors</span>
                                                <span><i class="fa-solid fa-door-open mr-1"></i>{{ $building->zones->count() }} Zones</span>
                                                <span><i class="fa-solid fa-chair mr-1"></i>{{ $building->meetingRooms->count() }} Rooms</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-xl border border-gray-100 p-8 text-center">
                        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-building text-2xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Facilities Assigned</h3>
                        <p class="text-sm text-gray-500">This sub-tenant doesn't have any buildings or spaces assigned yet.</p>
                    </div>
                @endif

                <!-- Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Meetings -->
                    @if($recentMeetings->count() > 0)
                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                            <div class="p-4 border-b border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Meetings</h3>
                            </div>
                            <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                                @foreach($recentMeetings as $meeting)
                                    <div class="p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                                                <i class="fa-solid fa-calendar"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $meeting->subject ?? 'Meeting' }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $meeting->start_at?->format('M d, Y g:i A') }} · {{ $meeting->meetingRoom?->name ?? 'No room' }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Recent Visitors -->
                    @if($recentVisitors->count() > 0)
                        <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                            <div class="p-4 border-b border-gray-100">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Visitors</h3>
                            </div>
                            <div class="divide-y divide-gray-100 max-h-64 overflow-y-auto">
                                @foreach($recentVisitors as $visitor)
                                    <div class="p-4">
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 flex-shrink-0">
                                                <i class="fa-solid fa-user"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="font-medium text-gray-900">{{ $visitor->name }}</p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $visitor->company ?? 'No company' }} · {{ $visitor->created_at?->format('M d, Y') }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @break
    @endswitch

    <!-- Edit Modal -->
    @if($showEditModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-data>
        <div class="fixed inset-0 bg-black/50" wire:click.self="$set('showEditModal', false)"></div>

        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl my-8 max-h-[90vh] overflow-y-auto">
            <form wire:submit="save">
                <div class="flex items-center justify-between p-5 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <h2 class="text-lg font-semibold text-gray-900">Edit Sub-tenant</h2>
                    <button type="button" wire:click="$set('showEditModal', false)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-6">
                    <!-- Basic Info -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Basic Information</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" wire:model="name" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" wire:model="email" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" wire:model="phone" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select wire:model="status" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Person -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Contact Person</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" wire:model="contact_person_name" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" wire:model="contact_person_email" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" wire:model="contact_person_phone" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Address -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Address</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                                <input type="text" wire:model="address_line1" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                                <input type="text" wire:model="address_line2" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                                <input type="text" wire:model="city" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                                <input type="text" wire:model="state" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                                <input type="text" wire:model="postal_code" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                                <input type="text" wire:model="country" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Space Allocation -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Space Allocation</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <input type="text" wire:model="allocated_space" placeholder="e.g., East Wing, Office 101-105" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Floor</label>
                                <input type="text" wire:model="allocated_floor" placeholder="e.g., 3rd Floor" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Zone</label>
                                <input type="text" wire:model="allocated_zone" placeholder="e.g., Zone A" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Allocated Seats</label>
                                <input type="number" wire:model="allocated_seats" placeholder="e.g., 10" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <!-- Contract -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">Contract & Billing</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Subscription Plan</label>
                                <select wire:model="subscription_plan" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                    <option value="basic">Basic</option>
                                    <option value="professional">Professional</option>
                                    <option value="enterprise">Enterprise</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Billing Cycle</label>
                                <select wire:model="billing_cycle" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                    <option value="monthly">Monthly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Price</label>
                                <input type="number" step="0.01" wire:model="monthly_price" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Yearly Price</label>
                                <input type="number" step="0.01" wire:model="yearly_price" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contract Start</label>
                                <input type="date" wire:model="contract_start_date" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contract End</label>
                                <input type="date" wire:model="contract_end_date" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                                <select wire:model="payment_status" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                    <option value="paid">Paid</option>
                                    <option value="pending">Pending</option>
                                    <option value="overdue">Overdue</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea wire:model="notes" rows="3" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <button type="button" wire:click="$set('showEditModal', false)" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#FF4B4B] rounded-lg hover:bg-[#E63E3E]">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>