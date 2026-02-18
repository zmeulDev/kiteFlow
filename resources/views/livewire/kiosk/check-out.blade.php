<!-- projects/visiflow/resources/views/livewire/kiosk/check-out.blade.php -->
<div class="flex flex-col justify-center space-y-8 w-full max-w-md mx-auto">
    <header>
        <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ __('Check Out') }}</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm font-medium mt-1">Thank you for visiting. Please enter your email to check out.</p>
    </header>

    <form wire:submit.prevent="submit" class="space-y-6">
        <div class="space-y-2">
            <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-widest" for="email">{{ __('messages.email') }}</label>
            <input wire:model="email" id="email" type="email" class="flex h-14 w-full rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 px-4 py-2 text-sm font-bold dark:text-white focus:ring-2 focus:ring-indigo-500" placeholder="john.doe@example.com" required>
            @error('email') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="inline-flex h-16 w-full items-center justify-center rounded-2xl bg-slate-900 dark:bg-slate-700 text-lg font-black uppercase tracking-widest text-white shadow-xl transition-all hover:bg-slate-800 active:scale-[0.98]">
            Complete Check-out
        </button>

        <div class="text-center pt-4">
            <button type="button" wire:click="$dispatch('switch-to-check-in')" class="text-[10px] font-black uppercase tracking-widest text-indigo-600 dark:text-indigo-400 hover:underline">
                Back to Check-in
            </button>
        </div>
    </form>
</div>
