<!-- resources/views/livewire/superadmin/tenant-editor.blade.php -->
<div>
    @if($isOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200">
            <div class="bg-white rounded-[40px] w-full max-w-2xl p-10 shadow-2xl animate-in zoom-in duration-300 overflow-y-auto max-h-[90vh]">
                <header class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight">Quick Edit Tenant</h3>
                        <p class="text-slate-500 text-sm">Managing: <span class="font-bold text-indigo-600">{{ $name }}</span></p>
                    </div>
                    <button wire:click="$set('isOpen', false)" class="h-10 w-10 flex items-center justify-center rounded-full bg-slate-50 text-slate-400 hover:text-slate-900 transition-all">✕</button>
                </header>

                <form wire:submit.prevent="save" class="space-y-8">
                    <!-- General Info -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest border-b border-slate-50 pb-2 block">General Information</label>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Company Name</label>
                                <input wire:model="name" type="text" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold">
                                @error('name') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Subdomain Slug</label>
                                <input wire:model="slug" type="text" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold">
                                @error('slug') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest border-b border-slate-50 pb-2 block">Primary Contact</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Contact Name</label>
                                <input wire:model="contact_name" type="text" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Contact Email</label>
                                <input wire:model="contact_email" type="email" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold">
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Contact Phone</label>
                                <input wire:model="contact_phone" type="text" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold">
                            </div>
                        </div>
                    </div>

                    <!-- Billing & Subscription -->
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest border-b border-slate-50 pb-2 block">Billing & Subscription</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Service Plan</label>
                                <select wire:model="plan" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold">
                                    <option value="free">Starter (Free)</option>
                                    <option value="pro">Pro Plan</option>
                                    <option value="enterprise">Enterprise</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Account Status</label>
                                <select wire:model="status" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold">
                                    <option value="active">Active</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Building Type</label>
                                <div class="flex items-center space-x-2 h-12 px-4 bg-slate-50 rounded-xl border border-slate-200">
                                    <input wire:model="is_hub" type="checkbox" id="is_hub" class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="is_hub" class="text-sm font-bold text-slate-600">Is Coworking Hub</label>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <label class="text-xs font-bold text-slate-600">Subscription Ends</label>
                                <input wire:model="subscription_ends_at" type="date" class="w-full h-12 px-4 rounded-xl border border-slate-200 bg-slate-50 text-sm focus:ring-2 focus:ring-indigo-500 font-bold">
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 flex gap-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('isOpen', false)" class="flex-1 h-14 rounded-2xl bg-slate-100 font-black uppercase tracking-widest text-slate-500 hover:bg-slate-200 transition-all">Cancel</button>
                        <button type="submit" class="flex-1 h-14 rounded-2xl bg-indigo-600 text-white font-black uppercase tracking-widest shadow-xl shadow-indigo-100 hover:bg-indigo-700 transition-all active:scale-95">Update Tenant</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
