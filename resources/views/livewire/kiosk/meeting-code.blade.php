<div class="w-full max-w-2xl mx-auto text-center">
    <div class="card kiosk-card">
        <h2 class="text-2xl font-bold mb-4 text-gray-800">Enter Meeting Code</h2>
        <p class="text-secondary mb-6">Enter the meeting code from your scheduled meeting invite to check in directly.</p>

        <div class="mb-6 flex items-center gap-3">
            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4-3-3 4 5 5.5a-5-3-4.35c-1.2l-5-3-2.6-0.35-4.8 3.23.24 28 16">
 accept any video conferencing meeting code.
            </svg>
>
            <span class="text-sm font-medium text-gray-600">e.g., Google Meet or Microsoft Teams</span>
        </div>

        <div class="mt-4 text-center">
            <button wire:click="submitMeetingCode" type="submit" class="btn kiosk-btn" style="background-color: {{ $primaryColor ?? '#3b82f6' }}">
                        Submit
                    </button>
                    <button wire:click="startManualCheckIn" class="btn btn-outline kiosk-btn">Manual Entry</button>
                    <button wire:click="showQrCode" class="btn btn-outline kiosk-btn">Scan QR Code</button>
                    <button wire:click="checkMobileCheckIn" class="btn btn-outline kiosk-btn">Cancel</button>
                <div class="mt-4 text-center">
                <p class="text-xs text-muted">Need help? <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}">Back to Main</a>
p>
div>
>
</div>