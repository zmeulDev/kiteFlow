<!-- resources/views/livewire/superadmin/tenant-show.blade.php -->
<div class="space-y-10 pb-20">
    @section('title', "Manage: " . $tenant->name)

    <div class="flex items-center justify-between">
        <a href="{{ route('superadmin.tenants') }}" class="flex items-center text-sm font-bold text-slate-400 hover:text-indigo-600 transition-colors">
            <span class="mr-2">←</span> Back to List
        </a>
        <div class="flex items-center space-x-3">
            <span class="px-4 py-1.5 bg-slate-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest">{{ $tenant->plan }}</span>
            <div class="flex items-center space-x-2 px-3 py-1.5 bg-white rounded-xl border border-slate-100 shadow-sm">
                <div class="h-2 w-2 rounded-full {{ $tenant->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                <span class="text-[10px] font-black uppercase text-slate-600">{{ $tenant->status }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Business Administration Form -->
        <div class="lg:col-span-2 space-y-10">
            <form wire:submit.prevent="save" class="space-y-10">
                <!-- Section: Business Identity -->
                <div class="p-10 bg-white rounded-[40px] border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Business Identity</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Company Name</label>
                            <input wire:model="name" type="text" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                            @error('name') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Subdomain Slug</label>
                            <input wire:model="slug" type="text" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                            @error('slug') <span class="text-rose-500 text-[10px] font-bold">{{ $message }}</span> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Plan & Tier</label>
                            <select wire:model="plan" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 font-bold">
                                <option value="free">Starter (Free)</option>
                                <option value="pro">Professional</option>
                                <option value="enterprise">Enterprise</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Account Status</label>
                            <select wire:model="status" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 font-bold">
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Building Type</label>
                            <div class="flex items-center space-x-2 h-14 px-6 bg-slate-50 rounded-2xl border border-transparent">
                                <input wire:model="is_hub" type="checkbox" id="is_hub_show" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                <label for="is_hub_show" class="text-sm font-bold text-slate-600">Is Coworking Hub</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section: Financials -->
                <div class="p-10 bg-white rounded-[40px] border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Financials & Billing</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">VAT / Tax ID</label>
                            <input wire:model="vat_id" type="text" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-bold" placeholder="e.g. RO12345678">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Monthly Rate ($)</label>
                            <input wire:model="monthly_rate" type="number" step="0.01" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                        </div>
                    </div>
                    <div class="mt-8 space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Billing Address</label>
                        <textarea wire:model="billing_address" class="w-full min-h-[100px] p-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm"></textarea>
                    </div>
                </div>

                <!-- Section: Contacts -->
                <div class="p-10 bg-white rounded-[40px] border border-slate-100 shadow-sm">
                    <h3 class="text-xl font-black text-slate-900 tracking-tight mb-8">Contract & Contact</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Lead Contact Name</label>
                            <input wire:model="contact_name" type="text" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Contact Email</label>
                            <input wire:model="contact_email" type="email" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Contact Phone</label>
                            <input wire:model="contact_phone" type="text" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                        </div>
                        <div class="space-y-2">
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Subscription Renewal</label>
                            <input wire:model="subscription_ends_at" type="date" class="w-full h-14 px-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-bold">
                        </div>
                    </div>
                    <div class="mt-8 space-y-2">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest">Internal Contract Notes</label>
                        <textarea wire:model="contract_notes" placeholder="Special terms, SLA agreements, historical context..." class="w-full min-h-[150px] p-6 rounded-2xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all font-medium text-sm"></textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <button type="submit" class="h-16 px-12 bg-indigo-600 text-white rounded-[24px] font-black uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">
                        Commit Business Changes 🛡️
                    </button>
                </div>
            </form>
        </div>

        <!-- Insights Sidebar -->
        <div class="space-y-10">
            <div class="p-10 bg-slate-900 rounded-[40px] text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 text-4xl opacity-20">📊</div>
                <h3 class="text-lg font-black uppercase tracking-widest text-slate-500 mb-8">Tenant Pulse</h3>
                <div class="space-y-8">
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-1">Lifetime Visitors</span>
                        <div class="text-4xl font-black tracking-tighter">{{ number_format($tenant->visits()->count()) }}</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-1">Active Locations</span>
                        <div class="text-2xl font-black tracking-tight">{{ $tenant->locations()->count() }} Desks</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest block mb-1">Registered Staff</span>
                        <div class="text-2xl font-black tracking-tight">{{ $tenant->users()->count() }} Members</div>
                    </div>
                </div>
            </div>

            <div class="p-10 bg-white rounded-[40px] border border-slate-100 shadow-sm">
                <h3 class="text-lg font-black uppercase tracking-widest text-slate-400 mb-8">Quick Actions</h3>
                <div class="space-y-4">
                    <button wire:click="impersonate" class="w-full h-14 rounded-2xl bg-slate-50 font-bold text-slate-900 hover:bg-indigo-50 hover:text-indigo-600 transition-all flex items-center justify-center">
                        <span class="mr-2">⚡</span> Login as Admin
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
