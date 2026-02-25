<div class="welcome-container">
    <!-- Decorative background elements -->
    <div class="welcome-decoration welcome-decoration--top"></div>
    <div class="welcome-decoration welcome-decoration--bottom"></div>

    <!-- Welcome Header -->
    <div class="welcome-hero kiosk-fade-in">
        @if($buildingName)
            <div class="welcome-hero__label">Welcome to</div>
            <h1 class="welcome-hero__title" style="color: {{ $primaryColor ?? 'var(--primary)' }}">
                {{ $buildingName }}
            </h1>
        @else
            <h1 class="welcome-hero__title" style="color: {{ $primaryColor ?? 'var(--primary)' }}">
                {{ $welcomeMessage }}
            </h1>
        @endif
        <p class="welcome-hero__subtitle">
            Please choose how you would like to check in
        </p>
    </div>

    <!-- Action Cards -->
    <div class="welcome-actions">
        <button wire:click="startManualCheckIn" class="welcome-card kiosk-fade-in kiosk-stagger-1">
            <div class="welcome-card__number">1</div>
            <div class="welcome-card__icon welcome-card__icon--coral">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <h2 class="welcome-card__title">Manual Entry</h2>
            <p class="welcome-card__desc">Fill in your details on this kiosk</p>
        </button>

        <button wire:click="checkInWithCode" class="welcome-card kiosk-fade-in kiosk-stagger-2">
            <div class="welcome-card__number">2</div>
            <div class="welcome-card__icon welcome-card__icon--blue">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                </svg>
            </div>
            <h2 class="welcome-card__title">I Have a Code</h2>
            <p class="welcome-card__desc">Enter your check-in code</p>
        </button>

        <button wire:click="showQrCode" class="welcome-card kiosk-fade-in kiosk-stagger-3">
            <div class="welcome-card__number">3</div>
            <div class="welcome-card__icon welcome-card__icon--green">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
            </div>
            <h2 class="welcome-card__title">Scan QR Code</h2>
            <p class="welcome-card__desc">Use your phone to check in</p>
        </button>
    </div>

    <!-- Checkout Link -->
    <div class="welcome-checkout kiosk-fade-in kiosk-stagger-4">
        <a href="{{ route('kiosk.checkout', $entrance->kiosk_identifier) }}" class="welcome-checkout__link">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
            Need to check out instead?
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
</div>
