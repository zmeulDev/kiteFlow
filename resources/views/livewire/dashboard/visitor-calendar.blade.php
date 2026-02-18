<!-- resources/views/livewire/dashboard/visitor-calendar.blade.php -->
<div class="space-y-8 animate-fade-in-up">
    <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-8 sm:p-12 transition-all">
        <header class="flex flex-col md:flex-row items-center justify-between gap-8 mb-12">
            <div>
                <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight uppercase italic">Visitor <span class="text-indigo-600">Schedule</span></h3>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest mt-1">Monitor upcoming guest arrivals</p>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center space-x-2 bg-slate-100 dark:bg-slate-800 p-1.5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-inner">
                    <button wire:click="previousMonth" class="h-10 w-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-900 shadow-sm hover:text-indigo-600 transition-all active:scale-95 text-slate-400 font-black text-xl">‹</button>
                    <span class="text-sm font-black text-slate-700 dark:text-white min-w-[140px] text-center uppercase tracking-widest">{{ $monthName }} {{ $currentYear }}</span>
                    <button wire:click="nextMonth" class="h-10 w-10 flex items-center justify-center rounded-xl bg-white dark:bg-slate-900 shadow-sm hover:text-indigo-600 transition-all active:scale-95 text-slate-400 font-black text-xl">›</button>
                </div>
                
                <button wire:click="openAddModal()" class="h-14 px-8 bg-slate-900 dark:bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-widest shadow-xl hover:bg-slate-800 dark:hover:bg-indigo-700 transition-all active:scale-95 border border-slate-700 dark:border-indigo-500 text-[10px]">
                    + Add Visit
                </button>
            </div>
        </header>

        <div class="grid grid-cols-7 gap-px bg-slate-200 dark:bg-slate-700 rounded-[28px] overflow-hidden border border-slate-700 shadow-inner">
            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
                <div class="bg-slate-50 dark:bg-slate-900/50 py-5 text-center text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-[0.2em]">{{ $dayName }}</div>
            @endforeach

            @for($i = 0; $i < $firstDayOfMonth; $i++)
                <div class="bg-white dark:bg-slate-950 min-h-[140px] opacity-20"></div>
            @endfor

            @for($day = 1; $day <= $daysInMonth; $day++)
                <div 
                    wire:key="day-{{ $day }}"
                    wire:click="openAddModal({{ $day }})"
                    class="bg-white dark:bg-slate-950 min-h-[140px] p-5 group cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-900 transition-all duration-300 relative border border-transparent hover:border-indigo-500/20"
                >
                    <span class="text-sm font-black transition-all duration-300 group-hover:scale-125 group-hover:text-indigo-600 inline-flex items-center justify-center h-8 w-8 rounded-full {{ $day == now()->day && $currentMonth == now()->month && $currentYear == now()->year ? 'text-white bg-indigo-600 shadow-lg shadow-indigo-500/30' : 'text-slate-400 dark:text-slate-500 group-hover:text-indigo-600' }}">
                        {{ $day }}
                    </span>
                    
                    <div class="mt-4 space-y-2 relative z-10">
                        @foreach($visits->get($day, []) as $visit)
                            <button 
                                wire:key="visit-{{ $visit->id }}"
                                wire:click.stop="showVisit({{ $visit->id }})"
                                class="w-full text-left p-2.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl hover:bg-white dark:hover:bg-slate-800 hover:border-indigo-500 dark:hover:border-indigo-400 transition-all duration-300 shadow-sm group/btn"
                            >
                                <div class="text-[9px] font-black text-slate-900 dark:text-white truncate group-hover/btn:text-indigo-600 transition-colors uppercase tracking-tight">{{ $visit->visitor->full_name }}</div>
                                <div class="text-[8px] text-slate-400 dark:text-slate-500 font-bold uppercase mt-0.5">{{ $visit->scheduled_at?->format('H:i') }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Details Modal (Using Solid Styles) -->
    @if($showDetailsModal && $selectedVisit)
        <div class="fixed inset-0 z-[110] flex items-center justify-center p-4 bg-slate-950/90" role="dialog" aria-modal="true">
            <div class="bg-white dark:bg-slate-900 rounded-[40px] w-full max-w-lg shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col" style="max-height: 90vh;" @click.away="$wire.set('showDetailsModal', false)">
                <header class="flex justify-between items-start p-10 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="px-3 py-1 bg-indigo-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest mb-3 inline-block shadow-lg shadow-indigo-500/20">Security Profile</span>
                        <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-none">{{ $selectedVisit->visitor->full_name }}</h3>
                        <p class="text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest text-[10px] mt-2">{{ $selectedVisit->visitor->email }}</p>
                    </div>
                    <button wire:click="$set('showDetailsModal', false)" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-400 hover:text-rose-500 transition-all">✕</button>
                </header>

                <div class="flex-1 overflow-y-auto p-10 pt-8 space-y-8">
                    <div class="grid grid-cols-2 gap-10">
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-600 tracking-widest block mb-2">Scheduled Time</label>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $selectedVisit->scheduled_at?->format('F d, Y') }}</div>
                            <div class="text-2xl font-black text-indigo-600 mt-1 uppercase italic">{{ $selectedVisit->scheduled_at?->format('H:i') }}</div>
                        </div>
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-600 tracking-widest block mb-2">Deployed Area</label>
                            <div class="text-sm font-bold text-slate-900 dark:text-white">{{ $selectedVisit->booking?->room?->name ?? 'Open Lobby' }}</div>
                            @if($selectedVisit->booking?->room?->location)
                                <div class="text-[9px] text-slate-400 font-black uppercase tracking-widest mt-1">{{ $selectedVisit->booking->room->location->name }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="p-8 bg-slate-50 dark:bg-slate-950 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-inner">
                        <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-600 tracking-widest block mb-3">Meeting Purpose</label>
                        <p class="text-slate-700 dark:text-slate-300 text-sm leading-relaxed font-medium italic">"{{ $selectedVisit->purpose }}"</p>
                    </div>
                </div>

                <div class="p-10 border-t border-slate-100 dark:border-slate-800 flex flex-col gap-6">
                    <!-- Reschedule Section -->
                    <div class="p-6 bg-slate-50 dark:bg-slate-950/50 rounded-2xl border border-slate-200 dark:border-slate-800">
                        <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest block mb-3">Reschedule Visit</label>
                        <div class="flex gap-3">
                            <input type="datetime-local" wire:model="scheduled_at" class="flex-1 h-12 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-white text-xs font-bold focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm">
                            <button wire:click="updateVisit" class="h-12 px-6 bg-indigo-600 text-white rounded-xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-95">Update</button>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 pt-2">
                        <button wire:click="deleteVisit" wire:confirm="Are you sure you want to delete this visit?" class="h-14 px-6 rounded-2xl bg-rose-50 dark:bg-rose-900/10 text-rose-600 dark:text-rose-400 font-black uppercase tracking-widest text-[10px] hover:bg-rose-100 dark:hover:bg-rose-900/30 transition-all border border-rose-100 dark:border-rose-900/50 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            Delete
                        </button>
                        
                        <div class="flex-1 flex gap-3 justify-end">
                            <button wire:click="$set('showDetailsModal', false)" class="h-14 px-6 rounded-2xl bg-slate-100 dark:bg-slate-800 font-black uppercase tracking-widest text-[10px] text-slate-500 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all border border-slate-200 dark:border-slate-700">Close</button>
                            @if($selectedVisit->check_in_token)
                                <a href="{{ route('check-in.fast-pass', ['token' => $selectedVisit->check_in_token]) }}" target="_blank" class="h-14 px-8 rounded-2xl bg-slate-900 dark:bg-indigo-600 text-white font-black uppercase tracking-widest text-[10px] flex items-center justify-center shadow-xl shadow-indigo-500/10 hover:bg-slate-800 dark:hover:bg-indigo-700 transition-all border border-slate-700 dark:border-indigo-500">Launch Pass 🚀</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Add Visit Modal -->
    <div 
        x-data="{ show: @entangle('showAddModal') }"
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-950/90"
        role="dialog"
        aria-modal="true"
        x-cloak
    >
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            class="bg-white dark:bg-slate-900 rounded-[32px] w-full max-w-xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col"
            style="max-height: 90vh;"
            @click.away="show = false"
        >
            <header class="flex justify-between items-center p-8 pb-4 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase italic">New <span class="text-indigo-600">Visit</span></h3>
                    <p class="text-slate-500 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest mt-1">Schedule a guest arrival</p>
                </div>
                <button @click="show = false" class="h-10 w-10 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-500 hover:text-rose-500 transition-all">✕</button>
            </header>

            <div class="flex-1 overflow-y-auto p-8 pt-6">
                <form wire:submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">First Name</label>
                            <input wire:model="first_name" type="text" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner" placeholder="John" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Last Name</label>
                            <input wire:model="last_name" type="text" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner" placeholder="Doe" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Email Address</label>
                        <input wire:model="email" type="email" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner" placeholder="john@example.com" required>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Purpose</label>
                        <input wire:model="purpose" type="text" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner" placeholder="Meeting..." required>
                    </div>

                    @if(auth()->user()->tenant?->is_hub)
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Visiting Company</label>
                            <div class="relative">
                                <select wire:model="target_tenant_id" class="w-full h-14 pl-6 pr-10 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-black uppercase tracking-tight focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner appearance-none">
                                    <option value="">Main Office (Hub)</option>
                                    @foreach($subtenants as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Expected At</label>
                            <input wire:model.live="scheduled_at" type="datetime-local" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner" required>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Visitors Count</label>
                            <input wire:model.live="visitor_count" type="number" min="1" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-bold focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-widest ml-1">Assign Room</label>
                        <div class="relative">
                            <select wire:model="meeting_room_id" class="w-full h-14 pl-6 pr-10 rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white font-black uppercase tracking-tight focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-inner appearance-none">
                                <option value="">No room assigned</option>
                                @foreach($availableRooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} @if($room->location) ({{ $room->location->name }}) @endif</option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full h-16 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-[0.98] border border-indigo-500" wire:loading.attr="disabled">
                        <span wire:loading.remove>Schedule Visit 📅</span>
                        <span wire:loading>Scheduling... ⏳</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
