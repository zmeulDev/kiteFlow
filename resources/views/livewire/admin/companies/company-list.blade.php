<x-slot name="title">Companies</x-slot>

<div class="companies-page">
    {{-- Header with Stats --}}
    <div class="companies-header">
        <div class="companies-header-top">
            <div>
                <h1 class="companies-title">Companies</h1>
                <p class="companies-subtitle">Manage visiting companies and their contact details</p>
            </div>
            <div class="companies-stats">
                <div class="companies-stat">
                    <div class="companies-stat-icon companies-stat-icon--total">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <div class="companies-stat-content">
                        <div class="companies-stat-value">{{ $totalCompanies }}</div>
                        <div class="companies-stat-label">Total</div>
                    </div>
                </div>
                <div class="companies-stat">
                    <div class="companies-stat-icon companies-stat-icon--active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="companies-stat-content">
                        <div class="companies-stat-value">{{ $activeCompanies }}</div>
                        <div class="companies-stat-label">Active</div>
                    </div>
                </div>
                <div class="companies-stat">
                    <div class="companies-stat-icon companies-stat-icon--inactive">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line>
                        </svg>
                    </div>
                    <div class="companies-stat-content">
                        <div class="companies-stat-value">{{ $inactiveCompanies }}</div>
                        <div class="companies-stat-label">Inactive</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('message'))
    <div class="companies-flash companies-flash--success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="companies-flash companies-flash--error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Filter Bar --}}
    <div class="companies-filters">
        <div class="companies-filters-row">
            <div class="companies-filters-search">
                <svg class="companies-filters-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search companies by name or contact...">
            </div>
            <button wire:click="createCompany" class="companies-add-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Add Company</span>
            </button>
        </div>
    </div>

    {{-- Companies List --}}
    <div class="companies-list">
        @forelse($companies as $company)
        <div class="company-card {{ $company->is_active ? 'company-card--active' : 'company-card--inactive' }}" wire:key="company-{{ $company->id }}">
            <div class="company-main">
                <div class="company-avatar">
                    {{ collect(explode(' ', $company->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
                </div>
                <div class="company-info">
                    <div class="company-name">{{ $company->name }}</div>
                    <div class="company-meta">
                        @if($company->contact_person)
                        <span class="company-contact">{{ $company->contact_person }}</span>
                        @endif
                        @if($company->email)
                        <span class="company-email">{{ $company->email }}</span>
                        @endif
                    </div>
                    @if($company->address)
                    <div class="company-address">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                        {{ $company->address }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="company-details">
                <div class="company-detail">
                    <div class="company-detail-label">Phone</div>
                    <div class="company-detail-value">{{ $company->phone ?? '—' }}</div>
                </div>
            </div>

            <div class="company-status">
                @if($company->is_active)
                <span class="company-status-badge company-status-badge--active">
                    <span class="company-status-dot"></span>
                    Active
                </span>
                @else
                <span class="company-status-badge company-status-badge--inactive">
                    Inactive
                </span>
                @endif
            </div>

            <div class="company-actions">
                <button wire:click="editCompany({{ $company->id }})" class="company-action-btn company-action-btn--edit">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
                <button wire:click="showDeleteConfirm({{ $company->id }}, '{{ $company->name }}')" class="company-action-btn company-action-btn--delete">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </button>
            </div>
        </div>
        @empty
        <div class="companies-empty">
            <div class="companies-empty-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <p class="companies-empty-text">{{ $search ? 'No companies found matching your search.' : 'No companies yet. Add your first company to get started.' }}</p>
        </div>
        @endforelse

        @if($companies->hasPages())
        <div class="companies-pagination">
            {{ $companies->links() }}
        </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="companies-modal-overlay" wire:click.self="$set('showModal', false)">
        <div class="companies-modal">
            <div class="companies-modal-header">
                <div class="companies-modal-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <div>
                    <h3 class="companies-modal-title">{{ $editingCompanyId ? 'Edit Company' : 'New Company' }}</h3>
                    <p class="companies-modal-subtitle">{{ $editingCompanyId ? 'Update company information' : 'Add a new visiting company' }}</p>
                </div>
            </div>

            <form wire:submit="save" class="companies-modal-body">
                <div class="companies-form-grid">
                    <div class="companies-form-field companies-form-field--full">
                        <label class="companies-form-label">Company Name *</label>
                        <input type="text" wire:model="name" class="companies-form-input" placeholder="Enter company name">
                        @error('name') <p class="companies-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="companies-form-field companies-form-field--full">
                        <label class="companies-form-label">Address</label>
                        <textarea wire:model="address" class="companies-form-input companies-form-textarea" rows="2" placeholder="Street address, city, postal code"></textarea>
                    </div>

                    <div class="companies-form-field">
                        <label class="companies-form-label">Phone</label>
                        <input type="text" wire:model="phone" class="companies-form-input" placeholder="+44 20 1234 5678">
                    </div>

                    <div class="companies-form-field">
                        <label class="companies-form-label">Email</label>
                        <input type="email" wire:model="email" class="companies-form-input" placeholder="contact@company.com">
                        @error('email') <p class="companies-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="companies-form-field companies-form-field--full">
                        <label class="companies-form-label">Contact Person</label>
                        <input type="text" wire:model="contact_person" class="companies-form-input" placeholder="Primary contact name">
                    </div>

                    <div class="companies-form-field companies-form-field--full">
                        <label class="companies-form-toggle">
                            <input type="checkbox" wire:model="is_active" class="companies-form-checkbox">
                            <span class="companies-form-toggle-label">
                                <span class="companies-form-toggle-text">Active Status</span>
                                <span class="companies-form-toggle-hint">Company will be available for selection in visits</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="companies-modal-footer">
                    <button type="button" wire:click="$set('showModal', false)" class="companies-btn companies-btn--secondary">Cancel</button>
                    <button type="submit" class="companies-btn companies-btn--primary">
                        {{ $editingCompanyId ? 'Update Company' : 'Create Company' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
