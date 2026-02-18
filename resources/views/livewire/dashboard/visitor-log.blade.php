<!-- projects/visiflow/resources/views/livewire/dashboard/visitor-log.blade.php -->
<div wire:poll.30s="refresh" class="space-y-6 mt-8">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-bold text-slate-900 dark:text-white">Recent Activity</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400">Visitor records for today</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="exportCsv" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">CSV</button>
            <button wire:click="exportPdf" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm font-medium text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">PDF</button>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search..." class="w-64 h-10 px-3 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-sm text-slate-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden">
        <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
            <thead class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-xs uppercase font-semibold text-slate-500 dark:text-slate-400">
                <tr>
                    <th class="px-6 py-4">Visitor</th>
                    <th class="px-6 py-4">Location</th>
                    <th class="px-6 py-4">Host</th>
                    <th class="px-6 py-4">Purpose</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Time</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                @forelse($visits as $visit)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $visit->visitor->full_name }}</div>
                            <div class="text-xs text-slate-500">{{ $visit->visitor->email }}</div>
                        </td>
                        <td class="px-6 py-4">{{ $visit->location->name ?? 'Main Desk' }}</td>
                        <td class="px-6 py-4">{{ $visit->host->name }}</td>
                        <td class="px-6 py-4">{{ $visit->purpose }}</td>
                        <td class="px-6 py-4">
                            @if($visit->checked_out_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300">Out</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">Active</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            {{ $visit->checked_in_at?->format('H:i') ?? '--:--' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-500">No visitors found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $visits->links() }}
    </div>
</div>
