<div class="space-y-4 lg:space-y-6" wire:key="access-point-list">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Access Points</h1>
            <p class="mt-1 text-sm text-gray-500">Manage entry and exit points</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-full hover:bg-brand-700 transition-colors shadow-sm">
            <i class="fa-solid fa-plus"></i>
            <span>Add Access Point</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-100 p-4">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="absolute left-4 top-1/2 -translate-y-1/2 fa-solid fa-magnifying-glass text-gray-400"></i>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search access points..."
                       class="w-full pl-11 pr-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all">
            </div>
            <div class="flex items-center gap-2">
                <select wire:model.live="buildingFilter" class="px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">All Buildings</option>
                    @foreach($buildings as $building)
                    <option value="{{ $building->id }}">{{ $building->name }}</option>
                    @endforeach
                </select>
                <select wire:model.live="typeFilter" class="px-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <option value="">All Types</option>
                    <option value="entrance">Entrance</option>
                    <option value="exit">Exit</option>
                    <option value="both">Both</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
        @forelse($accessPoints as $point)
        <div class="bg-white rounded-2xl border border-gray-100 p-4 hover:shadow-sm transition-shadow">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-xl {{ $point->type === 'entrance' ? 'bg-emerald-100 text-emerald-600' : ($point->type === 'exit' ? 'bg-red-100 text-red-600' : 'bg-brand-100 text-brand-600') }} flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid {{ $point->type === 'entrance' ? 'fa-arrow-right-to-bracket' : ($point->type === 'exit' ? 'fa-arrow-right-from-bracket' : 'fa-arrows-left-right') }} text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $point->name }}</p>
                            <p class="text-xs text-gray-500">{{ $point->building?->name ?? 'No Building' }}</p>
                        </div>
                        @if($point->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                            Inactive
                        </span>
                        @endif
                    </div>
                    <div class="mt-2 flex items-center gap-3">
                        <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full {{ $point->type === 'entrance' ? 'bg-emerald-50 text-emerald-700' : ($point->type === 'exit' ? 'bg-red-50 text-red-700' : 'bg-brand-50 text-brand-700') }}">
                            {{ ucfirst($point->type) }}
                        </span>
                        @if($point->requires_badge)
                        <span class="text-xs text-gray-400"><i class="fa-solid fa-id-card mr-1"></i>Badge required</span>
                        @endif
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <button wire:click="openEditModal({{ $point->id }})" 
                                class="flex-1 px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors text-center">
                            <i class="fa-solid fa-pen mr-1"></i> Edit
                        </button>
                        <button wire:click="openDeleteModal({{ $point->id }})" 
                                class="px-4 py-2 text-xs font-semibold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-colors">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-door-open text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-semibold text-gray-900">No access points found</p>
            <p class="text-sm text-gray-500 mt-1">Add your first access point</p>
        </div>
        @endforelse

        @if($accessPoints->hasPages())
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500">{{ $accessPoints->firstItem() }}-{{ $accessPoints->lastItem() }} of {{ $accessPoints->total() }}</p>
                <div class="flex items-center gap-2">
                    @if($accessPoints->onFirstPage())
                    <span class="p-2 text-gray-300"><i class="fa-solid fa-chevron-left text-xs"></i></span>
                    @else
                    <button wire:click="previousPage()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    @endif
                    @if($accessPoints->hasMorePages())
                    <button wire:click="nextPage()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                    @else
                    <span class="p-2 text-gray-300"><i class="fa-solid fa-chevron-right text-xs"></i></span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Access Point</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Building</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Badge</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($accessPoints as $point)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $point->type === 'entrance' ? 'bg-emerald-100 text-emerald-600' : ($point->type === 'exit' ? 'bg-red-100 text-red-600' : 'bg-brand-100 text-brand-600') }} flex items-center justify-center">
                                    <i class="fa-solid {{ $point->type === 'entrance' ? 'fa-arrow-right-to-bracket' : ($point->type === 'exit' ? 'fa-arrow-right-from-bracket' : 'fa-arrows-left-right') }}"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $point->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $point->code ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $point->building?->name ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full {{ $point->type === 'entrance' ? 'bg-emerald-50 text-emerald-700' : ($point->type === 'exit' ? 'bg-red-50 text-red-700' : 'bg-brand-50 text-brand-700') }}">
                                {{ ucfirst($point->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($point->requires_badge)
                            <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                <i class="fa-solid fa-id-card text-gray-400"></i>
                                Required
                            </span>
                            @else
                            <span class="text-sm text-gray-400">Not required</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($point->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEditModal({{ $point->id }})" 
                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <button wire:click="openDeleteModal({{ $point->id }})" 
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-door-open text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">No access points found</p>
                                <p class="text-sm text-gray-500 mt-1">Add your first access point</p>
                                <button wire:click="openCreateModal" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-brand-600 bg-brand-50 rounded-xl hover:bg-brand-100 transition-colors">
                                    <i class="fa-solid fa-plus"></i>
                                    Add Access Point
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($accessPoints->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">Showing {{ $accessPoints->firstItem() }} to {{ $accessPoints->lastItem() }} of {{ $accessPoints->total() }}</p>
            {{ $accessPoints->links() }}
        </div>
        @endif
    </div>
    
    <!-- Add/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-data>
        <div class="fixed inset-0 bg-black/50" wire:click.self="$set('showModal', false)"></div>
        
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl my-8">
            <form wire:submit="save">
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $selectedPoint ? 'Edit Access Point' : 'Add Access Point' }}</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Name *</label>
                            <input type="text" wire:model="name" 
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all" 
                                   required>
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Code</label>
                            <input type="text" wire:model="code" 
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Building *</label>
                        <select wire:model="building_id" 
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                                required>
                            <option value="">Select building</option>
                            @foreach($buildings as $building)
                            <option value="{{ $building->id }}">{{ $building->name }}</option>
                            @endforeach
                        </select>
                        @error('building_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Type</label>
                        <select wire:model="type" 
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all">
                            <option value="entrance">Entrance</option>
                            <option value="exit">Exit</option>
                            <option value="both">Both</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                        <textarea wire:model="description" rows="3"
                                  class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"></textarea>
                    </div>

                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="is_active" id="point_is_active" 
                                   class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <label for="point_is_active" class="text-sm text-gray-700">Active</label>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="requires_badge" id="requires_badge" 
                                   class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                            <label for="requires_badge" class="text-sm text-gray-700">Requires Badge</label>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <button type="button" wire:click="$set('showModal', false)" 
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-full hover:bg-brand-700 transition-colors shadow-sm">
                        {{ $selectedPoint ? 'Update' : 'Create' }}
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
            <div class="p-5 text-center">
                <div class="w-14 h-14 rounded-2xl bg-red-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-trash text-xl text-red-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Access Point?</h3>
                <p class="text-sm text-gray-500 mt-2">This action cannot be undone. The access point will be permanently removed.</p>
            </div>
            <div class="flex items-center gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                <button type="button" wire:click="$set('showDeleteModal', false)" 
                        class="flex-1 px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="delete" 
                        class="flex-1 px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors shadow-sm">
                    Delete
                </button>
            </div>
        </div>
    </div>
    @endif
</div>