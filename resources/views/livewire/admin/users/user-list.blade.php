<x-slot name="title">Users</x-slot>

<div class="users-page">
    {{-- Header with Stats --}}
    <div class="users-header">
        <div class="users-header-top">
            <div>
                <h1 class="users-title">Users</h1>
                <p class="users-subtitle">Manage system users and their access permissions</p>
            </div>
            <div class="users-stats">
                <div class="users-stat">
                    <div class="users-stat-icon users-stat-icon--total">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="users-stat-content">
                        <div class="users-stat-value">{{ $totalUsers }}</div>
                        <div class="users-stat-label">Total</div>
                    </div>
                </div>
                <div class="users-stat">
                    <div class="users-stat-icon users-stat-icon--active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                    </div>
                    <div class="users-stat-content">
                        <div class="users-stat-value">{{ $activeUsers }}</div>
                        <div class="users-stat-label">Active</div>
                    </div>
                </div>
                <div class="users-stat">
                    <div class="users-stat-icon users-stat-icon--admin">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                    </div>
                    <div class="users-stat-content">
                        <div class="users-stat-value">{{ $adminUsers }}</div>
                        <div class="users-stat-label">Admins</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('message'))
    <div class="users-flash users-flash--success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    @if(session()->has('error'))
    <div class="users-flash users-flash--error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="15" y1="9" x2="9" y2="15"></line>
            <line x1="9" y1="9" x2="15" y2="15"></line>
        </svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Filter Bar --}}
    <div class="users-filters">
        <div class="users-filters-row">
            <div class="users-filters-search">
                <svg class="users-filters-search-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search users by name or email...">
            </div>
            <div class="users-filters-select">
                <select wire:model.live="role_filter">
                    <option value="">All Roles</option>
                    @foreach(\App\Models\User::getRoles() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="users-filters-select">
                <select wire:model.live="company_filter">
                    <option value="">All Companies</option>
                    @if(auth()->user()->isAdmin())
                        <option value="global">Global System / {{ \App\Models\Setting::get('business_name', 'Main Business') }}</option>
                    @endif
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            @can('manageUsers', App\Models\User::class)
            <button wire:click="createUser" class="users-add-btn">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                <span>Add User</span>
            </button>
            @endcan
        </div>
    </div>

    {{-- Users List --}}
    <div class="users-list">
        @forelse($users as $user)
        <div class="user-card {{ $user->is_active ? 'user-card--active' : 'user-card--inactive' }} {{ $user->id === auth()->id() ? 'user-card--self' : '' }}" wire:key="user-{{ $user->id }}">
            <div class="user-main">
                <div class="user-avatar user-avatar--{{ $user->role }}">
                    {{ collect(explode(' ', $user->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
                </div>
                <div class="user-info">
                    <div class="user-name">
                        {{ $user->name }}
                        @if($user->id === auth()->id())
                        <span class="user-self-badge">You</span>
                        @endif
                    </div>
                    <div class="user-email">{{ $user->email }}</div>
                    @if($user->company)
                    <div class="user-company">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                        {{ $user->company->name }}
                    </div>
                    @endif
                </div>
            </div>

            <div class="user-role">
                @if($user->role === 'admin')
                <span class="user-role-badge user-role-badge--admin">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    {{ \App\Models\User::getRoles()[$user->role] ?? 'God Mode' }}
                </span>
                @elseif($user->role === 'administrator')
                <span class="user-role-badge user-role-badge--administrator">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    {{ \App\Models\User::getRoles()[$user->role] ?? 'Tenant' }}
                </span>
                @elseif($user->role === 'tenant')
                <span class="user-role-badge user-role-badge--tenant">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                    </svg>
                    {{ \App\Models\User::getRoles()[$user->role] ?? 'Sub-Tenant' }}
                </span>
                @elseif($user->role === 'receptionist')
                <span class="user-role-badge user-role-badge--receptionist">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    {{ \App\Models\User::getRoles()[$user->role] ?? 'Receptionist' }}
                </span>
                @else
                <span class="user-role-badge user-role-badge--viewer">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    {{ \App\Models\User::getRoles()[$user->role] ?? 'Viewer' }}
                </span>
                @endif
            </div>

            <div class="user-status">
                @if($user->is_active)
                <span class="user-status-badge user-status-badge--active">
                    <span class="user-status-dot"></span>
                    Active
                </span>
                @else
                <span class="user-status-badge user-status-badge--inactive">
                    Inactive
                </span>
                @endif
            </div>

            <div class="user-actions">
                @can('manageUsers', App\Models\User::class)
                <button wire:click="editUser({{ $user->id }})" class="user-action-btn user-action-btn--edit" title="Edit User">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                    </svg>
                </button>
                @endcan
                @if($user->id !== auth()->id())
                @can('manageUsers', App\Models\User::class)
                <button wire:click="showDeleteConfirm({{ $user->id }}, '{{ $user->name }}')" class="user-action-btn user-action-btn--delete">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </button>
                @endcan
                @endif
            </div>
        </div>
        @empty
        <div class="users-empty">
            <div class="users-empty-icon">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
            </div>
            <p class="users-empty-text">{{ $search || $role_filter ? 'No users found matching your filters.' : 'No users yet. Add your first user to get started.' }}</p>
        </div>
        @endforelse

        @if($users->hasPages())
        <div class="users-pagination">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    {{-- Modal --}}
    @if($showModal)
    <div class="users-modal-overlay" wire:click.self="$set('showModal', false)">
        <div class="users-modal">
            <div class="users-modal-header">
                <div class="users-modal-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
                <div>
                    <h3 class="users-modal-title">{{ $editingUserId ? 'Edit User' : 'New User' }}</h3>
                    <p class="users-modal-subtitle">{{ $editingUserId ? 'Update user account details' : 'Create a new system user' }}</p>
                </div>
            </div>

            <form wire:submit="save" class="users-modal-body">
                <div class="users-form-grid">
                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Full Name *</label>
                        <input type="text" wire:model="name" class="users-form-input" placeholder="Enter full name">
                        @error('name') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Email Address *</label>
                        <input type="email" wire:model="email" class="users-form-input" placeholder="user@example.com">
                        @error('email') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field">
                        <label class="users-form-label">Phone Number</label>
                        <input type="tel" wire:model="phone" class="users-form-input" placeholder="+1 (555) 123-4567">
                        @error('phone') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field">
                        <label class="users-form-label">{{ $editingUserId ? 'New Password' : 'Password *' }}</label>
                        <input type="password" wire:model="password" class="users-form-input" placeholder="{{ $editingUserId ? 'Leave blank to keep current' : 'Minimum 8 characters' }}">
                        @error('password') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field">
                        <label class="users-form-label">Role *</label>
                        <select wire:model="role" class="users-form-input">
                            @foreach(auth()->user()->getAssignableRoles() as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field">
                        <label class="users-form-label">Company</label>
                        <select wire:model="company_id" class="users-form-input">
                            @if(auth()->user()->canManageAllTenants())
                                <option value="">Global System / {{ \App\Models\Setting::get('business_name', 'Main Business') }}</option>
                            @else
                                <option value="">Select a company</option>
                            @endif
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Notes</label>
                        <textarea wire:model="notes" class="users-form-input users-form-textarea" rows="3" placeholder="Additional notes about this user..."></textarea>
                        @error('notes') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" wire:model="is_active" class="users-form-checkbox">
                            <span class="users-form-toggle-label">
                                <span class="users-form-toggle-text">Active Status</span>
                                <span class="users-form-toggle-hint">User will be able to log in and access the system</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="users-modal-footer">
                    <button type="button" wire:click="$set('showModal', false)" class="users-btn users-btn--secondary">Cancel</button>
                    <button type="submit" class="users-btn users-btn--primary">
                        {{ $editingUserId ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>