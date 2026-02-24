<x-slot name="title">Data Retention</x-slot>

<div class="settings-page">
    {{-- Header --}}
    <div class="settings-header">
        <div class="settings-header-content">
            <div class="settings-header-icon settings-header-icon--amber">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <div>
                <h1 class="settings-title">Data Retention</h1>
                <p class="settings-subtitle">Configure how long visitor data is retained</p>
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

    {{-- Retention Settings Card --}}
    <div class="settings-card">
        <form wire:submit="save" class="settings-form">
            <div class="settings-form-grid">
                <div class="settings-form-field">
                    <label class="settings-form-label">Retention Period (Days) *</label>
                    <input type="number" wire:model="retention_days" min="1" max="365" class="settings-form-input" placeholder="90">
                    @error('retention_days') <p class="settings-form-error">{{ $message }}</p> @enderror
                    <p class="settings-form-hint">Visit records older than this will be automatically deleted.</p>
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

    {{-- Manual Cleanup Card --}}
    <div class="settings-card settings-card--danger">
        <div class="settings-card-header">
            <div class="settings-card-icon settings-card-icon--danger">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    <line x1="10" y1="11" x2="10" y2="17"></line>
                    <line x1="14" y1="11" x2="14" y2="17"></line>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Manual Cleanup</h3>
                <p class="settings-card-description">Run a manual cleanup to delete visits older than the retention period. This action cannot be undone.</p>
            </div>
        </div>
        <div class="settings-card-footer">
            <button wire:click="showCleanupConfirm" class="settings-btn settings-btn--danger">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                </svg>
                Run Cleanup Now
            </button>
        </div>
    </div>
</div>
