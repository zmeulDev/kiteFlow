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

    {{-- Associated Users Card --}}
    @if(auth()->user()->company_id)
    <div class="settings-card mt-8">
        <div class="settings-card-header !pb-4 !border-b !border-gray-200 dark:!border-gray-700">
            <div>
                <h3 class="settings-card-title">Associated Users</h3>
                <p class="settings-card-description">Users belonging to your company</p>
            </div>
            <a href="{{ route('admin.users') }}" class="settings-btn settings-btn--outline text-sm">
                Manage Users
            </a>
        </div>
        
        <div class="settings-card-body p-0">
            @if($businessUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                                <th class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($businessUsers as $user)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-semibold text-xs mr-3">
                                            {{ collect(explode(' ', $user->name))->map(fn($w) => substr($w, 0, 1))->take(2)->join('') }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200">
                                        {{ \App\Models\User::getRoles()[$user->role] ?? ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-6 text-center text-gray-500 dark:text-gray-400">
                    No users currently associated with your company.
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="settings-card mt-8">
        <div class="p-6 text-center text-gray-500 dark:text-gray-400">
            You are a global System Administrator not bound to a specific company. 
            <br>
            <a href="{{ route('admin.companies') }}" class="text-blue-600 hover:underline mt-2 inline-block">Manage All Companies</a>
        </div>
    </div>
    @endif
</div>
