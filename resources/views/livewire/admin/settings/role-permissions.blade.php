<x-slot name="title">RBAC Settings</x-slot>

<div class="settings-page">
    {{-- Header --}}
    <div class="settings-header">
        <div class="settings-header-content">
            <div class="settings-header-icon settings-header-icon--blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <div>
                <h1 class="settings-title">Role-Based Access Control</h1>
                <p class="settings-subtitle">Configure permissions for each role in your organization</p>
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

    {{-- Permissions Matrix Card --}}
    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon settings-card-icon--primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Permissions Matrix</h3>
                <p class="settings-card-description">Grant or revoke access to specific system features for each role</p>
            </div>
        </div>

        <form wire:submit="save" class="settings-card-body">
            <div class="settings-table-wrapper">
                <table class="settings-table">
                    <thead>
                        <tr>
                            <th class="settings-table-header settings-table-header--permission">
                                Permission
                            </th>
                            @foreach($roles as $roleKey => $roleLabel)
                            <th class="settings-table-header settings-table-header--role">
                                <div class="settings-role-badge settings-role-badge--{{ $roleKey }}">
                                    {{ $roleLabel }}
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissionCategories as $categoryName => $permissionsGroup)
                        {{-- Category Header Row --}}
                        <tr class="settings-table-row bg-gray-50 dark:bg-gray-800/50">
                            <td colspan="{{ count($roles) + 1 }}" class="px-6 py-3 text-sm font-semibold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700">
                                {{ $categoryName }}
                            </td>
                        </tr>
                        {{-- Permissions in Category --}}
                        @foreach($permissionsGroup as $permKey => $permLabel)
                        <tr class="settings-table-row">
                            <td class="settings-table-cell settings-table-cell--permission pl-10">
                                <div class="settings-permission-info">
                                    <span class="settings-permission-name">{{ $permLabel }}</span>
                                    <span class="settings-permission-key">{{ $permKey }}</span>
                                </div>
                            </td>
                            @foreach($roles as $roleKey => $roleLabel)
                            @php
                                $isLocked = $roleKey === 'admin' || (auth()->user()->role === 'administrator' && in_array($roleKey, ['admin', 'administrator']));
                                $isChecked = $isLocked || in_array($permKey, $permissions[$roleKey] ?? []);
                            @endphp
                            <td class="settings-table-cell settings-table-cell--checkbox">
                                <label class="settings-checkbox {{ $isLocked ? 'settings-checkbox--locked' : '' }}">
                                    <input
                                        type="checkbox"
                                        wire:model="permissions.{{ $roleKey }}"
                                        value="{{ $permKey }}"
                                        {{ $isLocked ? 'disabled checked' : '' }}
                                        class="settings-checkbox-input"
                                    >
                                    <span class="settings-checkbox-custom">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </span>
                                </label>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="settings-legend">
                <div class="settings-legend-item">
                    <span class="settings-checkbox settings-checkbox--locked" style="pointer-events: none;">
                        <span class="settings-checkbox-custom">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12"></polyline>
                            </svg>
                        </span>
                    </span>
                    <span class="settings-legend-text">Locked permission (cannot be modified)</span>
                </div>
            </div>

            <div class="settings-form-footer">
                <button type="submit" class="settings-btn settings-btn--primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Save Permissions
                </button>
            </div>
        </form>
    </div>
</div>