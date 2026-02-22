<div class="space-y-4 lg:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('meetings.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg lg:-ml-2">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">{{ $isEdit ? 'Edit Meeting' : 'Schedule Meeting' }}</h1>
                <p class="text-sm text-gray-500">{{ $isEdit ? 'Update meeting details' : 'Create a new meeting' }}</p>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-gray-200">
        <form wire:submit="save">
            <div class="p-4 lg:p-6 space-y-6">
                <!-- Company & Host -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company *</label>
                        <select wire:model="selectedCompanyId" 
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                            <option value="{{ $company['id'] }}">{{ $company['name'] }}</option>
                            @endforeach
                        </select>
                        @error('selectedCompanyId') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Host *</label>
                        <select wire:model="host_id" 
                                class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                            <option value="">Select Host</option>
                            @foreach($hosts as $host)
                            <option value="{{ $host->id }}">{{ $host->name }}</option>
                            @endforeach
                        </select>
                        @error('host_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" wire:model="title" 
                           class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                           placeholder="Meeting title">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                
                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" rows="3" 
                              class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent resize-none"
                              placeholder="Add meeting details..."></textarea>
                </div>
                
                <!-- Date & Time -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                        <input type="datetime-local" wire:model.live="start_at" 
                               class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        @error('start_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Time *</label>
                        <input type="datetime-local" wire:model.live="end_at" 
                               class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        @error('end_at') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                
                <!-- Meeting Room -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Meeting Room
                        @if($start_at && $end_at)
                        <span class="text-xs text-gray-400 font-normal">(Showing available rooms)</span>
                        @endif
                    </label>
                    <select wire:model="meeting_room_id" 
                            class="w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        <option value="">Select Room</option>
                        @foreach($meetingRooms as $room)
                        <option value="{{ $room->id }}">
                            {{ $room->name }} (Capacity: {{ $room->capacity }})
                            @if($start_at && $end_at && $room->isAvailable($start_at, $end_at, $meeting?->id))
                            ✓ Available
                            @endif
                        </option>
                        @endforeach
                    </select>
                    @if($start_at && $end_at && $meetingRooms->isEmpty())
                    <p class="mt-1 text-xs text-amber-600">
                        <i class="fa-solid fa-exclamation-triangle mr-1"></i>
                        No rooms available for the selected time slot.
                    </p>
                    @endif
                </div>
                
                <!-- Meeting Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Type</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" wire:click="$set('meeting_type', 'in_person')" 
                                class="px-4 py-2.5 text-sm font-medium rounded-lg border transition-colors {{ $meeting_type === 'in_person' ? 'bg-brand-50 border-brand-200 text-brand-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-users mb-1 block"></i>
                            <span class="text-xs">In Person</span>
                        </button>
                        <button type="button" wire:click="$set('meeting_type', 'virtual')" 
                                class="px-4 py-2.5 text-sm font-medium rounded-lg border transition-colors {{ $meeting_type === 'virtual' ? 'bg-brand-50 border-brand-200 text-brand-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-video mb-1 block"></i>
                            <span class="text-xs">Virtual</span>
                        </button>
                        <button type="button" wire:click="$set('meeting_type', 'hybrid')" 
                                class="px-4 py-2.5 text-sm font-medium rounded-lg border transition-colors {{ $meeting_type === 'hybrid' ? 'bg-brand-50 border-brand-200 text-brand-700' : 'bg-white border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                            <i class="fa-solid fa-laptop mb-1 block"></i>
                            <span class="text-xs">Hybrid</span>
                        </button>
                    </div>
                </div>
                
                <!-- Meeting URL (conditional) -->
                @if(in_array($meeting_type, ['virtual', 'hybrid']))
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Meeting URL</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-link text-gray-400 text-sm"></i>
                        </div>
                        <input type="url" wire:model="meeting_url" 
                               class="w-full pl-10 pr-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                               placeholder="https://zoom.us/j/...">
                    </div>
                    @error('meeting_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                @endif
            </div>
            
            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 p-4 lg:p-6 border-t border-gray-100 bg-gray-50 rounded-b-xl">
                <a href="{{ route('meetings.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-6 py-2 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
                    <i class="fa-solid fa-check mr-1.5"></i>
                    {{ $isEdit ? 'Update' : 'Create' }} Meeting
                </button>
            </div>
        </form>
    </div>
</div>