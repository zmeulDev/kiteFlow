<!-- projects/visiflow/resources/views/livewire/kiosk/fast-pass.blade.php -->
<div class="flex flex-col justify-center space-y-8 text-center w-full max-w-md mx-auto">
    @if(!$visit)
        <header>
            <div class="mx-auto h-16 w-16 text-rose-500 mb-4 text-4xl">⚠️</div>
            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Invalid Token</h3>
            <p class="text-slate-500 dark:text-slate-400 font-medium mt-2">This Fast Pass link appears to be invalid or expired.</p>
        </header>
        <div class="pt-4">
            <a href="{{ route('kiosk', ['tenant' => 'jucu-hub']) }}" class="inline-flex h-14 items-center justify-center rounded-2xl bg-slate-900 dark:bg-slate-700 px-10 text-xs font-black uppercase tracking-widest text-white transition-all hover:bg-slate-800 shadow-xl">
                Return to Kiosk
            </a>
        </div>
    @elseif($isProcessed)
        <header>
            <div class="mx-auto h-16 w-16 text-emerald-500 mb-4 text-4xl">✅</div>
            @if(session('status') === 'already_checked_in')
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Already In!</h3>
                <p class="text-slate-500 dark:text-slate-400 font-medium mt-2">You are already checked in for this meeting. Please wait for your host.</p>
            @else
                <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Welcome, {{ $visit->visitor->first_name }}!</h3>
                <p class="text-slate-500 dark:text-slate-400 font-medium mt-2">Your check-in is complete. Please have a seat in the lobby.</p>
            @endif
        </header>
        
        @if($visit->booking)
            <div class="p-8 bg-indigo-50 dark:bg-indigo-900/20 rounded-[32px] border border-indigo-100 dark:border-indigo-800 text-center animate-pulse mt-4">
                <span class="text-[10px] font-black uppercase text-indigo-400 tracking-widest block mb-2">Room Assigned</span>
                <h4 class="text-2xl font-black text-indigo-700 dark:text-indigo-400">{{ $visit->booking->room->name }}</h4>
                <p class="text-indigo-600 dark:text-indigo-300 text-xs mt-1">Level 1, Floor 2</p>
            </div>
        @endif

        <div class="p-6 bg-slate-50 dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 mt-6">
            <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ $visit->host->name }} has been alerted.</p>
        </div>
        
        <div class="pt-6 border-t border-slate-100 dark:border-slate-800">
            <a href="{{ route('kiosk', ['tenant' => $visit->tenant->slug]) }}" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-indigo-600 transition-colors">
                Return to Kiosk
            </a>
        </div>
    @else
        <header>
            <div class="mx-auto h-16 w-16 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center text-3xl mb-4">⚡</div>
            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">Fast Pass</h3>
            <p class="text-slate-500 dark:text-slate-400 font-medium mt-2">Ready to check in for your meeting with **{{ $visit->host->name }}**?</p>
        </header>

        <div class="pt-4">
            <button wire:click="confirmCheckIn" class="inline-flex h-20 w-full items-center justify-center rounded-3xl bg-indigo-600 text-lg font-black uppercase tracking-widest text-white shadow-2xl shadow-indigo-200 dark:shadow-none transition-all hover:bg-indigo-700 active:scale-[0.98]">
                One-Tap Check In
            </button>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-6 font-bold uppercase tracking-widest">Visiting **{{ $visit->tenant->name }}**</p>
        </div>
    @endif
</div>
