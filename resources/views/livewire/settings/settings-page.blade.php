<div class="space-y-4 lg:space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Settings</h1>
        <p class="mt-1 text-sm text-gray-500">Manage your organization settings</p>
    </div>

    @if(session()->has('message'))
    <div class="p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-green-100 flex-shrink-0">
            <i class="fa-solid fa-check text-green-600 text-sm"></i>
        </div>
        <p class="text-sm text-green-800">{{ session('message') }}</p>
    </div>
    @endif

    @if($tenant)
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <form wire:submit="save">
            <!-- Organization Info -->
            <div class="p-4 lg:p-6 border-b border-gray-100">
                <h2 class="text-base lg:text-lg font-medium text-gray-900 mb-4">Organization Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Organization Name *</label>
                        <input type="text" wire:model="name" 
                               class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500" 
                               required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" wire:model="email" 
                               class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500" 
                               required>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" wire:model="phone" 
                               class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                        <select wire:model="currency" 
                                class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="USD">USD - US Dollar</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound</option>
                            <option value="RON">RON - Romanian Leu</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Regional Settings -->
            <div class="p-4 lg:p-6 border-b border-gray-100">
                <h2 class="text-base lg:text-lg font-medium text-gray-900 mb-4">Regional Settings</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Timezone *</label>
                        <select wire:model="timezone" 
                                class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="UTC">UTC</option>
                            <option value="Europe/Bucharest">Europe/Bucharest</option>
                            <option value="Europe/London">Europe/London</option>
                            <option value="America/New_York">America/New York</option>
                            <option value="America/Los_Angeles">America/Los Angeles</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Language *</label>
                        <select wire:model="locale" 
                                class="w-full px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-brand-500">
                            <option value="en">English</option>
                            <option value="ro">Română</option>
                            <option value="de">Deutsch</option>
                            <option value="fr">Français</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="p-4 lg:p-6 bg-gray-50 flex justify-end">
                <button type="submit" 
                        class="px-6 py-2.5 text-sm font-medium text-white bg-brand-600 rounded-lg hover:bg-brand-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    @else
    <div class="bg-white rounded-xl border border-gray-200 p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-building text-2xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No Organization</h3>
        <p class="text-sm text-gray-500">You are not associated with any organization. Please contact support.</p>
    </div>
    @endif

    <!-- Navigation Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-4">
        <a href="{{ route('settings.users') }}"
           class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6 hover:border-brand-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-medium text-gray-900">Users</h3>
                    <p class="text-sm text-gray-500">Manage team members</p>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-400 ml-auto"></i>
            </div>
        </a>

        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('settings.tenants') }}"
           class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6 hover:border-brand-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-building text-green-600 text-xl"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-medium text-gray-900">Tenants</h3>
                    <p class="text-sm text-gray-500">Manage organizations</p>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-400 ml-auto"></i>
            </div>
        </a>
        @endif

        @if(auth()->user()->hasRole('admin') && !auth()->user()->isSuperAdmin())
        <a href="{{ route('settings.subtenants') }}"
           class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6 hover:border-brand-300 hover:shadow-sm transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-building text-purple-600 text-xl"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-medium text-gray-900">Sub-Tenants</h3>
                    <p class="text-sm text-gray-500">Manage sub-tenants</p>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-400 ml-auto"></i>
            </div>
        </a>
        @endif

        <div class="bg-white rounded-xl border border-gray-200 p-4 lg:p-6 opacity-50 cursor-not-allowed">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-lg bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-plug text-purple-600 text-xl"></i>
                </div>
                <div class="min-w-0">
                    <h3 class="font-medium text-gray-900">Integrations</h3>
                    <p class="text-sm text-gray-500">Coming soon</p>
                </div>
            </div>
        </div>
    </div>
</div>