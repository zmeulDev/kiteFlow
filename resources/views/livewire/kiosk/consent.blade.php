<div class="w-full max-w-2xl mx-auto">
    <div class="card kiosk-card">
        <h2 class="text-2xl font-bold mb-6 text-center">Consent Required</h2>

        <div class="space-y-5">
            <div class="kiosk-consent-card">
                <h3 class="font-semibold mb-3">GDPR Consent</h3>
                <p class="text-secondary mb-4">{{ $gdprText }}</p>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="gdpr_consent" class="checkbox kiosk-checkbox">
                    <span class="ml-3">I agree to the GDPR terms *</span>
                </label>
                @error('gdpr_consent')
                <p class="text-sm mt-2" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>

            @if($showNda)
            <div class="kiosk-consent-card">
                <h3 class="font-semibold mb-3">NDA Consent</h3>
                <p class="text-secondary mb-4">{{ $ndaText }}</p>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="nda_consent" class="checkbox kiosk-checkbox">
                    <span class="ml-3">I agree to the NDA terms *</span>
                </label>
                @error('nda_consent')
                <p class="text-sm mt-2" style="color: #DC2626;">{{ $message }}</p>
                @enderror
            </div>
            @endif
        </div>

        <div class="flex justify-between mt-8">
            <button wire:click="$dispatch('consent-cancelled')" class="btn btn-outline kiosk-btn">Back</button>
            <button wire:click="submit" class="btn kiosk-btn">Continue</button>
        </div>
    </div>
</div>