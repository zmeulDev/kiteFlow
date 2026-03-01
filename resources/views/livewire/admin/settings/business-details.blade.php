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

    {{-- Manual Cleanup Card --}}
    <div class="settings-card settings-card--danger mt-8">
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
                <h3 class="settings-card-title">Data Retention & Manual Cleanup</h3>
                <p class="settings-card-description">Configure automatic data cleanup rules and run manual cleanup to delete outdated visits.</p>
            </div>
        </div>
        
        <div class="settings-card-body pb-0">
            <div class="mb-6 pb-6 border-b border-gray-100 dark:border-gray-800">
                <form wire:submit="save">
                    <div class="settings-form-field">
                        <label class="settings-form-label">Retention Period (Days) *</label>
                        <div class="flex items-start max-w-sm">
                            <input type="number" wire:model.live.debounce.500ms="retention_days" min="1" max="365" class="settings-form-input mr-3" placeholder="90">
                            <button type="submit" class="settings-btn settings-btn--outline whitespace-nowrap">
                                Save Rule
                            </button>
                        </div>
                        @error('retention_days') <p class="settings-form-error mt-1">{{ $message }}</p> @enderror
                        <p class="settings-form-hint mt-2">Visit records older than this will be automatically deleted going forward.</p>
                    </div>
                </form>
            </div>
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4">
                <div>
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">Estimated Cleanup Impact</h4>
                    <p class="text-xs text-gray-500 mb-3">Based on your current retention period of <strong>{{ $retention_days }} days</strong>:</p>
                    <ul class="text-sm space-y-2 mb-4">
                        <li class="flex items-center text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <strong>{{ $visitsToDeleteCount }}</strong>&nbsp;visit records to be deleted
                        </li>
                        <li class="flex items-center text-red-600 dark:text-red-400">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            <strong>{{ $visitorsToDeleteCount }}</strong>&nbsp;orphaned visitor profiles to be deleted
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="settings-card-footer border-t border-gray-100 dark:border-gray-800 pt-4 mt-2">
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
