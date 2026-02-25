<div class="w-full max-w-lg mx-auto kiosk-fade-in">
    @if(!$scheduledVisit)
    <div class="card kiosk-card">
        <!-- Back Link -->
        <div class="mb-8">
            <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" class="link flex items-center gap-2 text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Welcome
            </a>
        </div>

        <div class="text-center mb-8">
            <div class="w-16 h-16 mx-auto mb-4 icon-container icon-container--blue rounded-2xl">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-2">Have a Check-in Code?</h2>
            <p class="text-secondary">Enter the 6-character code from your visit invitation.</p>
        </div>

        <form wire:submit="lookupCode" class="space-y-6">
            <div>
                <input
                    type="text"
                    wire:model="checkInCode"
                    placeholder="ABC123"
                    class="input kiosk-input text-center text-3xl tracking-widest uppercase font-bold"
                    style="letter-spacing: 0.5em;"
                    maxlength="6"
                    autocomplete="off"
                    autofocus
                >
                @error('checkInCode')
                    <p class="text-sm mt-3 text-error text-center">{{ $message }}</p>
                @enderror
            </div>

            @if($codeNotFound)
                <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-center">
                    <div class="flex items-center justify-center gap-2 text-error font-semibold mb-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Code not found
                    </div>
                    <p class="text-sm text-secondary">Please check your invitation email and try again.</p>
                </div>
            @endif

            @if($alreadyCheckedIn)
                <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-center">
                    <div class="flex items-center justify-center gap-2 text-amber-600 font-semibold mb-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Already checked in
                    </div>
                    <p class="text-sm text-secondary">This visit has already been processed.</p>
                </div>
            @endif

            <button type="submit" class="btn kiosk-btn w-full">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Find My Visit
            </button>
        </form>
    </div>
    @endif

    @if($scheduledVisit)
    <div class="card kiosk-card">
        <!-- Found Visit -->
        <div class="text-center mb-8">
            <div class="kiosk-success-icon w-20 h-20 mx-auto mb-4 icon-container icon-container--green rounded-full flex items-center justify-center">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold mb-2">Visit Found!</h2>
            <p class="text-secondary">Welcome, {{ $scheduledVisit->visitor->first_name }}!</p>
        </div>

        <div class="detail-card mb-6">
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Host</span>
                <span class="font-medium text-primary">{{ $scheduledVisit->host?->name ?? $scheduledVisit->host_name }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Purpose</span>
                <span class="font-medium text-primary">{{ $scheduledVisit->purpose ?? 'Not specified' }}</span>
            </div>
            @if($scheduledVisit->scheduled_at)
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Scheduled</span>
                <span class="font-medium text-primary">{{ $scheduledVisit->scheduled_at->format('M j, Y \a\t g:i A') }}</span>
            </div>
            @endif
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Entrance</span>
                <span class="font-medium text-primary">{{ $scheduledVisit->entrance->name }}</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('kiosk.scheduled-check-in', ['entrance' => $entrance->kiosk_identifier, 'visit' => $scheduledVisit->id]) }}"
               class="btn btn-success kiosk-btn flex-1 justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Check In Now
            </a>
            <button wire:click="clearCode" class="btn btn-outline kiosk-btn justify-center">
                Different Code
            </button>
        </div>
    </div>
    @endif
</div>