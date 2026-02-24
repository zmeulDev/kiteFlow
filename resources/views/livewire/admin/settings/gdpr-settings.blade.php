<x-slot name="title">GDPR Settings</x-slot>

<div class="settings-page">
    {{-- Header --}}
    <div class="settings-header">
        <div class="settings-header-content">
            <div class="settings-header-icon settings-header-icon--blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <div>
                <h1 class="settings-title">GDPR Settings</h1>
                <p class="settings-subtitle">Configure GDPR consent text displayed to visitors</p>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('message'))
    <div class="settings-flash settings-flash--success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    {{-- Form Card --}}
    <div class="settings-card">
        <form wire:submit="save" class="settings-form">
            <div class="settings-form-grid">
                <div class="settings-form-field settings-form-field--full">
                    <label class="settings-form-label">GDPR Consent Text *</label>
                    <textarea wire:model="default_gdpr_text" rows="6" class="settings-form-input settings-form-textarea" placeholder="Enter the GDPR consent text that visitors must agree to..."></textarea>
                    @error('default_gdpr_text') <p class="settings-form-error">{{ $message }}</p> @enderror
                    <p class="settings-form-hint">This text will be shown to all visitors before they can check in.</p>
                </div>

                <div class="settings-form-field settings-form-field--full">
                    <label class="settings-form-toggle">
                        <input type="checkbox" wire:model="require_gdpr_consent" class="settings-form-checkbox">
                        <span class="settings-form-toggle-label">
                            <span class="settings-form-toggle-text">Require GDPR Consent</span>
                            <span class="settings-form-toggle-hint">Visitors must accept GDPR terms before checking in</span>
                        </span>
                    </label>
                </div>
            </div>

            <div class="settings-form-footer">
                <button type="submit" class="settings-btn settings-btn--primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
