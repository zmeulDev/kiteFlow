<!-- projects/visiflow/resources/views/livewire/dashboard/sub-tenant-manager.blade.php -->
<div class="space-y-12 animate-fade-in-up">
    <div class="p-8 sm:p-12 bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm transition-all">
        <header class="mb-10">
            <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight italic uppercase">Provision <span class="text-indigo-600">Sub-Tenant</span></h3>
            <p class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-widest mt-1">Onboard a new company to your hub</p>
        </header>

        <form wire:submit.prevent="save" class="space-y-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-[0.2em] ml-1">Company Name</label>
                    <input wire:model.live="name" type="text" placeholder="e.g. Innovate Labs" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-black uppercase tracking-tight focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                    @error('name') <span class="text-rose-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</span> @enderror
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-[0.2em] ml-1">Slug / Identifier</label>
                    <input wire:model="slug" type="text" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-black uppercase tracking-tight focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                    @error('slug') <span class="text-rose-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                @if($editingId)
                    <button type="button" wire:click="$reset" class="w-full sm:w-1/3 h-16 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] hover:bg-slate-200 dark:hover:bg-slate-700 transition-all active:scale-95 border border-slate-200 dark:border-slate-700">Cancel</button>
                @endif
                <button type="submit" class="group relative flex-1 h-16 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-lg shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-[0.98] border border-indigo-500">
                    <span class="relative z-10 flex items-center justify-center">
                        <span class="mr-3 text-xl group-hover:scale-125 transition-transform duration-300">🏢</span>
                        {{ $editingId ? 'Update Tenant Data' : 'Initialize Sub-Tenant' }}
                    </span>
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach($subtenants as $sub)
            <div wire:key="subtenant-{{ $sub->id }}" class="p-8 bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:border-indigo-500 transition-all group overflow-hidden relative">
                <div class="absolute -right-4 -top-4 text-6xl opacity-[0.03] dark:opacity-[0.05] group-hover:scale-110 group-hover:-rotate-12 transition-all grayscale">🏢</div>
                
                <div>
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h4 class="font-black text-2xl text-slate-900 dark:text-white group-hover:text-indigo-600 transition-colors leading-none italic uppercase">{{ $sub->name }}</h4>
                            <p class="text-[9px] font-black uppercase text-indigo-600/60 dark:text-indigo-400/60 tracking-[0.2em] mt-2 italic">{{ $sub->slug }}.kiteflow.io</p>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="edit({{ $sub->id }})" class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-500/10 transition-all border border-transparent hover:border-indigo-200" aria-label="Edit subtenant">✎</button>
                            <button 
                                @click="$dispatch('confirm', { 
                                    title: 'Delete {{ $sub->name }}?', 
                                    message: 'This will purge all associated data including users and visits.', 
                                    confirmText: 'Purge Tenant',
                                    variant: 'danger',
                                    onConfirm: () => $wire.delete({{ $sub->id }})
                                })"
                                class="h-8 w-8 flex items-center justify-center rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-all border border-transparent hover:border-rose-200"
                                aria-label="Delete subtenant"
                            >
                                ✕
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl border border-emerald-100 dark:border-emerald-800">
                            <span class="text-[10px] font-black uppercase text-emerald-600 dark:text-emerald-400 tracking-widest">Active Member</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>