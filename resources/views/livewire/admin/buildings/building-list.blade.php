<x-slot name="title">Buildings</x-slot>

<div class="buildings-page">
    {{-- Header with Stats --}}
    <div class="buildings-header">
        <div class="buildings-header-top">
            <div>
                <h1 class="buildings-title">Buildings</h1>
                <p class="buildings-subtitle">Manage locations and their kiosk entrances</p>
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
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
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
            @can('manageBuildings', App\Models\User::class)
            <button wire:click="createBuilding" class="buildings-add-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Add Building</span>
            </button>
            @endcan
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

                @can('viewBuildings', App\Models\User::class)
                <div class="building-actions">
                    <a href="{{ route('admin.buildings.edit', $building->id) }}" class="building-action-btn building-action-btn--edit" title="{{ auth()->user()->can('manageBuildings', App\Models\User::class) ? 'Manage Building' : 'View Details' }}">
                        @if(auth()->user()->can('manageBuildings', App\Models\User::class))
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                        </svg>
                        @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                        @endif
                    </a>
                </div>
                @endcan
            </div>

            {{-- Entrances & Spaces Summary --}}
            <div class="building-entrances">
                <div class="entrances-grid" style="grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));">
                    <div class="entrance-card entrance-card--{{ $building->entrances->count() > 0 ? 'active' : 'inactive' }}" style="padding: 0.875rem 1rem;">
                        <div style="font-size: 0.6875rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.25rem;">Entrances</div>
                        <div style="font-weight: 800; font-size: 1.25rem; color: var(--text-primary);">{{ $building->entrances->count() }}</div>
                    </div>
                    <div class="entrance-card entrance-card--{{ $building->spaces->count() > 0 ? 'active' : 'inactive' }}" style="padding: 0.875rem 1rem;">
                        <div style="font-size: 0.6875rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.25rem;">Meeting Rooms</div>
                        <div style="font-weight: 800; font-size: 1.25rem; color: var(--text-primary);">{{ $building->spaces->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="buildings-empty">
            <div class="buildings-empty-icon" style="width: 80px; height: 80px; border-radius: 20px; background: linear-gradient(135deg, rgba(255, 75, 75, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--primary);">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--text-primary); margin: 0 0 0.5rem;">{{ $search ? 'No matches found' : 'No buildings yet' }}</h3>
            <p class="buildings-empty-text">{{ $search ? 'Try adjusting your search terms.' : 'Add your first building to start managing kiosk entrances.' }}</p>
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

</div>