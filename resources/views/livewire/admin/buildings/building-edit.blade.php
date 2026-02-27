<x-slot name="title">Manage Building</x-slot>

<div class="buildings-page">
    {{-- Header --}}
    <div class="buildings-header">
        <div class="buildings-header-top">
            <div>
                <div class="building-edit-breadcrumb">
                    <a href="{{ route('admin.buildings') }}" class="buildings-btn buildings-btn--secondary" style="margin-bottom: 1rem; display: inline-flex;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Back to Buildings
                    </a>
                </div>
                <h1 class="buildings-title">Manage: {{ $building->name }}</h1>
                <p class="buildings-subtitle">Update building information and administer its entrances and meeting rooms</p>
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

    <div class="company-edit-grid mt-6">
        {{-- Main Content Column --}}
        <div class="company-edit-main space-y-6">
            {{-- Entrances --}}
            <div class="buildings-card">
                <div class="buildings-card-header">
                    <div>
                        <h3 class="buildings-card-title">Entrances</h3>
                        <p class="buildings-card-subtitle">{{ $entrances->count() }} entrance{{ $entrances->count() === 1 ? '' : 's' }}</p>
                    </div>
                </div>
                
                <div class="buildings-card-body p-6">
                    @if($entrances->count() > 0)
                        <div class="space-y-4">
                            @foreach($entrances as $entrance)
                            <div class="buildings-entrance-item">
                                <div class="buildings-entrance-info">
                                    <div class="buildings-entrance-name">
                                        {{ $entrance->name }}
                                        @if($entrance->is_active)
                                            <span class="buildings-badge buildings-badge--success ml-2">Active</span>
                                        @else
                                            <span class="buildings-badge buildings-badge--secondary ml-2">Inactive</span>
                                        @endif
                                    </div>
                                    <div class="buildings-entrance-meta">
                                        <a href="{{ route('kiosk.welcome', $entrance->kiosk_identifier) }}" target="_blank" class="buildings-link">
                                            Kiosk Link
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1 inline">
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                <polyline points="15 3 21 3 21 9"></polyline>
                                                <line x1="10" y1="14" x2="21" y2="3"></line>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                <div class="buildings-entrance-actions">
                                    <button wire:click="editEntrance({{ $entrance->id }})" class="buildings-action-btn buildings-action-btn--primary" title="Edit Entrance">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                    @if(auth()->user()->isAdmin())
                                    <button wire:click="showDeleteEntranceConfirm({{ $entrance->id }}, '{{ addslashes($entrance->name) }}')" class="buildings-action-btn buildings-action-btn--danger" title="Delete Entrance">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <h3 class="text-secondary font-medium mb-1">No entrances yet</h3>
                            <p class="text-muted text-sm">Create an entrance to set up a kiosk</p>
                        </div>
                    @endif

                    <div class="mt-6 flex justify-end">
                        <button wire:click="createEntrance" class="buildings-btn buildings-btn--primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Add Entrance
                        </button>
                    </div>
                </div>
            </div>

            {{-- Meeting Rooms (Spaces) --}}
            <div class="buildings-card">
                <div class="buildings-card-header">
                    <div>
                        <h3 class="buildings-card-title">Meeting Rooms</h3>
                        <p class="buildings-card-subtitle">{{ $spaces->count() }} space{{ $spaces->count() === 1 ? '' : 's' }}</p>
                    </div>
                </div>
                
                <div class="buildings-card-body p-6">
                    @if($spaces->count() > 0)
                        <div class="buildings-spaces-grid">
                            @foreach($spaces as $space)
                            <div class="buildings-space-item">
                                <div class="buildings-space-info">
                                    <div class="buildings-space-header">
                                        <span class="buildings-space-name">{{ $space->name }}</span>
                                        @if($space->is_active)
                                            <span class="buildings-badge buildings-badge--success">Active</span>
                                        @else
                                            <span class="buildings-badge buildings-badge--secondary">Inactive</span>
                                        @endif
                                    </div>
                                    @if($space->amenities)
                                    <div class="buildings-space-amenities">
                                        @foreach($space->amenities as $amenity)
                                            <span class="buildings-amenity-pill">{{ $amenity }}</span>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                @if(auth()->user()->isAdmin())
                                <div class="buildings-space-actions">
                                    <button wire:click="editSpace({{ $space->id }})" class="buildings-action-btn buildings-action-btn--primary" title="Edit Space">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="showDeleteSpaceConfirm({{ $space->id }}, '{{ addslashes($space->name) }}')" class="buildings-action-btn buildings-action-btn--danger" title="Delete Space">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                    </button>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-6">
                            <h3 class="text-secondary font-medium mb-1">No meeting rooms yet</h3>
                            <p class="text-muted text-sm">Add spaces to allow visitors to book specific rooms</p>
                        </div>
                    @endif

                    @if(auth()->user()->isAdmin())
                    <div class="mt-6 flex justify-end">
                        <button wire:click="createSpace" class="buildings-btn buildings-btn--primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            Add  Room
                        </button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar Cards --}}
        <div class="company-edit-sidebar">
            <form wire:submit="saveBuilding" class="company-edit-card company-edit-card--sidebar">
                <div class="company-edit-card-header">
                    <div class="company-edit-card-icon company-edit-card-icon--blue">
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
                    <div class="buildings-form-field">
                        <label class="buildings-form-label">Building Name *</label>
                        <input type="text" wire:model="building_name" class="buildings-form-input">
                        @error('building_name') <p class="buildings-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="buildings-form-field">
                        <label class="buildings-form-label">Address</label>
                        <textarea wire:model="building_address" class="buildings-form-input buildings-form-textarea" rows="2"></textarea>
                        @error('building_address') <p class="buildings-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="buildings-form-field">
                        <label class="buildings-form-toggle">
                            <input type="checkbox" wire:model="building_is_active" class="buildings-form-checkbox">
                            <div class="buildings-form-toggle-switch"></div>
                            <span class="buildings-form-toggle-label">Active Status</span>
                        </label>
                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="buildings-btn buildings-btn--primary">
                            Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Entrance Modal --}}
    @if($showEntranceModal)
    <div class="buildings-modal-overlay">
        <div class="buildings-modal">
            <form wire:submit="saveEntrance">
                <div class="buildings-modal-header">
                    <h3 class="buildings-modal-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-primary">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                            <polyline points="10 17 15 12 10 7"></polyline>
                            <line x1="15" y1="12" x2="3" y2="12"></line>
                        </svg>
                        {{ $editingEntranceId ? 'Edit' : 'Add' }} Entrance
                    </h3>
                    <button type="button" wire:click="$set('showEntranceModal', false)" class="buildings-modal-close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                
                <div class="buildings-modal-body">
                    <div class="buildings-form-grid">
                        <div class="buildings-form-field buildings-form-field--full">
                            <label class="buildings-form-label">Entrance Name *</label>
                            <input type="text" wire:model="entrance_name" class="buildings-form-input" placeholder="e.g. Main Lobby, North Gate">
                            @error('entrance_name') <p class="buildings-form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="buildings-form-field buildings-form-field--full">
                            <label class="buildings-form-label">Kiosk URL Identifier *</label>
                            <div class="flex items-center">
                                <span class="bg-gray-100 text-gray-500 px-3 py-2 border border-r-0 border-gray-200 rounded-l-lg text-sm">/kiosk/</span>
                                <input type="text" wire:model="entrance_kiosk_identifier" class="buildings-form-input rounded-l-none" placeholder="main-lobby">
                            </div>
                            @error('entrance_kiosk_identifier') <p class="buildings-form-error">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500 mt-1">This will be the unique URL for this entrance's kiosk.</p>
                        </div>

                        <div class="buildings-form-field buildings-form-field--full">
                            <label class="buildings-form-toggle">
                                <input type="checkbox" wire:model="entrance_is_active" class="buildings-form-checkbox">
                                <div class="buildings-form-toggle-switch"></div>
                                <span class="buildings-form-toggle-label">Active Status</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 mb-2">Inactive entrances will not process new check-ins.</p>
                        </div>
                    </div>
                </div>
                
                <div class="buildings-modal-footer">
                    <button type="button" wire:click="$set('showEntranceModal', false)" class="buildings-btn buildings-btn--secondary">Cancel</button>
                    <button type="submit" class="buildings-btn buildings-btn--primary">{{ $editingEntranceId ? 'Save Changes' : 'Create Entrance' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
    
    {{-- Space Modal --}}
    @if($showSpaceModal)
    <div class="buildings-modal-overlay">
        <div class="buildings-modal">
            <form wire:submit="saveSpace">
                <div class="buildings-modal-header">
                    <h3 class="buildings-modal-title">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-primary">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="12" y1="8" x2="12" y2="16"></line>
                            <line x1="8" y1="12" x2="16" y2="12"></line>
                        </svg>
                        {{ $editingSpaceId ? 'Edit' : 'Add' }} Meeting Room
                    </h3>
                    <button type="button" wire:click="$set('showSpaceModal', false)" class="buildings-modal-close">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                
                <div class="buildings-modal-body">
                    <div class="buildings-form-grid">
                        <div class="buildings-form-field buildings-form-field--full">
                            <label class="buildings-form-label">Meeting Room Name *</label>
                            <input type="text" wire:model="space_name" class="buildings-form-input" placeholder="e.g. Conference Room A">
                            @error('space_name') <p class="buildings-form-error">{{ $message }}</p> @enderror
                        </div>

                        <div class="buildings-form-field buildings-form-field--full">
                            <label class="buildings-form-label">Amenities</label>
                            <input type="text" wire:model="space_amenities" class="buildings-form-input" placeholder="e.g. Whiteboard, Projector, Video Call (comma separated)">
                            @error('space_amenities') <p class="buildings-form-error">{{ $message }}</p> @enderror
                            <p class="text-xs text-gray-500 mt-1">Separate amenities with commas.</p>
                        </div>

                        <div class="buildings-form-field buildings-form-field--full">
                            <label class="buildings-form-toggle">
                                <input type="checkbox" wire:model="space_is_active" class="buildings-form-checkbox">
                                <div class="buildings-form-toggle-switch"></div>
                                <span class="buildings-form-toggle-label">Active Status</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1 mb-2">Inactive meeting rooms will not be available for new visits.</p>
                        </div>
                    </div>
                </div>
                
                <div class="buildings-modal-footer">
                    <button type="button" wire:click="$set('showSpaceModal', false)" class="buildings-btn buildings-btn--secondary">Cancel</button>
                    <button type="submit" class="buildings-btn buildings-btn--primary">{{ $editingSpaceId ? 'Save Changes' : 'Add Room' }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div> 
