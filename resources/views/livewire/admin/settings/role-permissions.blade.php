<x-slot name="title">RBAC Settings</x-slot>

<div class="settings-page">
    {{-- Header --}}
    <div class="settings-header">
        <h1 class="settings-title">Role-Based Access Control (RBAC)</h1>
        <p class="settings-subtitle">Manage default permissions for administrator, receptionist, and viewer roles.</p>
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

    <div class="settings-card">
        <div class="settings-card-header">
            <div class="settings-card-icon settings-card-icon--primary">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <div>
                <h3 class="settings-card-title">Role Permissions Matrix</h3>
                <p class="settings-card-subtitle">Check the boxes to grant access to specific system areas.</p>
            </div>
        </div>
        
        <form wire:submit="save" class="settings-card-body">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="p-4 border-b border-gray-200 font-semibold text-gray-700 bg-gray-50/50 rounded-tl-lg">Permission</th>
                            @foreach($roles as $roleKey => $roleLabel)
                            <th class="p-4 border-b border-gray-200 font-semibold text-center text-gray-700 bg-gray-50/50 {{ $loop->last ? 'rounded-tr-lg' : '' }}">
                                {{ $roleLabel }}
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($availablePermissions as $permKey => $permLabel)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="p-4 border-b border-gray-100 text-sm font-medium text-gray-900">
                                {{ $permLabel }}
                            </td>
                            @foreach($roles as $roleKey => $roleLabel)
                            <td class="p-4 border-b border-gray-100 text-center">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        wire:model="permissions.{{ $roleKey }}" 
                                        value="{{ $permKey }}"
                                        class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 disabled:opacity-50"
                                        {{ $roleKey === 'admin' || (auth()->user()->role === 'administrator' && in_array($roleKey, ['admin', 'administrator'])) ? 'disabled checked' : '' }}
                                    >
                                </label>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if(isset($roles['admin']))
            <div class="mt-4 text-xs text-gray-500 italic">
                * Note: The Administrator role automatically has full access and cannot be restricted.
            </div>
            @endif

            <div class="mt-8 flex justify-end">
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
