<!-- resources/views/livewire/dashboard/live-activity-feed.blade.php -->
<div wire:poll.15s class="p-8 bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden h-full transition-all">
    <header class="mb-10 flex items-center justify-between">
        <div>
            <h3 class="font-black text-slate-900 dark:text-white tracking-tight text-xl uppercase italic">Live <span class="text-indigo-600 dark:text-indigo-400">Hub</span></h3>
            <p class="text-slate-500 dark:text-slate-500 text-[10px] font-black uppercase tracking-widest mt-1">Real-time visitor flow</p>
        </div>
        <div class="relative">
            <div class="h-3 w-3 bg-emerald-500 rounded-full animate-pulse shadow-[0_0_15px_rgba(16,185,129,0.5)]"></div>
            <div class="absolute inset-0 h-3 w-3 bg-emerald-500 rounded-full animate-ping opacity-25"></div>
        </div>
    </header>

    <div class="space-y-10 relative">
        <div class="absolute left-[19px] top-2 bottom-4 w-0.5 bg-slate-100 dark:bg-slate-800"></div>
        
        @forelse($activities as $visit)
            <div 
                wire:key="activity-{{ $visit->id }}" 
                wire:transition
                class="relative pl-12 group animate-slide-in"
            >
                <div class="absolute left-0 top-0 h-10 w-10 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl z-10 flex items-center justify-center transition-all group-hover:scale-110 group-hover:border-indigo-500 dark:group-hover:border-indigo-400 shadow-sm">
                    <span class="text-lg">{{ $visit->checked_out_at ? '👋' : ($visit->checked_in_at ? '✅' : '⏳') }}</span>
                    @if($visit->checked_in_at && !$visit->checked_out_at)
                        <div class="absolute -top-1 -right-1 h-3 w-3 bg-emerald-500 rounded-full border-2 border-white dark:border-slate-800"></div>
                    @endif
                </div>
                
                <div class="flex flex-col">
                    <div class="text-[9px] font-black uppercase text-indigo-600 dark:text-indigo-400 tracking-[0.2em] mb-1 group-hover:translate-x-1 transition-transform duration-300">
                        {{ $visit->tenant->name }}
                    </div>
                    <div class="text-sm font-black text-slate-900 dark:text-slate-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300 leading-tight">
                        {{ $visit->visitor->full_name }}
                    </div>
                    <div class="flex items-center space-x-2 mt-1">
                        @if($visit->checked_out_at)
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Checked out</span>
                        @elseif($visit->checked_in_at)
                            <span class="text-[10px] text-emerald-500 font-black uppercase tracking-tighter">Arrived</span>
                        @else
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">Scheduled</span>
                        @endif
                        <span class="h-1 w-1 bg-slate-300 dark:bg-slate-600 rounded-full"></span>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $visit->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-20 px-4">
                <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center mb-4 ring-1 ring-slate-100 dark:ring-slate-700">
                    <span class="text-3xl opacity-30 animate-pulse">📡</span>
                </div>
                <p class="text-slate-400 dark:text-slate-500 font-bold text-[10px] uppercase tracking-widest text-center">Awaiting activity...</p>
            </div>
        @endforelse
    </div>
</div>
