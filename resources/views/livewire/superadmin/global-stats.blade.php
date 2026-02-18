<!-- resources/views/livewire/superadmin/global-stats.blade.php -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- MRR -->
    <div class="p-6 bg-slate-900 rounded-xl shadow-sm border border-slate-800">
        <div class="text-xs font-medium text-slate-400 uppercase tracking-wider">Est. Revenue (MRR)</div>
        <div class="mt-2 flex items-baseline gap-2">
            <span class="text-3xl font-bold text-white">${{ number_format($mrr) }}</span>
        </div>
    </div>

    <!-- Tenants -->
    <div class="p-6 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
        <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Global Tenants</div>
        <div class="mt-2 flex items-baseline gap-2">
            <span class="text-3xl font-bold text-slate-900 dark:text-white">{{ $totalTenants }}</span>
            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">+{{ $newTenantsThisWeek }} this week</span>
        </div>
    </div>

    <!-- Active Trials -->
    <div class="p-6 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
        <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Active Trials</div>
        <div class="mt-2">
            <span class="text-3xl font-bold text-slate-900 dark:text-white">{{ $activeTrials }}</span>
        </div>
    </div>

    <!-- Activity -->
    <div class="p-6 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
        <div class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Logs</div>
        <div class="mt-2">
            <span class="text-3xl font-bold text-slate-900 dark:text-white">{{ number_format($totalVisits) }}</span>
        </div>
    </div>
</div>
