<!-- resources/views/livewire/superadmin/tenant-list.blade.php -->
<div class="space-y-8">
    <!-- Search & Filters -->
    <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-6 bg-white p-6 rounded-[32px] border border-slate-100 shadow-sm">
        <div class="relative flex-1">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by name or email..." class="w-full h-14 pl-12 pr-4 rounded-2xl border border-slate-200 bg-slate-50/50 text-sm focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
            <span class="absolute left-4 top-4 text-xl opacity-40">🔍</span>
        </div>
        
        <div class="flex flex-wrap items-center gap-4">
            <select wire:model.live="planFilter" class="h-14 px-6 rounded-2xl border border-slate-200 bg-white text-sm font-black uppercase tracking-widest text-slate-600 focus:ring-2 focus:ring-indigo-500">
                <option value="">All Plans</option>
                <option value="free">Free</option>
                <option value="pro">Pro</option>
                <option value="enterprise">Enterprise</option>
            </select>

            <select wire:model.live="statusFilter" class="h-14 px-6 rounded-2xl border border-slate-200 bg-white text-sm font-black uppercase tracking-widest text-slate-600 focus:ring-2 focus:ring-indigo-500">
                <option value="">All Status</option>
                <option value="active">Active Only</option>
                <option value="suspended">Suspended Only</option>
            </select>
        </div>
    </div>

    <!-- Tenant Cards -->
    <div class="space-y-6">
        @forelse($tenants as $tenant)
            <div wire:key="tenant-{{ $tenant->id }}" class="p-10 bg-white rounded-[40px] border border-slate-100 shadow-sm flex flex-col xl:flex-row xl:items-center justify-between gap-8 hover:border-indigo-200 transition-all hover:shadow-xl hover:shadow-indigo-50/50 group">
                <div class="flex items-start space-x-6 min-w-[350px]">
                    <div class="h-20 w-20 rounded-3xl bg-slate-50 flex items-center justify-center text-4xl shadow-inner group-hover:scale-110 transition-transform duration-500">🏢</div>
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tighter group-hover:text-indigo-600 transition-colors leading-tight">{{ $tenant->name }}</h3>
                        <p class="text-slate-400 text-xs font-bold tracking-widest uppercase mt-1">{{ $tenant->slug }}.kiteflow.io</p>
                        
                        <div class="mt-6 flex flex-col space-y-2">
                            <div class="flex items-center space-x-2">
                                <div class="h-5 w-5 rounded-lg bg-indigo-50 flex items-center justify-center text-[10px] font-bold text-indigo-600 border border-indigo-100">👤</div>
                                <div class="text-[11px] font-black text-slate-700 uppercase tracking-tighter">
                                    {{ $tenant->contact_name ?? 'NOT ASSIGNED' }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="h-5 w-5 rounded-lg bg-slate-50 flex items-center justify-center text-[10px] font-bold text-slate-400 border border-slate-100">✉️</div>
                                <div class="text-[11px] font-bold text-slate-400 truncate max-w-[200px]">
                                    {{ $tenant->contact_email ?? 'NO EMAIL' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-12 flex-1 border-l border-slate-50 pl-8">
                    <div>
                        <span class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Service Plan</span>
                        <span class="px-4 py-1.5 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-md">{{ $tenant->plan }}</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Valid Until</span>
                        <span class="text-sm font-black {{ $tenant->subscription_ends_at && $tenant->subscription_ends_at->isPast() ? 'text-rose-500' : 'text-slate-900' }}">
                            {{ $tenant->subscription_ends_at?->format('M d, Y') ?? 'LIFETIME' }}
                        </span>
                        <div class="text-[10px] text-slate-400 font-bold mt-0.5 tracking-tighter">Billing Cycle Active</div>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Activity Log</span>
                        <span class="text-lg font-black text-slate-900 tracking-tight">{{ number_format($tenant->visits_count) }} <span class="text-[10px] text-slate-400 font-bold uppercase ml-1">Total</span></span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Health</span>
                        <div class="flex items-center space-x-2">
                            <div class="h-2 w-2 rounded-full {{ $tenant->status === 'active' ? 'bg-emerald-500 animate-pulse' : 'bg-rose-500' }}"></div>
                            <span class="text-xs font-black uppercase tracking-widest {{ $tenant->status === 'active' ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ $tenant->status }}
                            </span>
                            @if($tenant->is_hub)
                                <span class="ml-2 px-2 py-0.5 bg-indigo-600 text-white text-[8px] font-black uppercase rounded-md tracking-tighter shadow-sm">HUB</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-3 pt-6 xl:pt-0 border-t xl:border-0 border-slate-50">
                    <a 
                        href="{{ route('superadmin.tenants.show', $tenant->id) }}"
                        class="flex-1 xl:flex-none px-6 py-4 rounded-2xl bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-slate-800 transition-all active:scale-95 text-center shadow-lg shadow-slate-200"
                    >
                        Manage
                    </a>
                    <button 
                        wire:click="impersonate({{ $tenant->id }})"
                        class="flex-1 xl:flex-none px-6 py-4 rounded-2xl bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95"
                    >
                        Login
                    </button>
                </div>
            </div>
        @empty
            <div class="p-20 text-center bg-white rounded-[40px] border border-dashed border-slate-200">
                <span class="text-6xl block mb-6">🔍</span>
                <h3 class="text-xl font-bold text-slate-900">No tenants found</h3>
                <p class="text-slate-400 text-sm mt-2">Try adjusting your filters or search terms.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $tenants->links() }}
    </div>
</div>
