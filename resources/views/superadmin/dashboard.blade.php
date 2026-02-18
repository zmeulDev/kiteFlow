<!-- resources/views/superadmin/dashboard.blade.php -->
<x-layouts.superadmin>
    @section('title', 'Global Overview')

    @livewire('superadmin.global-stats')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Onboarding -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                <h3 class="font-bold text-slate-900 dark:text-white">Recent Onboarding</h3>
                <a href="{{ route('superadmin.tenants') }}" class="text-sm text-indigo-600 hover:underline">View All</a>
            </div>
            <div class="divide-y divide-slate-200 dark:divide-slate-800">
                @foreach(\App\Models\Tenant::whereNull('parent_id')->latest()->take(5)->get() as $tenant)
                    <div class="px-6 py-4 flex justify-between items-center hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <div>
                            <div class="font-medium text-slate-900 dark:text-white">{{ $tenant->name }}</div>
                            <div class="text-xs text-slate-500">{{ $tenant->slug }}.kiteflow.io</div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="px-2 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-medium rounded">{{ $tenant->plan }}</span>
                            <span class="text-xs text-slate-400">{{ $tenant->created_at->format('M d') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- System Activity -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-900 dark:text-white">Live Activity</h3>
            </div>
            <div class="p-6 space-y-6">
                @foreach(\App\Models\Visit::with(['visitor', 'tenant'])->latest()->take(6)->get() as $visit)
                    <div class="flex gap-4">
                        <div class="relative flex flex-col items-center">
                            <div class="h-2 w-2 bg-indigo-600 rounded-full"></div>
                            <div class="w-px h-full bg-slate-200 dark:bg-slate-800 mt-1"></div>
                        </div>
                        <div class="pb-2">
                            <div class="text-sm font-medium text-slate-900 dark:text-white">
                                {{ $visit->visitor->full_name }} <span class="text-slate-500 font-normal">at {{ $visit->tenant->name }}</span>
                            </div>
                            <div class="text-xs text-slate-500">{{ $visit->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @endforeach
                @if(\App\Models\Visit::count() == 0)
                    <div class="text-center text-slate-500 py-4">No activity recorded yet.</div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.superadmin>
