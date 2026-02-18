<!-- projects/visiflow/resources/views/livewire/shared/registration-flow.blade.php -->
<div class="max-w-md mx-auto py-12">
    <div class="bg-white p-8 rounded-[32px] border border-slate-100 shadow-2xl shadow-slate-200">
        <header class="mb-8 text-center">
            <span class="text-3xl mb-4 block">🪁</span>
            <h2 class="text-2xl font-bold">Start your KiteFlow</h2>
            <p class="text-slate-500 text-sm">Join hundreds of offices managing visitors better.</p>
        </header>

        <form wire:submit.prevent="register" class="space-y-6">
            <div class="space-y-2">
                <label class="text-xs font-bold uppercase text-slate-400">Office / Company Name</label>
                <input wire:model="company_name" type="text" class="w-full h-12 px-4 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm" placeholder="Acme Corp">
                @error('company_name') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2 border-t border-slate-50 pt-6">
                <label class="text-xs font-bold uppercase text-slate-400">Admin Full Name</label>
                <input wire:model="admin_name" type="text" class="w-full h-12 px-4 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm" placeholder="John Doe">
                @error('admin_name') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2">
                <label class="text-xs font-bold uppercase text-slate-400">Email Address</label>
                <input wire:model="email" type="email" class="w-full h-12 px-4 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm" placeholder="john@example.com">
                @error('email') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <div class="space-y-2 pb-4">
                <label class="text-xs font-bold uppercase text-slate-400">Choose a Password</label>
                <input wire:model="password" type="password" class="w-full h-12 px-4 rounded-xl bg-slate-50 border-transparent focus:bg-white focus:ring-2 focus:ring-indigo-500 transition-all text-sm">
                @error('password') <span class="text-rose-500 text-[10px]">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="w-full h-14 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all active:scale-[0.98]">
                Create My Office 🚀
            </button>
        </form>
    </div>
</div>
