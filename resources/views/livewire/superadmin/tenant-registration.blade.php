<!-- resources/views/livewire/superadmin/tenant-registration.blade.php -->
<div>
    <button wire:click="$set('isOpen', true)" class="h-12 px-6 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all active:scale-95">
        + Register New Tenant
    </button>

    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
            <div class="bg-white rounded-[40px] w-full max-w-2xl p-10 shadow-2xl animate-in zoom-in duration-300 overflow-y-auto max-h-[90vh]">
                <header class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Register New Tenant</h3>
                        <p class="text-slate-500 text-sm">Onboard a new company and create their admin account.</p>
                    </div>
                    <button wire:click="$set('isOpen', false)" class="h-10 w-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-slate-900 transition-all">✕</button>
                </header>

                <form wire:submit.prevent="register" class="space-y-8">
                    <!-- Company Info -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest border-b border-slate-50 pb-2 block">Company Information</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Company Name</label>
                                <input wire:model.live="company_name" type="text" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold" placeholder="Acme Corp">
                                @error('company_name') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Subdomain Slug</label>
                                <input wire:model="slug" type="text" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold" placeholder="acme-corp">
                                @error('slug') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-600">Initial Plan</label>
                            <select wire:model="plan" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold">
                                <option value="free">Starter (Free)</option>
                                <option value="pro">Pro Plan</option>
                                <option value="enterprise">Enterprise</option>
                            </select>
                        </div>
                    </div>

                    <!-- Admin User Info -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest border-b border-slate-50 pb-2 block">Admin User Details</label>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-600">Admin Name</label>
                            <input wire:model="admin_name" type="text" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold" placeholder="John Doe">
                            @error('admin_name') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Admin Email</label>
                                <input wire:model="admin_email" type="email" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold" placeholder="admin@acme.com">
                                @error('admin_email') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Initial Password</label>
                                <input wire:model="admin_password" type="password" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold">
                                @error('admin_password') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex gap-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('isOpen', false)" class="flex-1 h-14 rounded-2xl bg-slate-100 font-black uppercase tracking-widest text-slate-500 hover:bg-slate-200 transition-all">Cancel</button>
                        <button type="submit" class="flex-1 h-14 rounded-2xl bg-indigo-600 text-white font-black uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">Register Tenant</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
