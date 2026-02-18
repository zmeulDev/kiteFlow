<div wire:init="loadStats" wire:poll.30s="loadStats" class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in-up">
    @if($readyToLoad)
        <!-- Active Visitors -->
        <div class="p-8 rounded-[32px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:border-emerald-500 group">
            <div class="flex items-center justify-between mb-6">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-950 rounded-2xl transition-colors group-hover:bg-emerald-500 group-hover:text-white">
                    <span class="text-xl">👥</span>
                </div>
                <span class="px-3 py-1 bg-emerald-500 text-white rounded-full text-[9px] font-black uppercase tracking-widest animate-pulse">Live Now</span>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline space-x-1">
                    <span class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $activeCount }}</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest">Visitors</span>
                </div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 font-black uppercase tracking-widest italic">In building presence</p>
            </div>
        </div>

        <!-- Today's Total -->
        <div class="p-8 rounded-[32px] bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm transition-all hover:border-indigo-500 group">
            <div class="flex items-center justify-between mb-6">
                <div class="p-3 bg-indigo-50 dark:bg-indigo-950 rounded-2xl transition-colors group-hover:bg-indigo-500 group-hover:text-white">
                    <span class="text-xl">📈</span>
                </div>
                <div class="flex items-center space-x-1 text-emerald-500 font-black text-[9px] uppercase tracking-widest">
                    <span>↑</span>
                    <span>{{ $percentageIncrease }}%</span>
                </div>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline space-x-1">
                    <span class="text-5xl font-black text-slate-900 dark:text-white tracking-tighter">{{ $todayCount }}</span>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest">Traffic</span>
                </div>
                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 font-black uppercase tracking-widest italic">Daily total volume</p>
            </div>
        </div>

        <!-- Expected Guests -->
        <div class="p-8 rounded-[32px] bg-indigo-600 dark:bg-indigo-900 border border-indigo-500 dark:border-indigo-800 transition-all hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-6">
                <div class="p-3 bg-indigo-500 rounded-2xl text-white">
                    <span class="text-xl">🎯</span>
                </div>
                <span class="text-indigo-200 text-[9px] font-black uppercase tracking-widest">Scheduled</span>
            </div>
            <div class="mt-4">
                <div class="flex items-baseline space-x-1">
                    <span class="text-5xl font-black text-white tracking-tighter">{{ $expectedCount }}</span>
                    <span class="text-[10px] text-indigo-200 font-black uppercase tracking-widest">Guests</span>
                </div>
                <p class="text-[10px] text-indigo-200 mt-2 font-black uppercase tracking-widest italic">Awaiting arrival</p>
            </div>
        </div>
    @endif
</div>