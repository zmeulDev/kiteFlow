<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Support & Helpdesk</h1>
            <p class="text-sm text-gray-500">Manage support tickets and help resources</p>
        </div>
        <button class="px-4 py-2 text-sm font-semibold text-white bg-[#FF4B4B] rounded-lg hover:bg-[#E63E3E]">
            <i class="fa-solid fa-plus mr-2"></i>New Ticket
        </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ count($tickets) }}</p>
                    <p class="text-xs text-gray-500">Total Tickets</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-amber-600">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ collect($tickets)->where('status', 'open')->count() }}</p>
                    <p class="text-xs text-gray-500">Open</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="fa-solid fa-spinner"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ collect($tickets)->where('status', 'in_progress')->count() }}</p>
                    <p class="text-xs text-gray-500">In Progress</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center text-green-600">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ collect($tickets)->where('status', 'resolved')->count() }}</p>
                    <p class="text-xs text-gray-500">Resolved</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="flex gap-6 -mb-px">
            <button wire:click="$set('tab', 'tickets')" 
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $tab === 'tickets' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Support Tickets
            </button>
            <button wire:click="$set('tab', 'faqs')" 
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $tab === 'faqs' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                FAQs
            </button>
            <button wire:click="$set('tab', 'resources')" 
                    class="pb-3 text-sm font-medium border-b-2 transition-colors {{ $tab === 'resources' ? 'border-[#FF4B4B] text-[#FF4B4B]' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                Resources
            </button>
        </nav>
    </div>

    @switch($tab)
        @case('tickets')
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="flex-1 relative">
                    <i class="absolute left-3 top-1/2 -translate-y-1/2 fa-solid fa-magnifying-glass text-gray-400"></i>
                    <input type="text" 
                           wire:model.live.debounce.300ms="search" 
                           placeholder="Search tickets..."
                           class="w-full pl-10 pr-4 py-2.5 text-sm bg-white border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#FF4B4B]/20">
                </div>
                <select wire:model="status_filter" class="px-4 py-2.5 text-sm bg-white border border-gray-200 rounded-lg">
                    <option value="">All Status</option>
                    <option value="open">Open</option>
                    <option value="in_progress">In Progress</option>
                    <option value="resolved">Resolved</option>
                </select>
            </div>

            <!-- Tickets List -->
            <div class="bg-white rounded-xl border border-gray-100 overflow-hidden">
                @if(count($tickets) > 0)
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Ticket</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Created</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($tickets as $ticket)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-gray-900">#{{ $ticket['id'] }}</p>
                                <p class="text-sm text-gray-600">{{ $ticket['subject'] }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $ticket['tenant'] }}
                            </td>
                            <td class="px-6 py-4">
                                @switch($ticket['priority'])
                                    @case('high')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">High</span>
                                        @break
                                    @case('medium')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Medium</span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Low</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4">
                                @switch($ticket['status'])
                                    @case('open')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Open</span>
                                        @break
                                    @case('in_progress')
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">In Progress</span>
                                        @break
                                    @default
                                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Resolved</span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $ticket['created_at'] }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="px-3 py-1.5 text-xs font-medium text-[#FF4B4B] bg-[#FF4B4B]/10 rounded-lg hover:bg-[#FF4B4B]/20">
                                    View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-ticket text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-900">No tickets found</p>
                    <p class="text-sm text-gray-500 mt-1">Try adjusting your filters</p>
                </div>
                @endif
            </div>
            @break

        @case('faqs')
            <!-- FAQs -->
            <div class="space-y-4">
                @foreach($faqs as $index => $faq)
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <details class="group">
                        <summary class="flex items-center justify-between cursor-pointer list-none">
                            <h3 class="text-sm font-semibold text-gray-900">{{ $faq['question'] }}</h3>
                            <i class="fa-solid fa-chevron-down text-gray-400 transition-transform group-open:rotate-180"></i>
                        </summary>
                        <p class="mt-4 text-sm text-gray-600">{{ $faq['answer'] }}</p>
                    </details>
                </div>
                @endforeach
            </div>
            @break

        @case('resources')
            <!-- Resources -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 mb-4">
                        <i class="fa-solid fa-book text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Documentation</h3>
                    <p class="text-sm text-gray-500 mb-4">Complete documentation for all features and integrations.</p>
                    <a href="#" class="text-sm font-medium text-[#FF4B4B] hover:underline">View Docs →</a>
                </div>
                
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600 mb-4">
                        <i class="fa-solid fa-video text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Video Tutorials</h3>
                    <p class="text-sm text-gray-500 mb-4">Watch step-by-step video guides for common tasks.</p>
                    <a href="#" class="text-sm font-medium text-[#FF4B4B] hover:underline">Watch Now →</a>
                </div>
                
                <div class="bg-white rounded-xl border border-gray-100 p-6">
                    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-green-600 mb-4">
                        <i class="fa-solid fa-envelope text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Contact Support</h3>
                    <p class="text-sm text-gray-500 mb-4">Need more help? Open a support ticket.</p>
                    <button class="text-sm font-medium text-[#FF4B4B] hover:underline">Open Ticket →</button>
                </div>
            </div>
            @break
    @endswitch
</div>