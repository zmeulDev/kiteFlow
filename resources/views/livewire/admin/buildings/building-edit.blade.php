<x-slot name="title">Manage Building</x-slot>

<div class="companies-page">
    {{-- Header --}}
    <div class="companies-header">
        <div class="companies-header-top">
            <div>
                <div class="company-edit-breadcrumb">
                    <a href="{{ route('admin.buildings') }}" class="company-edit-back">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Back to Buildings
                    </a>
                </div>
                <h1 class="companies-title">{{ $building->name }}</h1>
                <p class="companies-subtitle">Manage entrances and meeting rooms</p>
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

    {{-- Summary Cards --}}
    <div class="company-summary-grid">
        {{-- Entrances Card --}}
        <div class="company-summary-card company-edit-card">
            <div class="company-summary-card-header">
                <div class="company-summary-card-icon company-summary-card-icon--blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                </div>
                <h3 class="company-summary-card-title">Entrances</h3>
            </div>
            <div class="company-summary-card-body">
                <div class="company-summary-item">
                    <span class="company-summary-label">Total</span>
                    <span class="company-summary-value">{{ $entrances->count() }}</span>
                </div>
                <div class="company-summary-item">
                    <span class="company-summary-label">Active</span>
                    <span class="company-summary-value">{{ $entrances->where('is_active', true)->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Meeting Rooms Card --}}
        <div class="company-summary-card company-edit-card">
            <div class="company-summary-card-header">
                <div class="company-summary-card-icon company-summary-card-icon--amber">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                </div>
                <h3 class="company-summary-card-title">Meeting Rooms</h3>
            </div>
            <div class="company-summary-card-body">
                <div class="company-summary-item">
                    <span class="company-summary-label">Total</span>
                    <span class="company-summary-value">{{ $spaces->count() }}</span>
                </div>
                <div class="company-summary-item">
                    <span class="company-summary-label">Active</span>
                    <span class="company-summary-value">{{ $spaces->where('is_active', true)->count() }}</span>
                </div>
            </div>
        </div>

        {{-- Building Status Card --}}
        <div class="company-summary-card company-edit-card">
            <div class="company-summary-card-header">
                <div class="company-summary-card-icon company-summary-card-icon--green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                </div>
                <h3 class="company-summary-card-title">Building Status</h3>
            </div>
            <div class="company-summary-card-body">
                <div class="company-summary-item">
                    <span class="company-summary-label">Status</span>
                    <span class="company-summary-value">
                        @if($building->is_active)
                            <span class="company-summary-badge company-summary-badge--success">Active</span>
                        @else
                            <span class="company-summary-badge company-summary-badge--danger">Inactive</span>
                        @endif
                    </span>
                </div>
                @if($building->address)
                <div class="company-summary-item">
                    <span class="company-summary-label">Address</span>
                    <span class="company-summary-value">{{ $building->address }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="company-edit-grid">
        {{-- Main Content Column --}}
        <div class="company-edit-main">
            {{-- Entrances Card --}}
            <div class="company-edit-card company-edit-card--main">
                <div class="company-edit-card-header">
                    <div class="company-edit-card-icon company-edit-card-icon--blue">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 class="company-edit-card-title">Entrances</h3>
                        <p class="company-edit-card-subtitle">{{ $entrances->count() }} entrance{{ $entrances->count() === 1 ? '' : 's' }}</p>
                    </div>
                </div>
                <div class="company-edit-card-body">
                    @if($entrances->count() > 0)
                    <div class="company-users-list">
                        @foreach($entrances as $entrance)
                        <div class="company-user-item" wire:key="entrance-{{ $entrance->id }}">
                            <div class="company-user-info">
                                <div class="company-user-name">
                                    {{ $entrance->name }}
                                    @if($entrance->is_active)
                                    <span class="company-user-status company-user-status--active">Active</span>
                                    @else
                                    <span class="company-user-status company-user-status--inactive">Inactive</span>
                                    @endif
                                </div>
                                <div class="company-user-email">
                                    <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" target="_blank" style="color: inherit; text-decoration: none;">
                                        /kiosk/{{ $entrance->kiosk_identifier }}
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-left: 2px;">
                                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                            <polyline points="15 3 21 3 21 9"></polyline>
                                            <line x1="10" y1="14" x2="21" y2="3"></line>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            @can('manageBuildings', App\Models\User::class)
                            <div class="company-user-meta">
                                <button type="button" wire:click="editEntrance({{ $entrance->id }})" class="company-user-edit-btn">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button type="button" wire:click="showDeleteEntranceConfirm({{ $entrance->id }}, '{{ addslashes($entrance->name) }}')" class="company-user-edit-btn" style="color: #DC2626;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="company-users-empty">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        <p class="company-users-empty-text">No entrances yet. Create an entrance to set up a kiosk.</p>
                    </div>
                    @endif

                    @can('manageBuildings', App\Models\User::class)
                    <button type="button" wire:click="createEntrance" class="company-add-user-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Add Entrance</span>
                    </button>
                    @endcan
                </div>
            </div>

            {{-- Meeting Rooms Card --}}
            <div class="company-edit-card company-edit-card--main">
                <div class="company-edit-card-header">
                    <div class="company-edit-card-icon company-edit-card-icon--amber">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 class="company-edit-card-title">Meeting Rooms</h3>
                        <p class="company-edit-card-subtitle">{{ $spaces->count() }} space{{ $spaces->count() === 1 ? '' : 's' }}</p>
                    </div>
                </div>
                <div class="company-edit-card-body">
                    @if($spaces->count() > 0)
                    <div class="company-users-list">
                        @foreach($spaces as $space)
                        <div class="company-user-item" wire:key="space-{{ $space->id }}">
                            <div class="company-user-info">
                                <div class="company-user-name">
                                    {{ $space->name }}
                                    @if($space->capacity)
                                    <span class="company-user-status" style="background: rgba(107, 114, 128, 0.1); color: #6B7280; margin-left: 0.25rem;">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; margin-right:2px; margin-bottom: 2px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>{{ $space->capacity }}
                                    </span>
                                    @endif
                                    @if($space->is_active)
                                    <span class="company-user-status company-user-status--active">Active</span>
                                    @else
                                    <span class="company-user-status company-user-status--inactive">Inactive</span>
                                    @endif
                                </div>
                                @if($space->amenities)
                                <div class="company-user-meta" style="margin-top: 0.375rem;">
                                    @foreach($space->amenities as $amenity)
                                    <span class="badge badge-purple" style="font-size: 0.625rem;">{{ $amenity }}</span>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('manage_buildings'))
                            <div class="company-user-meta">
                                <button type="button" wire:click="editSpace({{ $space->id }})" class="company-user-edit-btn">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <button type="button" wire:click="showDeleteSpaceConfirm({{ $space->id }}, '{{ addslashes($space->name) }}')" class="company-user-edit-btn" style="color: #DC2626;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                    </svg>
                                    Delete
                                </button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="company-users-empty">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        <p class="company-users-empty-text">No meeting rooms yet. Add spaces to allow visitors to book specific rooms.</p>
                    </div>
                    @endif

                    @if(auth()->user()->isAdmin() || auth()->user()->hasPermission('manage_buildings'))
                    <button type="button" wire:click="createSpace" class="company-add-user-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Add Room</span>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar Card --}}
        <form wire:submit="saveBuilding" class="company-edit-sidebar">
            <div class="company-edit-card company-edit-card--sidebar">
                <div class="company-edit-card-header">
                    <div class="company-edit-card-icon company-edit-card-icon--primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 class="company-edit-card-title">Building Information</h3>
                        <p class="company-edit-card-subtitle">Manage details</p>
                    </div>
                </div>
                <div class="company-edit-card-body">
                    <div class="companies-form-field">
                        <label class="companies-form-label">Building Name *</label>
                        <input type="text" wire:model="building_name" class="companies-form-input" placeholder="Enter building name" {{ !auth()->user()->can('manageBuildings', App\Models\User::class) ? 'disabled' : '' }}>
                        @error('building_name') <p class="companies-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="companies-form-field">
                        <label class="companies-form-label">Address</label>
                        <textarea wire:model="building_address" class="companies-form-input companies-form-textarea" rows="2" placeholder="Street address, city, postal code" {{ !auth()->user()->can('manageBuildings', App\Models\User::class) ? 'disabled' : '' }}></textarea>
                        @error('building_address') <p class="companies-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="companies-form-field">
                        <label class="companies-form-toggle">
                            <input type="checkbox" wire:model="building_is_active" class="companies-form-checkbox" {{ !auth()->user()->can('manageBuildings', App\Models\User::class) ? 'disabled' : '' }}>
                            <span class="companies-form-toggle-label">
                                <span class="companies-form-toggle-text">Active Status</span>
                                <span class="companies-form-toggle-hint">Building and its entrances will be available</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            @can('manageBuildings', App\Models\User::class)
            {{-- Actions Card --}}
            <div class="company-edit-card company-edit-card--sidebar">
                <div class="company-edit-card-header">
                    <div class="company-edit-card-icon company-edit-card-icon--green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 class="company-edit-card-title">Save Changes</h3>
                        <p class="company-edit-card-subtitle">Update building info</p>
                    </div>
                </div>
                <div class="company-edit-card-body">
                    <button type="submit" class="companies-btn companies-btn--primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Save Changes 
            </button>
                </div>
            </div>
            @endcan
        </form>
    </div>

    {{-- Entrance Modal --}}
    @if($showEntranceModal)
    <div class="users-modal-overlay" wire:click.self="$set('showEntranceModal', false)">
        <div class="users-modal">
            <div class="users-modal-header">
                <div class="users-modal-icon" style="background: rgba(139, 92, 246, 0.15); color: #8B5CF6;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                </div>
                <div>
                    <h3 class="users-modal-title">{{ $editingEntranceId ? 'Edit' : 'Add' }} Entrance</h3>
                    <p class="users-modal-subtitle">{{ $editingEntranceId ? 'Update entrance settings' : 'Create a new entrance for this building' }}</p>
                </div>
            </div>

            <form wire:submit="saveEntrance" class="users-modal-body">
                <div class="users-form-grid">
                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Entrance Name *</label>
                        <input type="text" wire:model="entrance_name" class="users-form-input" placeholder="e.g. Main Lobby, North Gate">
                        @error('entrance_name') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Kiosk URL Identifier *</label>
                        <div style="display: flex; align-items: center;">
                            <span style="background: var(--bg-main); color: var(--text-muted); padding: 0.625rem 0.875rem; border-radius: var(--radius-md); border-top-right-radius: 0; border-bottom-right-radius: 0; font-size: 0.875rem; border: 2px solid transparent;">/kiosk/</span>
                            <input type="text" wire:model="entrance_kiosk_identifier" class="users-form-input" style="border-top-left-radius: 0; border-bottom-left-radius: 0;" placeholder="main-lobby">
                        </div>
                        @error('entrance_kiosk_identifier') <p class="users-form-error">{{ $message }}</p> @enderror
                        <p class="buildings-form-hint" style="font-size: 0.6875rem; color: var(--text-muted); margin-top: 0.375rem;">This will be the unique URL for this entrance's kiosk.</p>
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" wire:model="entrance_is_active" class="users-form-checkbox">
                            <span class="users-form-toggle-label">
                                <span class="users-form-toggle-text">Active Status</span>
                                <span class="users-form-toggle-hint">Inactive entrances will not process new check-ins</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="users-modal-footer">
                    <button type="button" wire:click="$set('showEntranceModal', false)" class="users-btn users-btn--secondary">Cancel</button>
                    <button type="submit" class="users-btn users-btn--primary">{{ $editingEntranceId ? 'Save Changes' : 'Create Entrance' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- Space Modal --}}
    @if($showSpaceModal)
    <div class="users-modal-overlay" wire:click.self="$set('showSpaceModal', false)">
        <div class="users-modal">
            <div class="users-modal-header">
                <div class="users-modal-icon" style="background: rgba(59, 130, 246, 0.15); color: #3B82F6;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="12" y1="8" x2="12" y2="16"></line>
                        <line x1="8" y1="12" x2="16" y2="12"></line>
                    </svg>
                </div>
                <div>
                    <h3 class="users-modal-title">{{ $editingSpaceId ? 'Edit' : 'Add' }} Meeting Room</h3>
                    <p class="users-modal-subtitle">{{ $editingSpaceId ? 'Update room details' : 'Create a new meeting room' }}</p>
                </div>
            </div>

            <form wire:submit="saveSpace" class="users-modal-body">
                <div class="users-form-grid">
                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Meeting Room Name *</label>
                        <input type="text" wire:model="space_name" class="users-form-input" placeholder="e.g. Conference Room A">
                        @error('space_name') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Capacity (Number of People)</label>
                        <input type="number" wire:model="space_capacity" class="users-form-input" placeholder="e.g. 10" min="1" step="1">
                        @error('space_capacity') <p class="users-form-error">{{ $message }}</p> @enderror
                        <p class="buildings-form-hint" style="font-size: 0.6875rem; color: var(--text-muted); margin-top: 0.375rem;">Leave empty if unlimited.</p>
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Amenities</label>
                        <input type="text" wire:model="space_amenities" class="users-form-input" placeholder="e.g. Whiteboard, Projector, Video Call (comma separated)">
                        @error('space_amenities') <p class="users-form-error">{{ $message }}</p> @enderror
                        <p class="buildings-form-hint" style="font-size: 0.6875rem; color: var(--text-muted); margin-top: 0.375rem;">Separate amenities with commas.</p>
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" wire:model="space_is_active" class="users-form-checkbox">
                            <span class="users-form-toggle-label">
                                <span class="users-form-toggle-text">Active Status</span>
                                <span class="users-form-toggle-hint">Inactive meeting rooms will not be available for new visits</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="users-modal-footer">
                    <button type="button" wire:click="$set('showSpaceModal', false)" class="users-btn users-btn--secondary">Cancel</button>
                    <button type="submit" class="users-btn users-btn--primary">{{ $editingSpaceId ? 'Save Changes' : 'Add Room' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
