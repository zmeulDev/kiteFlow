<div class="space-y-4 lg:space-y-6" wire:key="meeting-room-list">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Meeting Rooms</h1>
            <p class="mt-1 text-sm text-gray-500">Manage meeting rooms and their availability</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-full hover:bg-brand-700 transition-colors shadow-sm">
            <i class="fa-solid fa-plus"></i>
            <span>Add Room</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl p-4">
        <div class="relative">
            <i class="absolute left-3 top-1/2 -translate-y-1/2 fa-solid fa-magnifying-glass text-gray-400"></i>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Search meeting rooms..."
                   class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20 focus:border-[#FF4B4B]/30">
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
        @forelse($meetingRooms as $room)
        <div class="bg-white rounded-2xl border border-gray-100 p-4 hover:shadow-sm transition-shadow">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-xl bg-brand-100 flex items-center justify-center text-brand-600 flex-shrink-0">
                    <i class="fa-solid fa-door-open text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $room->name }}</p>
                            <p class="text-xs text-gray-500">{{ $room->location ?? 'No location' }}</p>
                        </div>
                        @if($room->is_active)
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
                    <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                        <span><i class="fa-solid fa-users mr-1"></i>{{ $room->capacity ?? 0 }} people</span>
                        @if(!empty($room->amenities))
                        <span><i class="fa-solid fa-wifi mr-1"></i>{{ count($room->amenities) }} amenities</span>
                        @endif
                    </div>
                    @if(!empty($room->amenities))
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach(array_slice($room->amenities, 0, 3) as $amenity)
                        <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">{{ $amenity }}</span>
                        @endforeach
                        @if(count($room->amenities) > 3)
                        <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">+{{ count($room->amenities) - 3 }} more</span>
                        @endif
                    </div>
                    @endif
                    <button wire:click="openEditModal({{ $room->id }})"
                            class="mt-3 w-full px-4 py-2 text-xs font-semibold text-white bg-brand-600 rounded-xl hover:bg-brand-700 transition-colors text-center">
                        <i class="fa-solid fa-pen mr-1"></i> Edit Room
                    </button>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-door-open text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-semibold text-gray-900">No meeting rooms found</p>
            <p class="text-sm text-gray-500 mt-1">Add your first meeting room</p>
        </div>
        @endforelse

        @if($meetingRooms->hasPages())
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500">{{ $meetingRooms->firstItem() }}-{{ $meetingRooms->lastItem() }} of {{ $meetingRooms->total() }}</p>
                <div class="flex items-center gap-2">
                    @if($meetingRooms->onFirstPage())
                    <span class="p-2 text-gray-300"><i class="fa-solid fa-chevron-left text-xs"></i></span>
                    @else
                    <button wire:click="previousPage()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    @endif
                    @if($meetingRooms->hasMorePages())
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Room</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Capacity</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Amenities</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($meetingRooms as $room)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-brand-100 flex items-center justify-center text-brand-600">
                                    <i class="fa-solid fa-door-open"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $room->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $room->code ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $room->location ?? '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 text-sm text-gray-600">
                                <i class="fa-solid fa-users text-gray-400"></i>
                                {{ $room->capacity ?? 0 }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if(!empty($room->amenities))
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($room->amenities, 0, 3) as $amenity)
                                <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">{{ $amenity }}</span>
                                @endforeach
                                @if(count($room->amenities) > 3)
                                <span class="px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">+{{ count($room->amenities) - 3 }}</span>
                                @endif
                            </div>
                            @else
                            <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($room->is_active)
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
                            <div class="flex items-center justify-end">
                                <button wire:click="openEditModal({{ $room->id }})"
                                        class="px-4 py-2 text-xs font-semibold text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
                                    <i class="fa-solid fa-pen mr-1"></i> Edit
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
                                <p class="text-sm font-semibold text-gray-900">No meeting rooms found</p>
                                <p class="text-sm text-gray-500 mt-1">Add your first meeting room</p>
                                <button wire:click="openCreateModal" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-brand-600 bg-brand-50 rounded-xl hover:bg-brand-100 transition-colors">
                                    <i class="fa-solid fa-plus"></i>
                                    Add Room
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($meetingRooms->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">Showing {{ $meetingRooms->firstItem() }} to {{ $meetingRooms->lastItem() }} of {{ $meetingRooms->total() }}</p>
            {{ $meetingRooms->links() }}
        </div>
        @endif
    </div>
    
    <!-- Add/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-data="{ showDeleteConfirm: false }">
        <div class="fixed inset-0 bg-black/50" wire:click.self="$set('showModal', false)"></div>

        <div class="relative w-full max-w-lg bg-white rounded-xl shadow-xl my-8">
            <form wire:submit="save">
                <div class="p-4 lg:p-6 space-y-5">
                    <!-- Header -->
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">{{ $selectedRoom ? 'Edit Meeting Room' : 'Add Meeting Room' }}</h2>
                        <button type="button" wire:click="$set('showModal', false)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    <!-- Room Name & Code -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                            <input type="text" wire:model="name"
                                   class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                   required>
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Code</label>
                            <input type="text" wire:model="code"
                                   class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Building & Access Point -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Building</label>
                            <select wire:model.live="building_id"
                                    class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                                <option value="">Select Building</option>
                                @foreach($buildings as $building)
                                <option value="{{ $building->id }}">{{ $building->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Access Point</label>
                            <select wire:model.live="access_point_id"
                                    class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                    @if(empty($accessPoints)) disabled @endif>
                                <option value="">Select Access Point</option>
                                @foreach($accessPoints as $ap)
                                <option value="{{ $ap->id }}">{{ $ap->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" wire:model="location"
                               class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                               placeholder="e.g., Floor 2, Building A">
                    </div>

                    <!-- Capacity & Status -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                            <input type="number" wire:model="capacity"
                                   class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select wire:model="is_active"
                                    class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Amenities -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amenities</label>
                        <div class="flex flex-wrap gap-2 mb-2">
                            @forelse($amenities as $index => $amenity)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-100 text-brand-700 text-sm rounded-full">
                                {{ $amenity }}
                                <button type="button" wire:click="removeAmenity({{ $index }})" class="hover:text-brand-900">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </span>
                            @empty
                            <span class="text-xs text-gray-400">No amenities added yet</span>
                            @endforelse
                        </div>
                        <div class="flex gap-2">
                            <input type="text"
                                   wire:model="amenityInput"
                                   wire:keydown.enter.prevent="addAmenity"
                                   class="flex-1 px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                                   placeholder="Type amenity and press Enter">
                            <button type="button"
                                    wire:click="addAmenity"
                                    class="px-4 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
                                <i class="fa-solid fa-plus"></i>
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Quick add: WiFi, Projector, TV, Whiteboard, Video Conf, AC, Phone</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea wire:model="description" rows="3"
                                  class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none"></textarea>
                    </div>

                    @error('delete')
                    <div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-xs text-red-700">{{ $message }}</p>
                    </div>
                    @enderror
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-between px-4 lg:px-6 py-4 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                    <div>
                        @if($selectedRoom)
                            @if(!$showDeleteConfirm)
                                <button type="button" wire:click="confirmDelete"
                                        class="px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <i class="fa-solid fa-trash mr-1.5"></i>Delete
                                </button>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-red-700">Delete this room?</span>
                                    <button type="button" wire:click="delete"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                                        Yes, delete
                                    </button>
                                    <button type="button" wire:click="$set('showDeleteConfirm', false)"
                                            class="px-3 py-1.5 text-xs font-medium text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                        Cancel
                                    </button>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" wire:click="$set('showModal', false)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-6 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
                            <i class="fa-solid fa-check mr-1.5"></i>{{ $selectedRoom ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>