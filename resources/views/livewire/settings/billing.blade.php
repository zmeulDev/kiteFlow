<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Billing & Subscription</h1>
            <p class="text-sm text-gray-500">Manage tenant subscriptions and payments</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex gap-6 -mb-px">
            <button wire:click="$set('tab', 'subscriptions')" 
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $tab === 'subscriptions' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Subscriptions
            </button>
            <button wire:click="$set('tab', 'plans')" 
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $tab === 'plans' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Plans
            </button>
            <button wire:click="$set('tab', 'invoices')" 
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $tab === 'invoices' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Invoices
            </button>
        </nav>
    </div>

    @switch($tab)
        @case('subscriptions')
            <!-- Subscriptions List -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Tenant Subscriptions</h3>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Plan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Billing</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Payment</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($tenants as $tenant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#FF6B6B] to-[#FF4B4B] text-white flex items-center justify-center font-semibold">
                                        {{ strtoupper(substr($tenant->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $tenant->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $tenant->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 capitalize">
                                    {{ $tenant->subscription_plan ?? 'Starter' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 capitalize">
                                {{ $tenant->billing_cycle ?? 'Monthly' }}
                            </td>
                            <td class="px-6 py-4">
                                @switch($tenant->status)
                                    @case('active')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Active</span>
                                        @break
                                    @case('trial')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Trial</span>
                                        @break
                                    @case('suspended')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Suspended</span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">{{ $tenant->status }}</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $tenant->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ ucfirst($tenant->payment_status ?? 'Paid') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="selectTenant({{ $tenant->id }})" 
                                        class="px-3 py-1.5 text-xs font-medium text-[#FF4B4B] bg-[#FF4B4B]/10 rounded-lg hover:bg-[#FF4B4B]/20">
                                    Manage
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($tenants->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $tenants->links() }}
                </div>
                @endif
            </div>

            <!-- Edit Subscription Modal -->
            @if($selectedTenant)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data>
                <div class="fixed inset-0 bg-black/50" wire:click="$set('selectedTenant', null)"></div>
                <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl my-8 max-h-[90vh] overflow-y-auto">
                    <form wire:submit="updateSubscription">
                        <div class="flex items-center justify-between p-5 border-b border-gray-100 sticky top-0 bg-white z-10">
                            <h2 class="text-lg font-semibold text-gray-900">Manage Subscription - {{ $selectedTenant->name }}</h2>
                            <button type="button" wire:click="$set('selectedTenant', null)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        
                        <div class="p-5 space-y-6">
                            <!-- Plan Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Select Plan</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @foreach($plans as $plan)
                                    <label class="relative cursor-pointer">
                                        <input type="radio" wire:model="plan" value="{{ $plan['id'] }}" class="sr-only peer">
                                        <div class="p-4 rounded-xl border-2 transition-all peer-checked:border-[#FF4B4B] peer-checked:bg-[#FF4B4B]/5 hover:bg-gray-50">
                                            <p class="text-sm font-semibold text-gray-900">{{ $plan['name'] }}</p>
                                            <p class="text-lg font-bold text-[#FF4B4B]">${{ $plan['monthly'] }}/mo</p>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Billing Cycle -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Billing Cycle</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="billing_cycle" value="monthly" class="w-4 h-4 text-[#FF4B4B]">
                                        <span class="text-sm text-gray-700">Monthly</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model="billing_cycle" value="yearly" class="w-4 h-4 text-[#FF4B4B]">
                                        <span class="text-sm text-gray-700">Yearly (Save ~20%)</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Contract Dates -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contract Start</label>
                                    <input type="date" wire:model="contract_start" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contract End</label>
                                    <input type="date" wire:model="contract_end" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                </div>
                            </div>

                            <!-- Status & Payment -->
                            <div class="grid grid-cols-2 gap-4">
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
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                                    <select wire:model="payment_status" class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg">
                                        <option value="paid">Paid</option>
                                        <option value="pending">Pending</option>
                                        <option value="overdue">Overdue</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                            <button type="button" wire:click="$set('selectedTenant', null)" class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-[#FF4B4B] rounded-lg hover:bg-[#E63E3E]">
                                Update Subscription
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            @break

        @case('plans')
            <!-- Plans Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($plans as $plan)
                <div class="bg-white rounded-xl border border-gray-100 p-6 {{ $plan['id'] === 'professional' ? 'ring-2 ring-[#FF4B4B]' : '' }}">
                    @if($plan['id'] === 'professional')
                    <span class="inline-block px-3 py-1 text-xs font-semibold text-white bg-[#FF4B4B] rounded-full mb-4">Popular</span>
                    @endif
                    <h3 class="text-lg font-semibold text-gray-900">{{ $plan['name'] }}</h3>
                    <div class="mt-2 mb-4">
                        <span class="text-3xl font-bold text-gray-900">${{ $plan['monthly'] }}</span>
                        <span class="text-sm text-gray-500">/month</span>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">or ${{ $plan['yearly'] }}/year</p>
                    <ul class="space-y-2 mb-6">
                        @foreach($plan['features'] as $feature)
                        <li class="flex items-center gap-2 text-sm text-gray-600">
                            <i class="fa-solid fa-check text-green-500"></i>
                            {{ $feature }}
                        </li>
                        @endforeach
                    </ul>
                    <button class="w-full px-4 py-2.5 text-sm font-semibold {{ $plan['id'] === 'professional' ? 'text-white bg-[#FF4B4B] hover:bg-[#E63E3E]' : 'text-[#FF4B4B] bg-[#FF4B4B]/10 hover:bg-[#FF4B4B]/20' }} rounded-lg transition-colors">
                        Select Plan
                    </button>
                </div>
                @endforeach
            </div>
            @break

        @case('invoices')
            <!-- Invoices -->
            <div class="bg-white rounded-xl border border-gray-100 p-8 text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-file-invoice text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">No Invoices Yet</h3>
                <p class="text-sm text-gray-500 mt-1">Invoices will appear here when tenants are billed</p>
            </div>
            @break
    @endswitch
</div>