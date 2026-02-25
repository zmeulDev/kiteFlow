<div class="w-full max-w-2xl mx-auto kiosk-fade-in">
    <div class="card kiosk-card text-center">
        <div class="kiosk-success-icon rounded-full icon-container icon-container--green flex items-center justify-center">
            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        <h2 class="text-3xl font-bold mb-3">{{ $message }}</h2>

        <div class="detail-card mb-8">
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Visitor</span>
                <span class="font-medium text-primary">{{ $visit->visitor->full_name }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Company</span>
                <span class="font-medium text-primary">{{ $visit->visitor->company->name ?? 'N/A' }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Host</span>
                <span class="font-medium text-primary">{{ $visit->host?->name ?? $visit->host_name }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Purpose</span>
                <span class="font-medium text-primary">{{ $visit->purpose ?? 'N/A' }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Location</span>
                <span class="font-medium text-primary">{{ $visit->entrance->name }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Building</span>
                <span class="font-medium text-primary">{{ $visit->entrance->building->name }}</span>
            </div>
            <div class="kiosk-detail-row">
                <span class="font-semibold text-secondary">Check-in Time</span>
                <span class="font-medium text-primary">{{ $visit->check_in_at->format('F j, Y g:i A') }}</span>
            </div>
        </div>

        <p class="text-muted mb-6">Please remember to check out when leaving.</p>

        <button wire:click="done" class="btn kiosk-btn">Done</button>
    </div>
</div>