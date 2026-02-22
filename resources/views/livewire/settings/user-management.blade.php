<div class="space-y-4 lg:space-y-6" wire:key="user-management">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">User Management</h1>
            <p class="mt-1 text-sm text-gray-500">Manage users, roles and permissions</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-full hover:bg-brand-700 transition-colors shadow-sm">
            <i class="fa-solid fa-plus"></i>
            <span>Add User</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-100 p-4">
        <div class="relative">
            <i class="absolute left-4 top-1/2 -translate-y-1/2 fa-solid fa-magnifying-glass text-gray-400"></i>
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Search users by name or email..."
                   class="w-full pl-11 pr-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all">
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
        @forelse($users as $user)
        <div class="bg-white rounded-2xl border border-gray-100 p-4 hover:shadow-sm transition-shadow">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                        </div>
                        @if($user->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">
                            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                            Active
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                            Inactive
                        </span>
                        @endif
                    </div>
                    <div class="mt-2 flex items-center gap-3">
                        <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-brand-50 text-brand-700">
                            {{ $user->roles->first()?->name ?? 'No role' }}
                        </span>
                        <span class="text-xs text-gray-400">Joined {{ $user->created_at->format('M j, Y') }}</span>
                    </div>
                    <div class="mt-1 flex items-center gap-2">
                        <i class="fa-solid fa-building text-gray-400 text-xs"></i>
                        <span class="text-xs text-gray-500">{{ $user->tenants->first()?->name ?? 'No tenant' }}</span>
                        @if($user->tenants->first()?->parent_id)
                        <span class="text-xs text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded">Sub-tenant</span>
                        @endif
                    </div>
                    <div class="mt-3 flex items-center gap-2">
                        <button wire:click="openEditModal({{ $user->id }})"
                                class="flex-1 px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors text-center">
                            <i class="fa-solid fa-pen mr-1"></i> Edit
                        </button>
                        <button wire:click="openDeleteModal({{ $user->id }})"
                                class="px-4 py-2 text-xs font-semibold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-colors text-center">
                            <i class="fa-solid fa-trash mr-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-users text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-semibold text-gray-900">No users found</p>
            <p class="text-sm text-gray-500 mt-1">Add your first user</p>
        </div>
        @endforelse

        @if($users->hasPages())
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500">{{ $users->firstItem() }}-{{ $users->lastItem() }} of {{ $users->total() }}</p>
                <div class="flex items-center gap-2">
                    @if($users->onFirstPage())
                    <span class="p-2 text-gray-300"><i class="fa-solid fa-chevron-left text-xs"></i></span>
                    @else
                    <button wire:click="previousPage()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    @endif
                    @if($users->hasMorePages())
                    <button wire:click="nextPage()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                    @else
                    <span class="p-2 text-gray-300"><i class="fa-solid fa-chevron-right text-xs"></i></span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-white rounded-2xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">User</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tenant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Joined</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-brand-50 text-brand-700">
                                {{ $user->roles->first()?->name ?? 'No role' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-building text-gray-400 text-xs"></i>
                                <span class="text-sm text-gray-700">{{ $user->tenants->first()?->name ?? 'No tenant' }}</span>
                                @if($user->tenants->first()?->parent_id)
                                <span class="text-xs text-purple-600 bg-purple-50 px-1.5 py-0.5 rounded">Sub-tenant</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">{{ $user->created_at->format('M j, Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                Active
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">
                                Inactive
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEditModal({{ $user->id }})"
                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <button wire:click="openDeleteModal({{ $user->id }})"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                                    <i class="fa-solid fa-users text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">No users found</p>
                                <p class="text-sm text-gray-500 mt-1">Add your first user</p>
                                <button wire:click="openCreateModal" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-brand-600 bg-brand-50 rounded-xl hover:bg-brand-100 transition-colors">
                                    <i class="fa-solid fa-plus"></i>
                                    Add User
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }}</p>
            {{ $users->links() }}
        </div>
        @endif
    </div>
    
    <!-- Add/Edit Modal -->
    @if($showModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-data>
        <div class="fixed inset-0 bg-black/50" wire:click.self="$set('showModal', false)"></div>
        
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-xl my-8">
            <form wire:submit="save">
                <div class="flex items-center justify-between p-5 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">{{ $selectedUser ? 'Edit User' : 'Create User' }}</h2>
                    <button type="button" wire:click="$set('showModal', false)" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Name *</label>
                        <input type="text" wire:model="name" 
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all" 
                               required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email *</label>
                        <input type="email" wire:model="email"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                               required>
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Assign to Company *</label>
                        <select wire:model="selectedTenantId"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                                required>
                            @forelse($this->availableTenants as $tenant)
                            <option value="{{ $tenant->id }}">
                                {{ $tenant->name }}{{ $tenant->parent_id ? ' (Sub-tenant)' : '' }}
                            </option>
                            @empty
                            <option value="">No companies available</option>
                            @endforelse
                        </select>
                        @error('selectedTenantId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password {{ $selectedUser ? '(leave blank to keep)' : '*' }}</label>
                        <input type="password" wire:model="password"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                               {{ $selectedUser ? '' : 'required' }}>
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                        <select wire:model="role" 
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all">
                            <option value="user">User</option>
                            <option value="receptionist">Receptionist</option>
                            <option value="admin">Admin</option>
                            <option value="super-admin">Super Admin</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" wire:model="is_active" id="user_is_active" 
                               class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                        <label for="user_is_active" class="text-sm text-gray-700">Active</label>
                    </div>
                </div>
                
                <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <button type="button" wire:click="$set('showModal', false)" 
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-full hover:bg-brand-700 transition-colors shadow-sm">
                        {{ $selectedUser ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-data>
        <div class="fixed inset-0 bg-black/50" wire:click.self="$set('showDeleteModal', false)"></div>

        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-xl my-8">
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 flex-shrink-0">
                        <i class="fa-solid fa-trash text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Delete User</h3>
                        <p class="text-sm text-gray-500">This action cannot be undone.</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        Are you sure you want to delete <strong>{{ $selectedUser?->name }}</strong>? This will permanently remove the user and all associated data.
                    </p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                <button type="button" wire:click="$set('showDeleteModal', false)"
                        class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="confirmDelete"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 transition-colors">
                    Delete User
                </button>
            </div>
        </div>
    </div>
    @endif
</div>