<div class="space-y-4 lg:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Meetings</h1>
            <p class="mt-1 text-sm text-gray-500">Schedule and manage meetings</p>
        </div>
        <button wire:click="openModal" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
            <i class="fa-solid fa-plus"></i>
            <span>Schedule Meeting</span>
        </button>
    </div>

    <!-- Search & Filters -->
    <div class="bg-white rounded-xl border border-gray-200 p-3 lg:p-4">
        <div class="flex items-center gap-3 flex-wrap">
            <div class="relative w-full sm:w-48">
                <i class="absolute left-2.5 top-1/2 -translate-y-1/2 fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search..."
                       class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>
            <select wire:model.live="statusFilter"
                    class="px-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="all">All Status</option>
                <option value="scheduled">Scheduled</option>
                <option value="in_progress">In Progress</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <button wire:click="$set('showCalendar', true)"
                        class="flex-1 px-3 py-1 text-xs font-medium rounded-md transition-colors {{ $showCalendar ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' }}">
                    <i class="fa-solid fa-calendar"></i>
                </button>
                <button wire:click="$set('showCalendar', false)"
                        class="flex-1 px-3 py-1 text-xs font-medium rounded-md transition-colors {{ !$showCalendar ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600' }}">
                    <i class="fa-solid fa-list"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Calendar View -->
    @if($showCalendar)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <!-- Calendar Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 px-4 py-3 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <button wire:click="previousMonth" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                    <i class="fa-solid fa-chevron-left"></i>
                </button>
                <button wire:click="goToToday" class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Today
                </button>
                <button wire:click="nextMonth" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">{{ $calendarData['monthName'] }} {{ $calendarData['year'] }}</h2>
            <div class="flex items-center gap-2 flex-wrap">
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    <span class="text-xs text-gray-500">Scheduled</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <span class="text-xs text-gray-500">In Progress</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-xs text-gray-500">Completed</span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <span class="text-xs text-gray-500">Cancelled</span>
                </div>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="p-4">
            <!-- Day Headers -->
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; margin-bottom: 8px;" class="w-full">
                <div class="text-center text-xs font-semibold text-gray-500 py-2">Mon</div>
                <div class="text-center text-xs font-semibold text-gray-500 py-2">Tue</div>
                <div class="text-center text-xs font-semibold text-gray-500 py-2">Wed</div>
                <div class="text-center text-xs font-semibold text-gray-500 py-2">Thu</div>
                <div class="text-center text-xs font-semibold text-gray-500 py-2">Fri</div>
                <div class="text-center text-xs font-semibold text-gray-500 py-2">Sat</div>
                <div class="text-center text-xs font-semibold text-gray-500 py-2">Sun</div>
            </div>

            <!-- Calendar Days -->
            <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px;" class="w-full">
                <!-- Empty cells for days before month starts -->
                @for($i = 0; $i < $calendarData['startingDayOfWeek']; $i++)
                <div style="min-height: 100px;" class="border border-transparent"></div>
                @endfor

                <!-- Days of the month -->
                @for($day = 1; $day <= $calendarData['daysInMonth']; $day++)
                    @php
                        $dateString = sprintf('%04d-%02d-%02d', $calendarData['year'], $calendarData['month'], $day);
                        $isToday = $dateString === now()->format('Y-m-d');
                        $dayMeetings = $calendarData['meetingsByDay'][$day] ?? [];
                    @endphp
                    <div style="min-height: 100px;" class="border border-gray-200 rounded-lg p-2 @if($isToday) bg-blue-50 border-blue-300 @else bg-white @endif hover:border-brand-200 transition-colors flex flex-col">
                        <div class="flex-shrink-0">
                            <span class="text-sm font-semibold @if($isToday) text-blue-700 @else text-gray-700 @endif">{{ $day }}</span>
                        </div>
                        <div class="flex-1 overflow-y-auto space-y-1" style="max-height: 72px;">
                            @foreach($dayMeetings as $meeting)
                            <a href="{{ route('meetings.show', $meeting) }}"
                               class="block text-xs px-2 py-1 rounded truncate leading-tight text-left @if($meeting->status === 'scheduled') bg-blue-100 text-blue-700 hover:bg-blue-200
                                                            @elseif($meeting->status === 'in_progress') bg-amber-100 text-amber-700 hover:bg-amber-200
                                                            @elseif($meeting->status === 'completed') bg-green-100 text-green-700 hover:bg-green-200
                                                            @elseif($meeting->status === 'cancelled') bg-red-100 text-red-700 hover:bg-red-200
                                                            @endif">
                                {{ $meeting->title }}
                            </a>
                            @endforeach
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- Legend for selected month -->
        <div class="px-4 py-3 border-t border-gray-100 bg-gray-50">
            <p class="text-xs text-gray-500">
                <span class="font-medium">{{ count($calendarMeetings) }}</span> meeting{{ count($calendarMeetings) != 1 ? 's' : '' }} in {{ $calendarData['monthName'] }} {{ $calendarData['year'] }}
            </p>
        </div>
    </div>
    @endif

    <!-- List View -->
    @if(!$showCalendar)
    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
        @forelse($meetings as $meeting)
        <a href="{{ route('meetings.show', $meeting) }}" class="block bg-white rounded-xl border border-gray-200 p-4 hover:border-brand-300 transition-colors">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 @if($meeting->meeting_type === 'in_person') bg-blue-100 text-blue-600 @elseif($meeting->meeting_type === 'virtual') bg-purple-100 text-purple-600 @else bg-green-100 text-green-600 @endif">
                    @if($meeting->meeting_type === 'in_person')
                        <i class="fa-solid fa-building"></i>
                    @elseif($meeting->meeting_type === 'virtual')
                        <i class="fa-solid fa-video"></i>
                    @else
                        <i class="fa-solid fa-building-user"></i>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-medium text-gray-900">{{ $meeting->title }}</h3>
                    <div class="mt-2 flex items-center gap-2 text-sm text-gray-500">
                        <i class="fa-solid fa-clock text-gray-400"></i>
                        <span>{{ $meeting->start_at->format('M j, g:i A') }} - {{ $meeting->end_at->format('g:i A') }}</span>
                    </div>
                    @if($meeting->meetingRoom)
                    <div class="mt-1 flex items-center gap-2 text-sm text-gray-500">
                        <i class="fa-solid fa-door-open text-gray-400"></i>
                        <span>{{ $meeting->meetingRoom->name }}</span>
                    </div>
                    @endif
                </div>
                <span class="flex-shrink-0 px-2 py-1 text-xs font-medium rounded-full {{ $meeting->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : ($meeting->status === 'completed' ? 'bg-green-100 text-green-700' : ($meeting->status === 'cancelled' ? 'bg-red-100 text-red-700' : ($meeting->status === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700'))) }}">
                    @if($meeting->status === 'in_progress')
                    In Progress
                    @else
                    {{ ucfirst($meeting->status) }}
                    @endif
                </span>
            </div>
        </a>
        @empty
        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
            <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-calendar text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-medium text-gray-900">No meetings found</p>
            <p class="text-sm text-gray-500 mt-1">Schedule your first meeting to get started</p>
            <button wire:click="openModal" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100">
                <i class="fa-solid fa-plus"></i>
                Schedule Meeting
            </button>
        </div>
        @endforelse

        @if($meetings->hasPages())
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <div class="flex items-center justify-between gap-4">
                <p class="text-xs text-gray-500">
                    {{ $meetings->firstItem() }}-{{ $meetings->lastItem() }} of {{ $meetings->total() }}
                </p>
                <div class="flex items-center gap-2">
                    @if($meetings->onFirstPage())
                    <span class="p-2 text-gray-300"><i class="fa-solid fa-chevron-left text-xs"></i></span>
                    @else
                    <button wire:click="previousPage()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    @endif
                    @if($meetings->hasMorePages())
                    <button wire:click="nextPage()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                    @else
                    <span class="p-2 text-gray-300"><i class="fa-solid fa-chevron-right text-xs"></i></span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meeting</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($meetings as $meeting)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 @if($meeting->meeting_type === 'in_person') bg-blue-100 text-blue-600 @elseif($meeting->meeting_type === 'virtual') bg-purple-100 text-purple-600 @else bg-green-100 text-green-600 @endif">
                                    @if($meeting->meeting_type === 'in_person')
                                        <i class="fa-solid fa-building text-xs"></i>
                                    @elseif($meeting->meeting_type === 'virtual')
                                        <i class="fa-solid fa-video text-xs"></i>
                                    @else
                                        <i class="fa-solid fa-building-user text-xs"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $meeting->title }}</p>
                                    @if($meeting->description)
                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $meeting->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm text-gray-600">
                                <i class="fa-solid fa-door-open text-gray-400 mr-1"></i>
                                {{ $meeting->meetingRoom?->name ?? 'No Room' }}
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-sm text-gray-600">
                                <div>{{ $meeting->start_at->format('M j, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $meeting->start_at->format('g:i A') }} - {{ $meeting->end_at->format('g:i A') }}</div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $meeting->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : ($meeting->status === 'completed' ? 'bg-green-100 text-green-700' : ($meeting->status === 'cancelled' ? 'bg-red-100 text-red-700' : ($meeting->status === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700'))) }}">
                                @if($meeting->status === 'in_progress')
                                In Progress
                                @else
                                {{ ucfirst($meeting->status) }}
                                @endif
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('meetings.show', $meeting) }}" 
                                   class="px-3 py-1.5 text-xs font-medium text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100 transition-colors">
                                    View
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-calendar text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-medium text-gray-900">No meetings found</p>
                                <p class="text-sm text-gray-500 mt-1">Schedule your first meeting to get started</p>
                                <button wire:click="openModal" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100">
                                    <i class="fa-solid fa-plus"></i>
                                    Schedule Meeting
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($meetings->hasPages())
        <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">
                Showing {{ $meetings->firstItem() }} to {{ $meetings->lastItem() }} of {{ $meetings->total() }}
            </p>
            {{ $meetings->links() }}
        </div>
        @endif
    </div>
    @endif

    <!-- Schedule Meeting Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-data>
        <div class="fixed inset-0 bg-black/50" wire:click.self="closeModal"></div>

        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-xl my-8 max-h-[90vh] overflow-y-auto">
            <form wire:submit="save">
                <div class="flex items-center justify-between p-5 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <h2 class="text-lg font-semibold text-gray-900">Schedule Meeting</h2>
                    <button type="button" wire:click="closeModal" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Title *</label>
                        <input type="text" wire:model="title"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                               placeholder="e.g., Team Standup"
                               required>
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                        <textarea wire:model="description"
                                  class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                                  rows="2"
                                  placeholder="Meeting description..."></textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Purpose</label>
                        <input type="text" wire:model="purpose"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                               placeholder="e.g., Weekly team sync">
                        @error('purpose') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Meeting Date *</label>
                        <input type="date" wire:model="meeting_date"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                               required>
                        @error('meeting_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Start Time *</label>
                            <input type="time" wire:model="start_time"
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                                   required>
                            @error('start_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">End Time *</label>
                            <input type="time" wire:model="end_time"
                                   class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                                   required>
                            @error('end_time') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Company *</label>
                            <select wire:model.live="selectedCompanyId"
                                    class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                                    required>
                                <option value="">Select a company</option>
                                @foreach($companies as $company)
                                <option value="{{ $company['id'] }}">{{ $company['name'] }}</option>
                                @endforeach
                            </select>
                            @error('selectedCompanyId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Host *</label>
                            <select wire:model="host_id"
                                    class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                                    required>
                                <option value="">Select a host</option>
                                @foreach($hosts as $host)
                                <option value="{{ $host->id }}">{{ $host->name }}</option>
                                @endforeach
                            </select>
                            @error('host_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Meeting Room</label>
                        <select wire:model="meeting_room_id"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all">
                            <option value="">Select a room</option>
                            @foreach($availableMeetingRooms as $room)
                            <option value="{{ $room->id }}">{{ $room->name }} (Capacity: {{ $room->capacity }})</option>
                            @endforeach
                        </select>
                        @error('meeting_room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Meeting Type</label>
                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="meeting_type" value="in_person"
                                       class="w-4 h-4 text-brand-600 border-gray-300 focus:ring-brand-500">
                                <span class="text-sm text-gray-700">In Person</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="meeting_type" value="virtual"
                                       class="w-4 h-4 text-brand-600 border-gray-300 focus:ring-brand-500">
                                <span class="text-sm text-gray-700">Virtual</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" wire:model="meeting_type" value="hybrid"
                                       class="w-4 h-4 text-brand-600 border-gray-300 focus:ring-brand-500">
                                <span class="text-sm text-gray-700">Hybrid</span>
                            </label>
                        </div>
                        @error('meeting_type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Meeting URL</label>
                        <input type="url" wire:model="meeting_url"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                               placeholder="https://meet.google.com/...">
                        @error('meeting_url') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <button type="button" wire:click="closeModal"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-xl hover:bg-brand-700 transition-colors shadow-sm">
                        Schedule Meeting
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>