<x-slot name="title">Buildings</x-slot>

<div class="buildings-page">
    {{-- Header with Stats --}}
    <div class="buildings-header">
        <div class="buildings-header-top">
            <div>
                <h1 class="buildings-title">Buildings & Entrances</h1>
                <p class="buildings-subtitle">Manage buildings and their kiosk entrances</p>
            </div>
            <div class="buildings-stats">
                <div class="buildings-stat">
                    <div class="buildings-stat-icon buildings-stat-icon--buildings">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <div class="buildings-stat-content">
                        <div class="buildings-stat-value">{{ $totalBuildings }}</div>
                        <div class="buildings-stat-label">Buildings</div>
                    </div>
                </div>
                <div class="buildings-stat">
                    <div class="buildings-stat-icon buildings-stat-icon--entrances">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3H6a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3V6a3 3 0 0 0-3-3 3 3 0 0 0-3 3 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 3 3 0 0 0-3-3z"></path>
                        </svg>
                    </div>
                    <div class="buildings-stat-content">
                        <div class="buildings-stat-value">{{ $totalEntrances }}</div>
                        <div class="buildings-stat-label">Entrances</div>
                    </div>
                </div>
                <div class="buildings-stat">
                    <div class="buildings-stat-icon buildings-stat-icon--active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="buildings-stat-content">
                        <div class="buildings-stat-value">{{ $activeEntrances }}</div>
                        <div class="buildings-stat-label">Active</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('message'))
    <div class="buildings-flash buildings-flash--success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="buildings-flash buildings-flash--error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Filter Bar --}}
    <div class="buildings-filters">
        <div class="buildings-filters-row">
            <div class="buildings-filters-search">
                <svg class="buildings-filters-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search buildings by name or address...">
            </div>
            <button wire:click="createBuilding" class="buildings-add-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Add Building</span>
            </button>
        </div>
    </div>

    {{-- Buildings List --}}
    <div class="buildings-list">
        @forelse($buildings as $building)
        <div class="building-card {{ $building->is_active ? 'building-card--active' : 'building-card--inactive' }}" wire:key="building-{{ $building->id }}">
            <div class="building-header">
                <div class="building-main">
                    <div class="building-avatar">
                        {{ collect(explode(' ', $building->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
                    </div>
                    <div class="building-info">
                        <div class="building-name">{{ $building->name }}</div>
                        @if($building->address)
                        <div class="building-address">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            {{ $building->address }}
                        </div>
                        @endif
                    </div>
                </div>

                <div class="building-status">
                    @if($building->is_active)
                    <span class="building-status-badge building-status-badge--active">
                        <span class="building-status-dot"></span>
                        Active
                    </span>
                    @else
                    <span class="building-status-badge building-status-badge--inactive">
                        Inactive
                    </span>
                    @endif
                </div>

                <div class="building-actions">
                    <button wire:click="createEntrance({{ $building->id }})" class="building-action-btn building-action-btn--add" title="Add Entrance">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 3a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3H6a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3V6a3 3 0 0 0-3-3 3 3 0 0 0-3 3 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 3 3 0 0 0-3-3z"></path>
                        </svg>
                    </button>
                    <button wire:click="editBuilding({{ $building->id }})" class="building-action-btn building-action-btn--edit" title="Edit Building">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                    </button>
                    <button wire:click="showDeleteBuildingConfirm({{ $building->id }}, '{{ $building->name }}')" class="building-action-btn building-action-btn--delete" title="Delete Building">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"></polyline>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                            <line x1="10" y1="11" x2="10" y2="17"></line>
                            <line x1="14" y1="11" x2="14" y2="17"></line>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Entrances --}}
            <div class="building-entrances">
                @if($building->entrances->count() > 0)
                <div class="entrances-grid">
                    @foreach($building->entrances as $entrance)
                    <div class="entrance-card {{ $entrance->is_active ? 'entrance-card--active' : 'entrance-card--inactive' }}" wire:key="entrance-{{ $entrance->id }}">
                        <div class="entrance-header">
                            <div class="entrance-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="9" y1="3" x2="9" y2="21"></line>
                                </svg>
                            </div>
                            <div class="entrance-info">
                                <div class="entrance-name">{{ $entrance->name }}</div>
                                <div class="entrance-id">{{ $entrance->kiosk_identifier }}</div>
                            </div>
                            @if($entrance->is_active)
                            <span class="entrance-badge entrance-badge--active">Active</span>
                            @else
                            <span class="entrance-badge entrance-badge--inactive">Inactive</span>
                            @endif
                        </div>
                        <div class="entrance-actions">
                            <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" target="_blank" class="entrance-link">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                    <polyline points="15 3 21 3 21 9"></polyline>
                                    <line x1="10" y1="14" x2="21" y2="3"></line>
                                </svg>
                                Kiosk
                            </a>
                            <a href="{{ route('kiosk.checkout', $entrance->kiosk_identifier) }}" target="_blank" class="entrance-link">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                Checkout
                            </a>
                            <button wire:click="editEntrance({{ $entrance->id }})" class="entrance-btn entrance-btn--edit">Edit</button>
                            <button wire:click="showDeleteEntranceConfirm({{ $entrance->id }}, '{{ $entrance->name }}')" class="entrance-btn entrance-btn--delete">Delete</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="entrances-empty">
                    <p>No entrances configured</p>
                    <button wire:click="createEntrance({{ $building->id }})" class="entrances-add-btn">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        Add Entrance
                    </button>
                </div>
                @endif
            </div>
        </div>
        @empty
        <div class="buildings-empty">
            <div class="buildings-empty-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <p class="buildings-empty-text">{{ $search ? 'No buildings found matching your search.' : 'No buildings yet. Add your first building to get started.' }}</p>
        </div>
        @endforelse

        @if($buildings->hasPages())
        <div class="buildings-pagination">
            {{ $buildings->links() }}
        </div>
        @endif
    </div>

    {{-- Building Modal --}}
    @if($showBuildingModal)
    <div class="buildings-modal-overlay" wire:click.self="$set('showBuildingModal', false)">
        <div class="buildings-modal">
            <div class="buildings-modal-header">
                <div class="buildings-modal-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <div>
                    <h3 class="buildings-modal-title">{{ $editingBuildingId ? 'Edit Building' : 'New Building' }}</h3>
                    <p class="buildings-modal-subtitle">{{ $editingBuildingId ? 'Update building information' : 'Add a new building location' }}</p>
                </div>
            </div>

            <form wire:submit="saveBuilding" class="buildings-modal-body">
                <div class="buildings-form-grid">
                    <div class="buildings-form-field buildings-form-field--full">
                        <label class="buildings-form-label">Building Name *</label>
                        <input type="text" wire:model="building_name" class="buildings-form-input" placeholder="Enter building name">
                        @error('building_name') <p class="buildings-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="buildings-form-field buildings-form-field--full">
                        <label class="buildings-form-label">Address</label>
                        <textarea wire:model="building_address" class="buildings-form-input buildings-form-textarea" rows="2" placeholder="Street address, city, postal code"></textarea>
                    </div>

                    <div class="buildings-form-field buildings-form-field--full">
                        <label class="buildings-form-toggle">
                            <input type="checkbox" wire:model="building_is_active" class="buildings-form-checkbox">
                            <span class="buildings-form-toggle-label">
                                <span class="buildings-form-toggle-text">Active Status</span>
                                <span class="buildings-form-toggle-hint">Building and its entrances will be available</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="buildings-modal-footer">
                    <button type="button" wire:click="$set('showBuildingModal', false)" class="buildings-btn buildings-btn--secondary">Cancel</button>
                    <button type="submit" class="buildings-btn buildings-btn--primary">
                        {{ $editingBuildingId ? 'Update Building' : 'Create Building' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Entrance Modal --}}
    @if($showEntranceModal)
    <div class="buildings-modal-overlay" wire:click.self="$set('showEntranceModal', false)">
        <div class="buildings-modal">
            <div class="buildings-modal-header buildings-modal-header--entrance">
                <div class="buildings-modal-icon buildings-modal-icon--entrance">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="9" y1="3" x2="9" y2="21"></line>
                    </svg>
                </div>
                <div>
                    <h3 class="buildings-modal-title">{{ $editingEntranceId ? 'Edit Entrance' : 'New Entrance' }}</h3>
                    <p class="buildings-modal-subtitle">{{ $editingEntranceId ? 'Update entrance settings' : 'Add a kiosk entrance point' }}</p>
                </div>
            </div>

            <form wire:submit="saveEntrance" class="buildings-modal-body">
                <div class="buildings-form-grid">
                    <div class="buildings-form-field buildings-form-field--full">
                        <label class="buildings-form-label">Entrance Name *</label>
                        <input type="text" wire:model="entrance_name" class="buildings-form-input" placeholder="e.g., Main Lobby, Side Entrance">
                        @error('entrance_name') <p class="buildings-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="buildings-form-field buildings-form-field--full">
                        <label class="buildings-form-label">Kiosk Identifier *</label>
                        <input type="text" wire:model="entrance_kiosk_identifier" class="buildings-form-input" placeholder="unique-identifier">
                        @error('entrance_kiosk_identifier') <p class="buildings-form-error">{{ $message }}</p> @enderror
                        <p class="buildings-form-hint">Unique identifier used in kiosk URLs</p>
                    </div>

                    <div class="buildings-form-field buildings-form-field--full">
                        <label class="buildings-form-toggle">
                            <input type="checkbox" wire:model="entrance_is_active" class="buildings-form-checkbox">
                            <span class="buildings-form-toggle-label">
                                <span class="buildings-form-toggle-text">Active Status</span>
                                <span class="buildings-form-toggle-hint">Entrance will be available for check-ins</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="buildings-modal-footer">
                    <button type="button" wire:click="$set('showEntranceModal', false)" class="buildings-btn buildings-btn--secondary">Cancel</button>
                    <button type="submit" class="buildings-btn buildings-btn--primary">
                        {{ $editingEntranceId ? 'Update Entrance' : 'Create Entrance' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
