<div class="w-full max-w-xl mx-auto text-center">
    <div class="card kiosk-card">
        <h2 class="text-2xl font-bold mb-4">Scan with Your Phone</h2>
        <p class="text-secondary mb-6">Use your phone's camera to scan this QR code and fill in your details on your device.</p>

        <div class="bg-white p-4 rounded-lg inline-block mb-6 shadow-sm">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($qrCodeUrl) }}"
                alt="QR Code for Check-in" class="mx-auto">
        </div>

        <p class="text-sm text-muted mb-6">
            Or visit: <a href="{{ $qrCodeUrl }}" class="link link-primary break-all">{{ $qrCodeUrl }}</a>
        </p>

        <div wire:poll.3s="checkMobileCheckIn" class="text-secondary">
            <div class="flex items-center justify-center gap-3">
                <div class="kiosk-waiting">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                <span>Waiting for mobile check-in</span>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" class="link">Cancel</a>
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