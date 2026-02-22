<div class="space-y-4 lg:space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('meetings.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg lg:-ml-2">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">{{ $meeting->title }}</h1>
                <p class="text-sm text-gray-500">{{ $meeting->start_at->format('l, F j, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('meetings.edit', $meeting) }}" 
               class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fa-solid fa-pen"></i>
                <span>Edit</span>
            </a>
            @if($meeting->status === 'scheduled')
            <button wire:click="openCancelModal"
                    class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                <i class="fa-solid fa-xmark"></i>
                <span>Cancel</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Status Banner -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6">
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-10 h-10 rounded-lg {{ $meeting->status === 'scheduled' ? 'bg-blue-100' : ($meeting->status === 'completed' ? 'bg-green-100' : ($meeting->status === 'cancelled' ? 'bg-red-100' : 'bg-gray-100')) }}">
                    <i class="fa-solid {{ $meeting->status === 'scheduled' ? 'fa-clock text-blue-600' : ($meeting->status === 'completed' ? 'fa-check text-green-600' : ($meeting->status === 'cancelled' ? 'fa-xmark text-red-600' : 'fa-question text-gray-600')) }}"></i>
                </div>
                <div>
                    <span class="text-sm font-medium text-gray-900">
                        @if($meeting->status === 'in_progress')
                        In Progress
                        @else
                        {{ ucfirst($meeting->status) }}
                        @endif
                    </span>
                    <p class="text-xs text-gray-500">Duration: {{ $meeting->duration }}</p>
                </div>
            </div>
            <span class="px-3 py-1.5 text-xs font-medium rounded-full {{ $meeting->status === 'scheduled' ? 'bg-blue-100 text-blue-700' : ($meeting->status === 'completed' ? 'bg-green-100 text-green-700' : ($meeting->status === 'cancelled' ? 'bg-red-100 text-red-700' : ($meeting->status === 'in_progress' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700'))) }}">
                @if($meeting->status === 'in_progress')
                In Progress
                @else
                {{ ucfirst($meeting->status) }}
                @endif
            </span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6">
        <!-- Details Card -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200">
            <div class="p-4 lg:p-6 border-b border-gray-100">
                <h2 class="text-base lg:text-lg font-semibold text-gray-900">Meeting Details</h2>
            </div>
            
            <div class="p-4 lg:p-6 space-y-6">
                @if($meeting->description)
                <div>
                    <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider">Description</h3>
                    <p class="mt-2 text-sm text-gray-900">{{ $meeting->description }}</p>
                </div>
                @endif
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:gap-6">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2 text-gray-500 mb-2">
                            <i class="fa-solid fa-clock text-sm"></i>
                            <span class="text-xs font-medium uppercase tracking-wider">Time</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ $meeting->start_at->format('g:i A') }} - {{ $meeting->end_at->format('g:i A') }}</p>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2 text-gray-500 mb-2">
                            <i class="fa-solid fa-door-open text-sm"></i>
                            <span class="text-xs font-medium uppercase tracking-wider">Location</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ $meeting->meetingRoom?->name ?? 'No Room Assigned' }}</p>
                        @if($meeting->meetingRoom?->location)
                        <p class="text-xs text-gray-500 mt-0.5">{{ $meeting->meetingRoom->location }}</p>
                        @endif
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2 text-gray-500 mb-2">
                            <i class="fa-solid fa-user text-sm"></i>
                            <span class="text-xs font-medium uppercase tracking-wider">Host</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 text-xs font-medium">
                                {{ strtoupper(substr($meeting->host->name, 0, 1)) }}
                            </div>
                            <p class="text-sm font-medium text-gray-900">{{ $meeting->host->name }}</p>
                        </div>
                    </div>
                    
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2 text-gray-500 mb-2">
                            <i class="fa-solid fa-video text-sm"></i>
                            <span class="text-xs font-medium uppercase tracking-wider">Type</span>
                        </div>
                        <p class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $meeting->meeting_type)) }}</p>
                    </div>
                </div>
                
                @if($meeting->meeting_url)
                <div>
                    <h3 class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Meeting Link</h3>
                    <a href="{{ $meeting->meeting_url }}" target="_blank" 
                       class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-brand-600 bg-brand-50 rounded-lg hover:bg-brand-100 transition-colors">
                        <i class="fa-solid fa-link"></i>
                        <span>Join Meeting</span>
                        <i class="fa-solid fa-external-link text-xs"></i>
                    </a>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Attendees Card -->
        <div class="bg-white rounded-xl border border-gray-200">
            <div class="p-4 lg:p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-base lg:text-lg font-semibold text-gray-900">Attendees</h2>
                    <span class="text-xs text-gray-500">{{ $meeting->attendees->count() }} people</span>
                </div>
            </div>
            
            <div class="p-4 lg:p-6">
                @if($meeting->attendees->count() > 0)
                <div class="space-y-3">
                    @foreach($meeting->attendees as $attendee)
                    @if($attendee->attendee)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 text-xs font-medium">
                            {{ strtoupper(substr($attendee->attendee->name ?? $attendee->attendee->full_name ?? '?', 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $attendee->attendee->name ?? $attendee->attendee->full_name }}</p>
                        </div>
                        <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $attendee->status === 'accepted' ? 'bg-green-100 text-green-700' : ($attendee->status === 'declined' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600') }}">
                            {{ $attendee->status === 'accepted' ? 'Yes' : ($attendee->status === 'declined' ? 'No' : 'Pending') }}
                        </span>
                    </div>
                    @endif
                    @endforeach
                </div>
                @else
                <div class="py-8 text-center">
                    <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-users text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-500">No attendees yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cancel Confirmation Modal -->
    @if($showCancelModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-data>
        <div class="fixed inset-0 bg-black/50" wire:click.self="$set('showCancelModal', false)"></div>

        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl my-8">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 flex-shrink-0">
                        <i class="fa-solid fa-xmark text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Cancel Meeting</h3>
                        <p class="text-sm text-gray-500">This action cannot be undone.</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        Are you sure you want to cancel <strong>{{ $meeting->title }}</strong>? This will notify all attendees and the meeting will be marked as cancelled.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                <button type="button" wire:click="$set('showCancelModal', false)"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Keep Meeting
                </button>
                <button wire:click="confirmCancel"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors">
                    Cancel Meeting
                </button>
            </div>
        </div>
    </div>
    @endif
</div>