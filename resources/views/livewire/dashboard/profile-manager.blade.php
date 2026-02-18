<!-- resources/views/livewire/dashboard/profile-manager.blade.php -->
<div class="space-y-12 animate-fade-in-up">
    <div class="p-8 sm:p-12 bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm transition-all">
        <header class="mb-10">
            <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight italic uppercase">Account <span class="text-indigo-600">Settings</span></h3>
            <p class="text-[10px] text-slate-400 dark:text-slate-400 font-black uppercase tracking-widest mt-1">Update your personal identification</p>
        </header>

        <form wire:submit.prevent="updateProfile" class="space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-[0.2em] ml-1">Full Name</label>
                    <input wire:model="name" type="text" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-[0.2em] ml-1">Email Address</label>
                    <input wire:model="email" type="email" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white font-medium focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                </div>
            </div>
            <button type="submit" class="group relative h-14 px-10 bg-slate-900 dark:bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] shadow-lg hover:bg-slate-800 dark:hover:bg-indigo-700 transition-all active:scale-95 border border-slate-700 dark:border-indigo-500">
                Update Identity
            </button>
        </form>
    </div>

    <div class="p-8 sm:p-12 bg-white dark:bg-slate-900 rounded-[32px] border border-slate-200 dark:border-slate-800 shadow-sm transition-all">
        <header class="mb-10">
            <h3 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight italic uppercase">Security <span class="text-rose-600">Vault</span></h3>
            <p class="text-[10px] text-slate-400 dark:text-slate-400 font-black uppercase tracking-widest mt-1">Manage your access credentials</p>
        </header>

        <form wire:submit.prevent="updatePassword" class="space-y-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-[0.2em] ml-1">Current Password</label>
                    <input wire:model="current_password" type="password" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                </div>
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase text-slate-400 dark:text-slate-400 tracking-[0.2em] ml-1">New Password</label>
                    <input wire:model="new_password" type="password" class="w-full h-14 px-6 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-950 text-slate-900 dark:text-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all shadow-sm">
                </div>
            </div>
            <button type="submit" class="group relative h-14 px-10 bg-indigo-600 text-white rounded-2xl font-black uppercase tracking-[0.2em] text-[10px] shadow-lg hover:bg-indigo-700 transition-all active:scale-95 border border-indigo-500">
                Set New Password
            </button>
        </form>
    </div>
</div>
 </button>
        </form>
    </div>
</div>