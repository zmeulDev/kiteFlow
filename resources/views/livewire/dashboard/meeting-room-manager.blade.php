<!-- projects/visiflow/resources/views/livewire/dashboard/meeting-room-manager.blade.php -->
<div class="space-y-12 animate-fade-in-up">
    <div class="p-8 sm:p-12 bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm transition-all">
        <header class="mb-10">
            <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight italic uppercase">Deploy <span class="text-indigo-600">Meeting Room</span></h3>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest mt-1">Configure workspace amenities and capacity</p>
        </header>

        <form wire:submit.prevent="createRoom" class="space-y-10">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-[0.2em] ml-1">Room Name</label>
                    <input wire:model="name" type="text" placeholder="e.g. Conference Room A" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-black uppercase tracking-tight focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-[0.2em] ml-1">Building/Zone</label>
                    <div class="relative">
                        <select wire:model="location_id" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-black uppercase tracking-tight focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm appearance-none cursor-pointer">
                            <option value="">Main Area</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-[0.2em] ml-1">Capacity</label>
                    <div class="relative">
                        <input wire:model="capacity" type="number" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-black focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                        <span class="absolute right-6 top-1/2 -translate-y-1/2 text-[10px] font-black uppercase text-slate-400">Seats</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-[0.2em] ml-1">Amenities & Tech</label>
                <div class="flex flex-wrap gap-2 mb-6">
                    @forelse($amenities as $index => $amenity)
                        <span wire:key="amenity-{{ $index }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-[10px] font-black uppercase tracking-widest border border-indigo-100 dark:border-indigo-800">
                            {{ $amenity }}
                            <button type="button" wire:click="removeAmenity({{ $index }})" class="ml-3 text-indigo-400 hover:text-indigo-600 transition-colors">✕</button>
                        </span>
                    @empty
                        <span class="text-[10px] text-slate-400 italic font-black uppercase tracking-widest">No amenities added yet</span>
                    @endforelse
                </div>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="relative flex-1 w-full">
                        <input wire:model="new_amenity" type="text" placeholder="e.g. 4K TV, Whiteboard" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                    </div>
                    <button type="button" wire:click="addAmenity" class="w-full sm:w-auto h-14 px-8 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95 border border-slate-200 dark:border-slate-700 shadow-sm">Add Item</button>
                </div>
            </div>

            <button type="submit" class="group relative w-full h-16 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-[0.98] border border-indigo-500">
                <span class="relative z-10 flex items-center justify-center">
                    <span class="mr-3 text-xl group-hover:scale-125 transition-transform duration-300">🚪</span>
                    Initialize Meeting Space
                </span>
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($rooms as $room)
            <div wire:key="room-{{ $room->id }}" class="p-8 bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:border-indigo-500 transition-all group overflow-hidden relative">
                <div class="absolute -right-4 -top-4 text-6xl opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 group-hover:-rotate-12 transition-all">🚪</div>
                
                <div>
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h4 class="font-black text-2xl text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors leading-none italic uppercase">{{ $room->name }}</h4>
                            @if($room->location)
                                <p class="text-[9px] font-black uppercase text-indigo-600/60 tracking-[0.2em] mt-2 italic">{{ $room->location->name }}</p>
                            @endif
                        </div>
                        <button 
                            @click="$dispatch('confirm', { 
                                title: 'Decommission {{ $room->name }}?', 
                                message: 'This will cancel all future bookings for this room.', 
                                confirmText: 'Remove Room',
                                variant: 'danger',
                                onConfirm: () => $wire.deleteRoom({{ $room->id }})
                            })"
                            class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-all border border-transparent hover:border-rose-200"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>

                    <div class="flex items-center space-x-3 mb-8">
                        <div class="px-3 py-1.5 bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm">
                            <span class="text-[10px] font-black uppercase text-slate-500 dark:text-slate-400 tracking-widest">{{ $room->capacity }} Seats</span>
                        </div>
                        <div class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl border border-emerald-100 dark:border-emerald-800">
                            <span class="text-[10px] font-black uppercase text-emerald-600 dark:text-emerald-400 tracking-widest">Active</span>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @foreach($room->amenities ?? [] as $amenity)
                            <span wire:key="room-{{ $room->id }}-amenity-{{ $loop->index }}" class="text-[9px] bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-500 px-3 py-1 rounded-lg font-black uppercase tracking-widest border border-slate-100 dark:border-slate-700 shadow-sm">{{ $amenity }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>