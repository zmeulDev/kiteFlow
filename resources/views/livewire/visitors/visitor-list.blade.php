<div class="space-y-5" wire:key="visitor-list">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Visitors</h1>
            <p class="mt-0.5 text-sm text-gray-500">Manage visitor records and check-ins</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-[#FF4B4B] rounded-lg hover:bg-[#E63E3E] transition-colors">
            <i class="fa-solid fa-plus"></i>
            <span>Add Visitor</span>
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 lg:gap-4">
        <div class="bg-white rounded-2xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600">
                    <i class="fa-solid fa-users text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_visitors'] }}</p>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Visitors</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-green-400 to-green-600">
                    <i class="fa-solid fa-door-open text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['checked_in_today'] }}</p>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Checked In Today</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600">
                    <i class="fa-solid fa-clipboard-list text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_visits'] }}</p>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Visits</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-4 border border-gray-100">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-red-400 to-red-600">
                    <i class="fa-solid fa-ban text-white text-lg"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['blacklisted'] }}</p>
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Blacklisted</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="absolute left-3 top-1/2 -translate-y-1/2 fa-solid fa-magnifying-glass text-gray-400"></i>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search visitors..."
                       class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30">
            </div>
            <div class="flex items-center gap-2">
                <select wire:model.live="statusFilter" class="px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20">
                    <option value="all">All Status</option>
                    <option value="active">Active</option>
                    <option value="checked-in">Checked In</option>
                    <option value="blacklisted">Blacklisted</option>
                </select>
                <button class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100">
                    <i class="fa-solid fa-download"></i>
                    <span class="hidden sm:inline">Export</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
        @forelse($visitors as $visitor)
        <div class="bg-white rounded-2xl p-4">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#FF7070] to-[#FF4B4B] flex items-center justify-center text-white font-semibold flex-shrink-0">
                    @if($visitor->photo)
                    <img src="{{ asset('storage/' . $visitor->photo) }}" class="w-12 h-12 rounded-full object-cover">
                    @else
                    <span class="text-sm">{{ strtoupper(substr($visitor->first_name, 0, 1) . substr($visitor->last_name, 0, 1)) }}</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $visitor->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $visitor->company ?? 'No Company' }}</p>
                        </div>
                        @if($visitor->is_blacklisted)
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                            Blacklisted
                        </span>
                        @elseif($visitor->isCheckedIn())
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            In
                        </span>
                        @else
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                            Active
                        </span>
                        @endif
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        @if(!$visitor->isCheckedIn() && !$visitor->is_blacklisted)
                        <button wire:click="openCheckInModal({{ $visitor->id }})"
                                class="flex-1 px-3 py-1.5 text-xs font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 text-center">
                            Check In
                        </button>
                        @endif
                        <button wire:click="openEditModal({{ $visitor->id }})"
                                class="flex-1 px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-8 text-center">
            <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-users text-gray-400"></i>
            </div>
            <p class="text-sm font-medium text-gray-900">No visitors found</p>
            <p class="text-sm text-gray-500 mt-1">Try adjusting your search</p>
        </div>
        @endforelse
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-white rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Visitor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Company</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Last Visit</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($visitors as $visitor)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#FF7070] to-[#FF4B4B] flex items-center justify-center text-white font-semibold text-sm">
                                    @if($visitor->photo)
                                    <img src="{{ asset('storage/' . $visitor->photo) }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                    <span>{{ strtoupper(substr($visitor->first_name, 0, 1) . substr($visitor->last_name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $visitor->full_name }}</p>
                                    <p class="text-xs text-gray-400">#{{ $visitor->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $visitor->company ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($visitor->is_blacklisted)
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                Blacklisted
                            </span>
                            @elseif($visitor->isCheckedIn())
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                Checked In
                            </span>
                            @else
                            <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                Active
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">{{ $visitor->visits->first()?->check_in_at?->diffForHumans() ?? 'Never' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                @if(!$visitor->isCheckedIn() && !$visitor->is_blacklisted)
                                <button wire:click="openCheckInModal({{ $visitor->id }})"
                                        class="px-3 py-1.5 text-xs font-semibold text-green-700 bg-green-100 rounded-lg hover:bg-green-200">
                                    Check In
                                </button>
                                @endif
                                <button wire:click="openEditModal({{ $visitor->id }})"
                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mb-3">
                                    <i class="fa-solid fa-users text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-900">No visitors found</p>
                                <p class="text-sm text-gray-500 mt-1">Try adjusting your search</p>
                                <button wire:click="openCreateModal" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-[#FF4B4B] bg-red-50 rounded-lg hover:bg-red-100">
                                    <i class="fa-solid fa-plus"></i>
                                    Add Visitor
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($visitors->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">Showing {{ $visitors->firstItem() }} to {{ $visitors->lastItem() }} of {{ $visitors->total() }}</p>
            {{ $visitors->links() }}
        </div>
        @endif
    </div>
    
    <!-- Add/Edit Visitor Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-data>
        <div class="fixed inset-0 bg-black/50" wire:click.self="$set('showModal', false)"></div>
        
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl my-8">
            <form wire:submit="save">
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $selectedVisitor ? 'Edit Visitor' : 'Add New Visitor' }}</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">First Name *</label>
                            <input type="text" wire:model="first_name" 
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30" 
                                   required>
                            @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Last Name *</label>
                            <input type="text" wire:model="last_name" 
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30" 
                                   required>
                            @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model="email" 
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" wire:model="phone" 
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
                        @if(!empty($companies))
                        <select wire:model="company" 
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30">
                            <option value="">Select a company</option>
                            @foreach($companies as $companyName)
                            <option value="{{ $companyName }}">{{ $companyName }}</option>
                            @endforeach
                            <option value="__other__">Other (enter manually)</option>
                        </select>
                        @else
                        <input type="text" wire:model="company" 
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30"
                               placeholder="Company name">
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ID Number</label>
                        <input type="text" wire:model="id_number"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="flex items-center gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="is_blacklisted" value="active"
                                       class="w-4 h-4 text-gray-600 border-gray-300 focus:ring-gray-500">
                                <span class="text-sm text-gray-700">Active</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="is_blacklisted" value="blacklisted"
                                       class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                                <span class="text-sm text-gray-700">Blacklisted</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <div>
                        @if($selectedVisitor)
                        <button type="button" wire:click="openDeleteModal({{ $selectedVisitor->id }})"
                                class="px-4 py-2.5 text-sm font-semibold text-red-600 bg-red-50 rounded-lg hover:bg-red-100">
                            <i class="fa-solid fa-trash mr-2"></i>Delete
                        </button>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2.5 text-sm font-semibold text-white bg-[#FF4B4B] rounded-lg hover:bg-[#E63E3E]">
                            {{ $selectedVisitor ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
    
    <!-- Check-In Modal -->
    @if($showCheckInModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-data>
        <div class="fixed inset-0 bg-black/50" wire:click.self="$set('showCheckInModal', false)"></div>
        
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl my-8">
            <form wire:submit="checkIn">
                <div class="p-5 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Check In Visitor</h3>
                    <div class="flex items-center gap-3 mt-3">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#FF7070] to-[#FF4B4B] flex items-center justify-center text-white font-semibold">
                            <span class="text-sm">{{ strtoupper(substr($selectedVisitor?->first_name ?? '?', 0, 1) . substr($selectedVisitor?->last_name ?? '', 0, 1)) }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $selectedVisitor?->full_name }}</p>
                            <p class="text-xs text-gray-500">{{ $selectedVisitor?->company ?? 'No Company' }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Purpose of Visit</label>
                        <input type="text" wire:model="purpose" placeholder="e.g., Meeting, Interview"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Host</label>
                        <select wire:model="host_id"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30">
                            <option value="">Select host</option>
                            @foreach(auth()->user()->getCurrentTenant()->users as $host)
                            <option value="{{ $host->id }}">{{ $host->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <button type="button" wire:click="$set('showCheckInModal', false)" class="flex-1 px-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2.5 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700">
                        Check In
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-data>
        <div class="fixed inset-0 bg-black/50" wire:click.self="$set('showDeleteModal', false)"></div>

        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl my-8">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 flex-shrink-0">
                        <i class="fa-solid fa-trash text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Delete Visitor</h3>
                        <p class="text-sm text-gray-500">This action cannot be undone.</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        Are you sure you want to delete <strong>{{ $selectedVisitor?->full_name }}</strong>? This will permanently remove the visitor and all associated data including visit history.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="confirmDelete"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors">
                    Delete Visitor
                </button>
            </div>
        </div>
    </div>
    @endif
</div>