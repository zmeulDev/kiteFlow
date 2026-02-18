<!-- projects/visiflow/resources/views/livewire/dashboard/tenant-settings.blade.php -->
<div class="space-y-10 animate-fade-in-up">
    <div class="p-8 sm:p-12 bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm transition-all">
        <header class="mb-10">
            <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight italic uppercase">Space <span class="text-indigo-600">Branding</span></h3>
            <p class="text-[10px] text-slate-400 dark:text-slate-400 font-black uppercase tracking-widest mt-1">Customize your office branding and rules</p>
        </header>

        <form wire:submit.prevent="save" class="space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-[0.2em] ml-1">Office Name</label>
                    <input wire:model="name" type="text" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-[0.2em] ml-1">Primary Brand Color</label>
                    <div class="flex items-center space-x-3 p-1.5 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-inner">
                        <input wire:model="primary_color" type="color" class="h-11 w-14 rounded-xl border-0 p-0 overflow-hidden cursor-pointer shadow-sm">
                        <input wire:model="primary_color" type="text" class="flex-1 bg-transparent border-0 text-sm font-black text-slate-900 dark:text-white focus:ring-0 uppercase tracking-widest">
                    </div>
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-[0.2em] ml-1">Guest Requirements</label>
                    <label for="require_photo" class="flex items-center space-x-4 h-14 px-6 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800 cursor-pointer hover:bg-white dark:hover:bg-slate-900 transition-all group shadow-inner">
                        <input wire:model="require_photo" type="checkbox" id="require_photo" class="h-6 w-6 rounded-lg border-slate-300 dark:border-slate-700 text-indigo-600 focus:ring-indigo-500/20 transition-all cursor-pointer">
                        <span class="text-sm font-bold text-slate-600 dark:text-slate-400 group-hover:text-indigo-600 transition-colors">Require visitor photo</span>
                    </label>
                </div>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-[0.2em] ml-1">Terms & Conditions (Kiosk)</label>
                <textarea wire:model="terms_text" class="w-full min-h-[160px] px-6 py-5 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm leading-relaxed focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm"></textarea>
            </div>

            <button type="submit" class="group relative w-full h-16 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-[0.98] border border-indigo-500">
                <span class="relative z-10 flex items-center justify-center">
                    <span class="mr-3 text-xl group-hover:rotate-12 transition-transform">🎨</span>
                    Save Branding Configuration
                </span>
            </button>
        </form>
    </div>

    <!-- Location Management -->
    <div class="p-8 sm:p-12 bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm transition-all">
        <header class="mb-10">
            <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight italic uppercase">Office <span class="text-indigo-600">Zones</span></h3>
            <p class="text-[10px] text-slate-400 dark:text-slate-400 font-black uppercase tracking-widest mt-1">Manage reception desks and building zones</p>
        </header>

        <div class="space-y-4">
            @foreach($locations as $location)
                <div class="flex items-center justify-between p-6 bg-slate-50 dark:bg-slate-950 rounded-2xl border border-slate-200 dark:border-slate-800 group hover:bg-white dark:hover:bg-slate-800 hover:shadow-lg transition-all">
                    <div class="flex items-center space-x-4">
                        <div class="h-12 w-12 bg-white dark:bg-slate-800 rounded-xl flex items-center justify-center text-xl shadow-sm border border-slate-100 dark:border-slate-700 group-hover:scale-110 transition-transform">📍</div>
                        <div>
                            <div class="font-black text-slate-900 dark:text-white tracking-tight">{{ $location->name }}</div>
                            <div class="text-[9px] text-indigo-600 dark:text-indigo-400 font-black uppercase tracking-[0.2em] mt-0.5">{{ $location->slug }}</div>
                        </div>
                    </div>
                    <button 
                        @click="$dispatch('confirm', { 
                            title: 'Remove {{ $location->name }}?', 
                            message: 'This will deactivate all kiosks associated with this zone.', 
                            confirmText: 'Remove Zone',
                            variant: 'danger',
                            onConfirm: () => $wire.deleteLocation({{ $location->id }})
                        })"
                        class="h-10 w-10 flex items-center justify-center rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-all border border-transparent hover:border-rose-200 dark:hover:border-rose-500/30"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>
            @endforeach

            <div class="pt-8 border-t border-slate-100 dark:border-slate-800 mt-8">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="relative flex-1 w-full">
                        <input wire:model="new_location_name" type="text" placeholder="e.g. West Wing Reception" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white text-sm font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                        <span class="absolute left-4 -top-2.5 px-2 bg-white dark:bg-slate-950 text-[9px] font-black uppercase tracking-widest text-slate-400 dark:text-slate-400">New Zone Name</span>
                    </div>
                    <button wire:click="addLocation" class="w-full sm:w-auto h-14 px-10 bg-slate-900 dark:bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] hover:bg-slate-800 dark:hover:bg-indigo-700 transition-all active:scale-95 shadow-lg border border-slate-800 dark:border-indigo-500">Add Zone</button>
                </div>
                @error('new_location_name') <span class="text-rose-500 text-[10px] font-black uppercase tracking-widest mt-3 ml-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>
