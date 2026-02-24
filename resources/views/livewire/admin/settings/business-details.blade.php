<x-slot name="title">Business Details</x-slot>

<div class="settings-page">
    {{-- Header --}}
    <div class="settings-header">
        <div class="settings-header-content">
            <div class="settings-header-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <div>
                <h1 class="settings-title">Business Details</h1>
                <p class="settings-subtitle">Configure your company information displayed on kiosks and reports</p>
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
                <div class="settings-form-field">
                    <label class="settings-form-label">Company Name *</label>
                    <input type="text" wire:model="business_name" class="settings-form-input" placeholder="Your Company Ltd">
                    @error('business_name') <p class="settings-form-error">{{ $message }}</p> @enderror
                </div>

                <div class="settings-form-field">
                    <label class="settings-form-label">Email</label>
                    <input type="email" wire:model="business_email" class="settings-form-input" placeholder="contact@company.com">
                    @error('business_email') <p class="settings-form-error">{{ $message }}</p> @enderror
                </div>

                <div class="settings-form-field settings-form-field--full">
                    <label class="settings-form-label">Address</label>
                    <textarea wire:model="business_address" rows="2" class="settings-form-input settings-form-textarea" placeholder="Street address, city, postal code"></textarea>
                    @error('business_address') <p class="settings-form-error">{{ $message }}</p> @enderror
                </div>

                <div class="settings-form-field">
                    <label class="settings-form-label">Phone</label>
                    <input type="text" wire:model="business_phone" class="settings-form-input" placeholder="+44 20 1234 5678">
                    @error('business_phone') <p class="settings-form-error">{{ $message }}</p> @enderror
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
