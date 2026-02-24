<x-slot name="title">NDA Settings</x-slot>

<div class="settings-page">
    {{-- Header --}}
    <div class="settings-header">
        <div class="settings-header-content">
            <div class="settings-header-icon settings-header-icon--purple">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <div>
                <h1 class="settings-title">NDA Settings</h1>
                <p class="settings-subtitle">Configure Non-Disclosure Agreement text for visitors</p>
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
                    <label class="settings-form-toggle">
                        <input type="checkbox" wire:model="show_nda_globally" class="settings-form-checkbox">
                        <span class="settings-form-toggle-label">
                            <span class="settings-form-toggle-text">Show NDA on All Kiosks</span>
                            <span class="settings-form-toggle-hint">Display NDA consent form on every kiosk entrance</span>
                        </span>
                    </label>
                </div>

                <div class="settings-form-field settings-form-field--full">
                    <label class="settings-form-label">NDA Consent Text</label>
                    <textarea wire:model="default_nda_text" rows="6" class="settings-form-input settings-form-textarea" placeholder="Enter the NDA consent text that visitors must agree to..."></textarea>
                    @error('default_nda_text') <p class="settings-form-error">{{ $message }}</p> @enderror
                    <p class="settings-form-hint">This text will be shown to visitors when NDA is enabled.</p>
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
