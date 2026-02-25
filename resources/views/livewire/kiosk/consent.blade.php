<div class="w-full max-w-2xl mx-auto kiosk-fade-in">
    <div class="card kiosk-card">
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-4 icon-container icon-container--green rounded-2xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold mb-2">Consent Required</h2>
            <p class="text-secondary">Please review and agree to the terms below</p>
        </div>

        <div class="space-y-5">
            <div class="kiosk-consent-card">
                <div class="flex items-start gap-4">
                    <input type="checkbox" wire:model="gdpr_consent" class="checkbox kiosk-checkbox mt-1 flex-shrink-0" id="gdpr_consent">
                    <div>
                        <label for="gdpr_consent" class="font-semibold text-primary cursor-pointer">GDPR Consent <span class="text-error">*</span></label>
                        <p class="text-secondary mt-1 text-sm leading-relaxed">{{ $gdprText }}</p>
                    </div>
                </div>
                @error('gdpr_consent')
                <p class="text-sm mt-3 text-error ml-10">{{ $message }}</p>
                @enderror
            </div>

            @if($showNda)
            <div class="kiosk-consent-card">
                <div class="flex items-start gap-4">
                    <input type="checkbox" wire:model="nda_consent" class="checkbox kiosk-checkbox mt-1 flex-shrink-0" id="nda_consent">
                    <div>
                        <label for="nda_consent" class="font-semibold text-primary cursor-pointer">NDA Consent <span class="text-error">*</span></label>
                        <p class="text-secondary mt-1 text-sm leading-relaxed">{{ $ndaText }}</p>
                    </div>
                </div>
                @error('nda_consent')
                <p class="text-sm mt-3 text-error ml-10">{{ $message }}</p>
                @enderror
            </div>
            @endif
        </div>

        <div class="flex justify-between mt-8 pt-6 border-t border-light">
            <button wire:click="$dispatch('consent-cancelled')" class="btn btn-outline kiosk-btn">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </button>
            <button wire:click="submit" class="btn kiosk-btn">
                Continue
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </div>
</div>