<div class="w-full max-w-xl mx-auto kiosk-fade-in">
    <div class="card kiosk-card text-center">
        <div class="mb-6">
            <div class="w-14 h-14 mx-auto mb-4 icon-container icon-container--green rounded-2xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold mb-2">Scan with Your Phone</h2>
            <p class="text-secondary">Use your phone's camera to scan this QR code and fill in your details on your device.</p>
        </div>

        <div class="bg-surface p-6 rounded-2xl inline-block mb-6 shadow-lg border border-light">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}"
                alt="QR Code for Check-in" class="mx-auto">
        </div>

        <p class="text-sm text-muted mb-6">
            Or visit: <a href="{{ $qrCodeUrl }}" class="link link-primary break-all">{{ $qrCodeUrl }}</a>
        </p>

        <div wire:poll.3s="checkMobileCheckIn" class="p-4 bg-main rounded-xl">
            <div class="flex items-center justify-center gap-3 text-secondary">
                <div class="kiosk-waiting">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span class="font-medium">Waiting for mobile check-in</span>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-light">
            <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" class="link flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('mobile-checkin-complete', () => {
            window.location.href = '{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}?mode=success';
        });
    });
</script>