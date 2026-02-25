<x-slot name="title">Edit Company</x-slot>

<div class="companies-page">
    {{-- Header --}}
    <div class="companies-header">
        <div class="companies-header-top">
            <div>
                <div class="company-edit-breadcrumb">
                    <a href="{{ route('admin.companies') }}" class="company-edit-back">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="19" y1="12" x2="5" y2="12"></line>
                            <polyline points="12 19 5 12 12 5"></polyline>
                        </svg>
                        Back to Companies
                    </a>
                </div>
                <h1 class="companies-title">Edit: {{ $company->name }}</h1>
                <p class="companies-subtitle">Update company information</p>
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
        {{-- Contact Details Card --}}
        <div class="company-summary-card">
            <div class="company-summary-card-header">
                <div class="company-summary-card-icon company-summary-card-icon--blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </div>
                <h3 class="company-summary-card-title">Contact Person Overview</h3>
            </div>
            <div class="company-summary-card-body">
                @if($company->mainContact)
                    <div class="company-summary-item">
                        <span class="company-summary-label">Name</span>
                        <span class="company-summary-value">{{ $company->mainContact->name }}</span>
                    </div>
                    <div class="company-summary-item">
                        <span class="company-summary-label">Email</span>
                        <span class="company-summary-value">{{ $company->mainContact->email ?: '—' }}</span>
                    </div>
                    <div class="company-summary-item">
                        <span class="company-summary-label">Phone</span>
                        <span class="company-summary-value">{{ $company->mainContact->phone ?: '—' }}</span>
                    </div>
                @else
                    <div class="company-summary-item">
                        <span class="company-summary-label">Contact Person</span>
                        <span class="company-summary-value">{{ $company->contact_person ?: '—' }}</span>
                    </div>
                    <div class="company-summary-item">
                        <span class="company-summary-label">Email</span>
                        <span class="company-summary-value">{{ $company->email ?: '—' }}</span>
                    </div>
                    <div class="company-summary-item">
                        <span class="company-summary-label">Phone</span>
                        <span class="company-summary-value">{{ $company->phone ?: '—' }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Contract Details Card --}}
        <div class="company-summary-card">
            <div class="company-summary-card-header">
                <div class="company-summary-card-icon company-summary-card-icon--amber">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <h3 class="company-summary-card-title">Contract Details</h3>
            </div>
            <div class="company-summary-card-body">
                <div class="company-summary-item">
                    <span class="company-summary-label">Start Date</span>
                    <span class="company-summary-value">{{ $company->contract_start_date?->format('d M Y') ?? '—' }}</span>
                </div>
                <div class="company-summary-item">
                    <span class="company-summary-label">End Date</span>
                    <span class="company-summary-value">{{ $company->contract_end_date?->format('d M Y') ?? '—' }}</span>
                </div>
                @if($company->contract_start_date && $company->contract_end_date)
                <div class="company-summary-item">
                    <span class="company-summary-label">Status</span>
                    @php
                        $remaining = now()->diffInDays($company->contract_end_date, false);
                        if ($remaining > 0) $remaining = (int) ceil($remaining);
                    @endphp
                    <span class="company-summary-value">
                        @if($remaining > 0)
                            <span class="company-summary-badge company-summary-badge--success">{{ $remaining }} days remaining</span>
                        @elseif($remaining < 0)
                            <span class="company-summary-badge company-summary-badge--danger">Expired</span>
                        @else
                            <span class="company-summary-badge company-summary-badge--warning">Ends today</span>
                        @endif
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Quick Stats Card --}}
        <div class="company-summary-card">
            <div class="company-summary-card-header">
                <div class="company-summary-card-icon company-summary-card-icon--green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3v18h18"></path>
                        <rect x="7" y="14" width="4" height="7"></rect>
                        <rect x="15" y="7" width="4" height="14"></rect>
                    </svg>
                </div>
                <h3 class="company-summary-card-title">Quick Stats</h3>
            </div>
            <div class="company-summary-card-body">
                <div class="company-summary-item">
                    <span class="company-summary-label">Total Users</span>
                    <span class="company-summary-value">{{ $company->users()->count() }}</span>
                </div>
                <div class="company-summary-item">
                    <span class="company-summary-label">Total Visitors</span>
                    <span class="company-summary-value">{{ $company->visitors()->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="company-edit-grid">
        {{-- Main Content Column --}}
        <div class="company-edit-main">
            {{-- Basic Information Card --}}
            <div class="company-edit-card company-edit-card--main">
                <div class="company-edit-card-header">
                    <div class="company-edit-card-icon company-edit-card-icon--primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                            <polyline points="9 22 9 12 15 12 15 22"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 class="company-edit-card-title">Basic Information</h3>
                        <p class="company-edit-card-subtitle">Company details and contact information</p>
                    </div>
                </div>
                <div class="company-edit-card-body">
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
                            <label class="companies-form-toggle">
                                <input type="checkbox" wire:model="is_active" class="companies-form-checkbox">
                                <span class="companies-form-toggle-label">
                                    <span class="companies-form-toggle-text">Active Status</span>
                                    <span class="companies-form-toggle-hint">Company will be available for selection in visits</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Company Users Card --}}
            <div class="company-edit-card company-edit-card--main">
                <div class="company-edit-card-header">
                    <div class="company-edit-card-icon company-edit-card-icon--green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="company-edit-card-title">Company Users</h3>
                        <p class="company-edit-card-subtitle">{{ $companyUsers->count() }} user{{ $companyUsers->count() !== 1 ? 's' : '' }} assigned</p>
                    </div>
                </div>
                <div class="company-edit-card-body">
                    @if($companyUsers->count() > 0)
                    <div class="company-users-list">
                        @foreach($companyUsers as $user)
                        <div class="company-user-item" wire:key="user-{{ $user->id }}">
                            <div class="company-user-info">
                                <div class="company-user-name">
                                    {{ $user->name }}
                                    @if($user->id === $main_contact_user_id)
                                    <span class="company-user-main-badge">Main</span>
                                    @endif
                                </div>
                                <div class="company-user-email">{{ $user->email }}</div>
                            </div>
                            <div class="company-user-meta">
                                @if($user->role === 'admin')
                                <span class="company-user-role company-user-role--admin">God Mode</span>
                                @elseif($user->role === 'administrator')
                                <span class="company-user-role company-user-role--admin">Administrator</span>
                                @elseif($user->role === 'tenant')
                                <span class="company-user-role company-user-role--receptionist">Tenant</span>
                                @elseif($user->role === 'receptionist')
                                <span class="company-user-role company-user-role--receptionist">Receptionist</span>
                                @else
                                <span class="company-user-role company-user-role--viewer">Viewer</span>
                                @endif
                                @if($user->is_active)
                                <span class="company-user-status company-user-status--active">Active</span>
                                @else
                                <span class="company-user-status company-user-status--inactive">Inactive</span>
                                @endif
                            </div>
                            <button type="button" wire:click="editCompanyUser({{ $user->id }})" class="company-user-edit-btn">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                </svg>
                                Edit
                            </button>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="company-users-empty">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                        <p class="company-users-empty-text">No users assigned to this company yet.</p>
                    </div>
                    @endif

                    <button type="button" wire:click="createCompanyUser" class="company-add-user-btn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>Add User</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Sidebar Cards --}}
        <div class="company-edit-sidebar">
            {{-- Contract Details Card --}}
            <div class="company-edit-card company-edit-card--sidebar">
                <div class="company-edit-card-header">
                    <div class="company-edit-card-icon company-edit-card-icon--amber">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                    </div>
                    <div>
                        <h3 class="company-edit-card-title">Contract Details</h3>
                        <p class="company-edit-card-subtitle">Edit agreement period</p>
                    </div>
                </div>
                <div class="company-edit-card-body">
                    <div class="companies-form-field">
                        <label class="companies-form-label">Contract Start Date</label>
                        <input type="date" wire:model="contract_start_date" class="companies-form-input">
                        @error('contract_start_date') <p class="companies-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="companies-form-field">
                        <label class="companies-form-label">Contract End Date</label>
                        <input type="date" wire:model="contract_end_date" class="companies-form-input">
                        @error('contract_end_date') <p class="companies-form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Contact Person Card --}}
            <div class="company-edit-card company-edit-card--sidebar">
                <div class="company-edit-card-header">
                    <div class="company-edit-card-icon company-edit-card-icon--blue">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="company-edit-card-title">Contact Person</h3>
                        <p class="company-edit-card-subtitle">Select primary contact</p>
                    </div>
                </div>
                <div class="company-edit-card-body">
                    <div class="companies-form-field">
                        <label class="companies-form-label">Main Contact User</label>
                        @if($companyUsers->count() > 0)
                        <select wire:model="main_contact_user_id" class="companies-form-input companies-form-select">
                            <option value="">-- Select a user --</option>
                            @foreach($companyUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        @else
                        <div class="companies-form-hint text-amber-600">No users found.</div>
                        @endif
                    </div>

                    <div class="companies-form-divider"></div>

                    <div class="companies-form-field">
                        <label class="companies-form-label">Alternative Contact Name</label>
                        <input type="text" wire:model="contact_person" class="companies-form-input" placeholder="Name (if not a user)">
                        @error('contact_person') <p class="companies-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="companies-form-field">
                        <label class="companies-form-label">Alternative Contact Email</label>
                        <input type="email" wire:model="contact_person_email" class="companies-form-input" placeholder="email@example.com">
                        @error('contact_person_email') <p class="companies-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="companies-form-field">
                        <label class="companies-form-label">Alternative Contact Phone</label>
                        <input type="text" wire:model="contact_person_phone" class="companies-form-input" placeholder="+1 (555) 123-4567">
                        @error('contact_person_phone') <p class="companies-form-error">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Actions Footer --}}
        <div class="company-edit-actions">
            <a href="{{ route('admin.companies') }}" class="companies-btn companies-btn--secondary">Cancel</a>
            <button type="submit" class="companies-btn companies-btn--primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Save Changes
            </button>
        </div>
    </form>

    {{-- User Modal --}}
    @if($showUserModal)
    <div class="users-modal-overlay" wire:click.self="$set('showUserModal', false)">
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
                    <p class="users-modal-subtitle">{{ $editingUserId ? 'Update user account details' : 'Create a new user for ' . $company->name }}</p>
                </div>
            </div>

            <form wire:submit="saveUser" class="users-modal-body">
                <div class="users-form-grid">
                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Full Name *</label>
                        <input type="text" wire:model="user_name" class="users-form-input" placeholder="Enter full name">
                        @error('user_name') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Email Address *</label>
                        <input type="email" wire:model="user_email" class="users-form-input" placeholder="user@example.com">
                        @error('user_email') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field">
                        <label class="users-form-label">Phone Number</label>
                        <input type="tel" wire:model="user_phone" class="users-form-input" placeholder="+1 (555) 123-4567">
                        @error('user_phone') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field">
                        <label class="users-form-label">{{ $editingUserId ? 'New Password' : 'Password *' }}</label>
                        <input type="password" wire:model="user_password" class="users-form-input" placeholder="{{ $editingUserId ? 'Leave blank to keep current' : 'Minimum 8 characters' }}">
                        @error('user_password') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field">
                        <label class="users-form-label">Role *</label>
                        <select wire:model="user_role" class="users-form-input">
                            <option value="admin">God Mode</option>
                            <option value="administrator">Administrator</option>
                            <option value="tenant">Tenant</option>
                            <option value="receptionist">Receptionist</option>
                            <option value="viewer">Viewer</option>
                        </select>
                        @error('user_role') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-label">Notes</label>
                        <textarea wire:model="user_notes" class="users-form-input users-form-textarea" rows="3" placeholder="Additional notes about this user..."></textarea>
                        @error('user_notes') <p class="users-form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="users-form-field users-form-field--full">
                        <label class="users-form-toggle">
                            <input type="checkbox" wire:model="user_is_active" class="users-form-checkbox">
                            <span class="users-form-toggle-label">
                                <span class="users-form-toggle-text">Active Status</span>
                                <span class="users-form-toggle-hint">User will be able to log in and access the system</span>
                            </span>
                        </label>
                    </div>
                </div>

                <div class="users-modal-footer">
                    <button type="button" wire:click="$set('showUserModal', false)" class="users-btn users-btn--secondary">Cancel</button>
                    <button type="submit" class="users-btn users-btn--primary">
                        {{ $editingUserId ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>