<div class="space-y-4 lg:space-y-6" wire:key="subtenant-management">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Sub-Tenants</h1>
            <p class="mt-1 text-sm text-gray-500">Manage your organization's sub-tenants</p>
        </div>
        <button wire:click="openCreateModal" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-full hover:bg-brand-700 transition-colors shadow-sm">
            <i class="fa-solid fa-plus"></i>
            <span>Add Sub-Tenant</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-100 p-4">
        <div class="relative">
            <i class="absolute left-4 top-1/2 -translate-y-1/2 fa-solid fa-magnifying-glass text-gray-400"></i>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Search sub-tenants by name or email..."
                   class="w-full pl-11 pr-4 py-3 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all">
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="lg:hidden space-y-3">
        @forelse($subtenants as $subtenant)
        <a href="{{ route('settings.subtenants.show', $subtenant->id) }}" class="block bg-white rounded-2xl border border-gray-100 p-4 hover:shadow-sm transition-shadow">
            <div class="flex items-start gap-3">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-semibold flex-shrink-0">
                    {{ strtoupper(substr($subtenant->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $subtenant->name }}</p>
                            <p class="text-xs text-gray-500">{{ $subtenant->email }}</p>
                        </div>
                        @if($subtenant->status === 'active')
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
                    <div class="mt-2 grid grid-cols-3 gap-2 text-xs text-gray-500">
                        <div><i class="fa-solid fa-user mr-1"></i> {{ $subtenant->users_count }} Users</div>
                        <div><i class="fa-solid fa-users mr-1"></i> {{ $subtenant->visitors_count }} Visitors</div>
                        <div><i class="fa-solid fa-calendar mr-1"></i> {{ $subtenant->meetings_count }} Meetings</div>
                    </div>
                    <div class="mt-3">
                        <button wire:click.stop="openEditModal({{ $subtenant->id }})"
                                class="w-full px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors text-center">
                            <i class="fa-solid fa-pen mr-1"></i> Edit
                        </button>
                        <button wire:click.stop="deleteSubtenant({{ $subtenant->id }})"
                                class="w-full mt-2 px-4 py-2 text-xs font-semibold text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-colors text-center">
                            <i class="fa-solid fa-trash mr-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </a>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-8 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <i class="fa-solid fa-building text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-semibold text-gray-900">No sub-tenants found</p>
            <p class="text-sm text-gray-500 mt-1">Create your first sub-tenant</p>
        </div>
        @endforelse

        @if($subtenants->hasPages())
        <div class="bg-white rounded-2xl border border-gray-100 p-4">
            <div class="flex items-center justify-between">
                <p class="text-xs text-gray-500">{{ $subtenants->firstItem() }}-{{ $subtenants->lastItem() }} of {{ $subtenants->total() }}</p>
                <div class="flex items-center gap-2">
                    @if($subtenants->onFirstPage())
                    <span class="p-2 text-gray-300"><i class="fa-solid fa-chevron-left text-xs"></i></span>
                    @else
                    <button wire:click="previousPage()" class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg">
                        <i class="fa-solid fa-chevron-left text-xs"></i>
                    </button>
                    @endif
                    @if($subtenants->hasMorePages())
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
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Sub-Tenant</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Users</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Visitors</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Meetings</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($subtenants as $subtenant)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ strtoupper(substr($subtenant->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('settings.subtenants.show', $subtenant->id) }}" class="text-sm font-semibold text-gray-900 hover:text-brand-600">
                                        {{ $subtenant->name }}
                                    </a>
                                    <p class="text-xs text-gray-400">{{ $subtenant->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 text-sm text-gray-700">
                                <i class="fa-solid fa-user text-gray-400 text-xs"></i>
                                {{ $subtenant->users_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 text-sm text-gray-700">
                                <i class="fa-solid fa-users text-gray-400 text-xs"></i>
                                {{ $subtenant->visitors_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 text-sm text-gray-700">
                                <i class="fa-solid fa-calendar text-gray-400 text-xs"></i>
                                {{ $subtenant->meetings_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($subtenant->status === 'active')
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
                                <button wire:click="openEditModal({{ $subtenant->id }})"
                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <button wire:click="deleteSubtenant({{ $subtenant->id }})"
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
                                    <i class="fa-solid fa-building text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">No sub-tenants found</p>
                                <p class="text-sm text-gray-500 mt-1">Create your first sub-tenant</p>
                                <button wire:click="openCreateModal" class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-brand-600 bg-brand-50 rounded-xl hover:bg-brand-100 transition-colors">
                                    <i class="fa-solid fa-plus"></i>
                                    Add Sub-Tenant
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($subtenants->hasPages())
        <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">Showing {{ $subtenants->firstItem() }} to {{ $subtenants->lastItem() }} of {{ $subtenants->total() }}</p>
            {{ $subtenants->links() }}
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
                    <h2 class="text-lg font-semibold text-gray-900">{{ $selectedSubtenant ? 'Edit Sub-Tenant' : 'Create Sub-Tenant' }}</h2>
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
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone</label>
                        <input type="text" wire:model="phone"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Slug *</label>
                        <input type="text" wire:model="slug"
                               class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all"
                               required>
                        @error('slug') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                        <select wire:model="status"
                                class="w-full px-4 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent focus:bg-white transition-all">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-100 bg-gray-50 rounded-b-2xl">
                    <button type="button" wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 text-sm font-semibold text-white bg-brand-600 rounded-full hover:bg-brand-700 transition-colors shadow-sm">
                        {{ $selectedSubtenant ? 'Update' : 'Create' }}
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
                        <h3 class="text-lg font-semibold text-gray-900">Delete Sub-Tenant</h3>
                        <p class="text-sm text-gray-500">This action cannot be undone.</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <p class="text-sm text-gray-700">
                        Are you sure you want to delete <strong>{{ $selectedSubtenant?->name }}</strong>? This will permanently remove the sub-tenant and all associated data including users, visitors, and meetings.
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
                    Delete Sub-Tenant
                </button>
            </div>
        </div>
    </div>
    @endif
</div>