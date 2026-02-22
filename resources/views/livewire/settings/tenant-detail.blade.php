<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.tenants') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-xl lg:text-2xl font-bold text-gray-900">{{ $tenant->name }}</h1>
                    @switch($tenant->status)
                        @case('active')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Active</span>
                            @break
                        @case('suspended')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Suspended</span>
                            @break
                        @case('inactive')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
                            @break
                        @case('trial')
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Trial</span>
                            @break
                    @endswitch
                </div>
                <p class="text-sm text-gray-500">{{ $tenant->email }} • {{ $tenant->domain ?: 'No domain' }}</p>
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
                    <i class="fa-solid fa-building"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_buildings'] }}</p>
                    <p class="text-xs text-gray-500">Buildings</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['subtenants_count'] }}</p>
                    <p class="text-xs text-gray-500">Sub-tenants</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex gap-6 -mb-px">
            <button wire:click="switchTab('overview')" 
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $tab === 'overview' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Overview
            </button>
            <button wire:click="switchTab('users')" 
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $tab === 'users' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Users
            </button>
            <button wire:click="switchTab('subtenants')" 
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $tab === 'subtenants' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Sub-tenants
            </button>
            <button wire:click="switchTab('contract')" 
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $tab === 'contract' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Contract
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
                            <dd class="text-sm font-medium text-gray-900">{{ $tenant->name }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $tenant->email }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Phone</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $tenant->phone ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Slug</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $tenant->slug }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Domain</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $tenant->domain ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Timezone</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $tenant->timezone }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Address -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Address</h3>
                    @if($tenant->address)
                        <address class="not-italic text-sm text-gray-600">
                            {{ $tenant->address['line1'] }}<br>
                            @if($tenant->address['line2']){{ $tenant->address['line2'] }}<br>@endif
                            {{ $tenant->address['city'] }}, {{ $tenant->address['state'] }} {{ $tenant->address['postal_code'] }}<br>
                            {{ $tenant->address['country'] }}
                        </address>
                    @else
                        <p class="text-sm text-gray-500">No address provided</p>
                    @endif
                </div>

                <!-- Subscription -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Subscription</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Plan</dt>
                            <dd class="text-sm font-medium text-gray-900 capitalize">{{ $tenant->subscription_plan ?? 'Basic' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Billing Cycle</dt>
                            <dd class="text-sm font-medium text-gray-900 capitalize">{{ $tenant->billing_cycle ?? 'Monthly' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Monthly Price</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($tenant->monthly_price ?? 0, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Yearly Price</dt>
                            <dd class="text-sm font-medium text-gray-900">${{ number_format($tenant->yearly_price ?? 0, 2) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Payment Status</dt>
                            <dd class="text-sm font-medium capitalize {{ $tenant->payment_status === 'paid' ? 'text-green-600' : 'text-amber-600' }}">
                                {{ $tenant->payment_status ?? 'Paid' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Contract -->
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Period</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Start Date</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $tenant->contract_start_date?->format('M d, Y') ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">End Date</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $tenant->contract_end_date?->format('M d, Y') ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Trial Ends</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $tenant->trial_ends_at?->format('M d, Y') ?: '-' }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-500">Subscription Ends</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ $tenant->subscription_ends_at?->format('M d, Y') ?: '-' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
            @break

        @case('users')
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Users ({{ $users->count() }})</h3>
                </div>
                @if($users->count() > 0)
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
                @else
                    <div class="p-8 text-center">
                        <p class="text-gray-500">No users found</p>
                    </div>
                @endif
            </div>
            @break

        @case('subtenants')
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Sub-tenants ({{ $subtenants->count() }})</h3>
                    <button wire:click="openSubtenantModal()" class="px-4 py-2 text-sm font-semibold text-white bg-[#FF4B4B] rounded-lg hover:bg-[#E63E3E]">
                        <i class="fa-solid fa-plus mr-2"></i>Add Sub-tenant
                    </button>
                </div>
                @if($subtenants->count() > 0)
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Name</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Users</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Visitors</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Meetings</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($subtenants as $subtenant)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $subtenant->name }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $subtenant->email }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $subtenant->users_count }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $subtenant->visitors_count }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $subtenant->meetings_count }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
                                            {{ $subtenant->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $subtenant->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button wire:click="openSubtenantModal({{ $subtenant->id }})" class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <button wire:click="deleteSubtenant({{ $subtenant->id }})" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-8 text-center">
                        <p class="text-gray-500">No sub-tenants found</p>
                    </div>
                @endif
            </div>
            @break

        @case('contract')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Subscription Details</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">Plan</dt>
                            <dd class="text-lg font-semibold text-gray-900 capitalize">{{ $tenant->subscription_plan ?? 'Basic' }}</dd>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-sm text-gray-500 mb-1">Monthly Price</dt>
                                <dd class="text-2xl font-bold text-gray-900">${{ number_format($tenant->monthly_price ?? 0, 2) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500 mb-1">Yearly Price</dt>
                                <dd class="text-2xl font-bold text-gray-900">${{ number_format($tenant->yearly_price ?? 0, 2) }}</dd>
                            </div>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">Billing Cycle</dt>
                            <dd class="text-sm font-medium text-gray-900 capitalize">{{ $tenant->billing_cycle ?? 'Monthly' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">Payment Status</dt>
                            <dd>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $tenant->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($tenant->payment_status ?? 'Paid') }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contract Period</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">Start Date</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $tenant->contract_start_date?->format('F d, Y') ?: 'Not set' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500 mb-1">End Date</dt>
                            <dd class="text-lg font-semibold text-gray-900">{{ $tenant->contract_end_date?->format('F d, Y') ?: 'Not set' }}</dd>
                        </div>
                        @if($tenant->contract_start_date && $tenant->contract_end_date)
                            @php
                                $daysRemaining = now()->diffInDays($tenant->contract_end_date, false);
                            @endphp
                            <div>
                                <dt class="text-sm text-gray-500 mb-1">Time Remaining</dt>
                                <dd class="text-lg font-semibold {{ $daysRemaining > 30 ? 'text-green-600' : ($daysRemaining > 0 ? 'text-amber-600' : 'text-red-600') }}">
                                    {{ $daysRemaining > 0 ? $daysRemaining . ' days' : 'Expired' }}
                                </dd>
                            </div>
                        @endif
                        <div class="pt-4 border-t border-gray-100">
                            <dt class="text-sm text-gray-500 mb-1">Trial Period</dt>
                            <dd class="text-sm font-medium text-gray-900">
                                @if($tenant->trial_ends_at)
                                    @if($tenant->trial_ends_at->isPast())
                                        Trial ended
                                    @else
                                        {{ $tenant->trial_ends_at->diffInDays() }} days remaining
                                    @endif
                                @else
                                    No trial
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>

                @if($tenant->notes)
                    <div class="bg-white rounded-xl border border-gray-100 p-6 lg:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Notes</h3>
                        <p class="text-sm text-gray-600 whitespace-pre-wrap">{{ $tenant->notes }}</p>
                    </div>
                @endif
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
                    <h2 class="text-lg font-semibold text-gray-900">Edit Tenant</h2>
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
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" wire:model="email" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <input type="text" wire:model="phone" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                                <input type="text" wire:model="slug" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select wire:model="status" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                    <option value="active">Active</option>
                                    <option value="trial">Trial</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Domain</label>
                                <input type="text" wire:model="domain" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Timezone</label>
                                <select wire:model="timezone" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                    <option value="UTC">UTC</option>
                                    <option value="America/New_York">Eastern Time</option>
                                    <option value="America/Chicago">Central Time</option>
                                    <option value="America/Denver">Mountain Time</option>
                                    <option value="America/Los_Angeles">Pacific Time</option>
                                    <option value="Europe/London">London</option>
                                    <option value="Europe/Paris">Paris</option>
                                    <option value="Europe/Berlin">Berlin</option>
                                </select>
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

    <!-- Subtenant Modal -->
    @if($showSubtenantModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
        <div class="fixed inset-0 bg-black/50" wire:click.self="$set('showSubtenantModal', false)"></div>
        
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl my-8">
            <form wire:submit="saveSubtenant">
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $selectedSubtenant ? 'Edit' : 'Add' }} Sub-tenant</h2>
                    <button type="button" wire:click="$set('showSubtenantModal', false)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" wire:model="subtenant_name" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model="subtenant_email" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="text" wire:model="subtenant_phone" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Slug</label>
                        <input type="text" wire:model="subtenant_slug" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model="subtenant_status" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <button type="button" wire:click="$set('showSubtenantModal', false)" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#FF4B4B] rounded-lg hover:bg-[#E63E3E]">
                        {{ $selectedSubtenant ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>