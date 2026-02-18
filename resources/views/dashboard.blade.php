<!-- resources/views/dashboard.blade.php -->
<x-layouts.app header="Dashboard Overview">
    <div class="max-w-7xl mx-auto space-y-12 sm:space-y-16">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 animate-fade-in-up">
            <div>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tighter leading-none italic uppercase">
                    Welcome <span class="text-indigo-600 dark:text-indigo-400">Back,</span>
                </h1>
                <p class="mt-2 text-slate-500 dark:text-slate-400 font-black uppercase tracking-[0.3em] text-[10px]">{{ auth()->user()->name }} • {{ now()->format('l, F jS') }}</p>
            </div>
            
            <button 
                type="button"
                onclick="if(window.Livewire) { window.Livewire.dispatch('openInviteModal'); } else { console.error('Livewire not found'); }"
                class="group relative inline-flex items-center justify-center h-14 px-10 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-95 text-[10px] border border-indigo-500"
            >
                <span class="relative z-10 flex items-center">
                    <span class="mr-3 text-lg group-hover:rotate-90 transition-transform duration-300">✦</span>
                    Invite Guest
                </span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 sm:gap-10">
            <div class="lg:col-span-2 space-y-8 sm:space-y-10">
                <!-- Lazy Loaded Stats -->
                @livewire('dashboard.stats-overview')

                <!-- Room Bookings Schedule -->
                <div class="bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden p-6 sm:p-8 transition-all">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 dark:text-white tracking-tight italic uppercase">Today's <span class="text-indigo-600">Schedule</span></h3>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest mt-1">Daily meeting room utilization</p>
                        </div>
                        <span class="text-2xl opacity-50 grayscale">🗓️</span>
                    </div>
                    
                    <div class="space-y-4">
                        @forelse(\App\Models\Booking::whereDate('starts_at', today())->with(['room.location', 'visit.visitor'])->get() as $booking)
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-5 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-800 transition-all hover:bg-white dark:hover:bg-slate-700 group gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="h-12 w-12 bg-white dark:bg-slate-900 rounded-xl shadow-sm flex items-center justify-center text-xl group-hover:scale-110 transition-transform duration-300 border border-slate-100 dark:border-slate-700">🚪</div>
                                    <div>
                                        <div class="font-bold text-slate-900 dark:text-white">
                                            {{ $booking->room->name }} 
                                            @if($booking->room->location)
                                                <span class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest ml-1">{{ $booking->room->location->name }}</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-2 mt-0.5">
                                            <span class="h-1.5 w-1.5 bg-indigo-500 rounded-full"></span>
                                            <div class="text-xs text-slate-500 dark:text-slate-400 font-bold tracking-tight">{{ $booking->starts_at->format('H:i') }} - {{ $booking->ends_at->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex flex-row sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-1 text-right">
                                    <div class="font-black text-indigo-600 dark:text-indigo-400 tracking-tight text-sm uppercase">{{ $booking->visit?->visitor->full_name ?? 'Reserved' }}</div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500 uppercase font-black tracking-widest bg-slate-100 dark:bg-slate-900 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-800">{{ $booking->room->capacity }} Seats</div>
                                </div>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-12 px-4 border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-[24px]">
                                <span class="text-4xl mb-4 grayscale opacity-30">😴</span>
                                <p class="text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest text-[10px]">No rooms booked for today</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Lazy Loaded Log -->
                @livewire('dashboard.visitor-log')
            </div>

            <div class="space-y-8 sm:space-y-10">
                <!-- Live Activity Feed -->
                @livewire('dashboard.live-activity-feed')
            </div>
        </div>
    </div>
    
    @livewire('dashboard.pre-register-guest')
</x-layouts.app>
