<!-- resources/views/settings.blade.php -->
<x-layouts.app header="Office Management">
    <div class="max-w-4xl mx-auto w-full px-4 sm:px-0" x-data="{ tab: 'branding' }">
        <div class="mb-12">
            <div role="tablist" class="flex flex-wrap items-center justify-center gap-2 p-2 bg-slate-200 dark:bg-slate-900 rounded-[28px] border border-slate-300 dark:border-slate-800 shadow-inner">
                <button 
                    role="tab"
                    :aria-selected="tab === 'branding'"
                    aria-controls="panel-branding"
                    @click="tab = 'branding'"
                    :class="tab === 'branding' ? 'bg-white dark:bg-indigo-600 text-slate-900 dark:text-white shadow-md scale-105 border border-slate-200 dark:border-indigo-500' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    class="px-8 py-3.5 rounded-[20px] text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 ease-out outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                >
                    🎨 Branding
                </button>
                <button 
                    role="tab"
                    :aria-selected="tab === 'rooms'"
                    aria-controls="panel-rooms"
                    @click="tab = 'rooms'"
                    :class="tab === 'rooms' ? 'bg-white dark:bg-indigo-600 text-slate-900 dark:text-white shadow-md scale-105 border border-slate-200 dark:border-indigo-500' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                    class="px-8 py-3.5 rounded-[20px] text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 ease-out outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                >
                    🚪 Rooms
                </button>
                @if(auth()->user()->tenant->is_hub)
                    <button 
                        role="tab"
                        :aria-selected="tab === 'subtenants'"
                        aria-controls="panel-subtenants"
                        @click="tab = 'subtenants'"
                        :class="tab === 'subtenants' ? 'bg-white dark:bg-indigo-600 text-slate-900 dark:text-white shadow-md scale-105 border border-slate-200 dark:border-indigo-500' : 'text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200'"
                        class="px-8 py-3.5 rounded-[20px] text-[10px] font-black uppercase tracking-[0.2em] transition-all duration-300 ease-out outline-none focus-visible:ring-2 focus-visible:ring-indigo-500"
                    >
                        🏢 Sub-tenants
                    </button>
                @endif
            </div>
        </div>

        <!-- Tab Content -->
        <div class="relative">
            <div id="panel-branding" role="tabpanel" x-show="tab === 'branding'" x-cloak 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8"
                x-transition:enter-end="opacity-100 translate-y-0">
                @livewire('dashboard.tenant-settings')
            </div>

            <div id="panel-rooms" role="tabpanel" x-show="tab === 'rooms'" x-cloak 
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8"
                x-transition:enter-end="opacity-100 translate-y-0">
                @livewire('dashboard.meeting-room-manager')
            </div>

            @if(auth()->user()->tenant->is_hub)
                <div id="panel-subtenants" role="tabpanel" x-show="tab === 'subtenants'" x-cloak 
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-8"
                    x-transition:enter-end="opacity-100 translate-y-0">
                    @livewire('dashboard.sub-tenant-manager')
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>