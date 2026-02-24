<div class="w-full max-w-3xl mx-auto text-center">
    <!-- Welcome Header -->
    <div class="mb-10 kiosk-fade-in">
        @if($buildingName)
            <p class="text-lg text-secondary mb-2">Welcome to</p>
            <h1 class="text-4xl font-bold mb-3" style="color: {{ $primaryColor ?? 'var(--primary)' }}">
                {{ $buildingName }}
            </h1>
        @else
            <h1 class="text-4xl font-bold mb-3" style="color: {{ $primaryColor ?? 'var(--primary)' }}">
                {{ $welcomeMessage }}
            </h1>
        @endif
        <p class="text-lg text-secondary">
            Please choose how you would like to check in
        </p>
    </div>

    <!-- Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <button wire:click="startManualCheckIn" class="card kiosk-action-card flex flex-col items-center justify-center kiosk-fade-in kiosk-stagger-1">
            <div class="w-16 h-16 mb-4 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2">Manual Entry</h2>
            <p class="text-muted">Fill in your details on this kiosk</p>
        </button>

        <button wire:click="showQrCode" class="card kiosk-action-card flex flex-col items-center justify-center kiosk-fade-in kiosk-stagger-2">
            <div class="w-16 h-16 mb-4 rounded-full bg-green-100 flex items-center justify-center">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                </svg>
            </div>
            <h2 class="text-xl font-semibold mb-2">Scan QR Code</h2>
            <p class="text-muted">Use your phone to check in</p>
        </button>
    </div>

    <!-- Checkout Link -->
    <div class="kiosk-fade-in kiosk-stagger-3">
        <a href="{{ route('kiosk.checkout', $entrance->kiosk_identifier) }}" class="link">
            Need to check out instead?
        </a>
    </div>
</div>