<!-- projects/visiflow/resources/views/livewire/kiosk/kiosk-main.blade.php -->
<div class="w-full">
    @if($mode === 'check-in')
        @livewire('kiosk.check-in', ['tenant' => $tenant])
        <div class="mt-8 text-center border-t border-slate-100 dark:border-slate-800 pt-8">
            <button wire:click="showCheckOut" class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-indigo-600 transition-colors">
                Already visited? <span class="underline text-indigo-600">Check Out Here</span>
            </button>
        </div>
    @else
        @livewire('kiosk.check-out', ['tenant' => $tenant])
    @endif
</div>
