<div class="space-y-6">
    <!-- Notifications -->
    <x-app-toast />

    <div class="flex items-center justify-between">
        <h3 class="text-xl font-bold text-slate-900">All Users ({{ $users->total() }})</h3>
        <button wire:click="createUser" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-500/30">
            + New User
        </button>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search users..." class="w-full border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tenant</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Joined</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                @if($user->tenant)
                                    <span class="text-sm text-slate-600 font-medium">{{ $user->tenant->name }}</span>
                                @else
                                    <span class="text-xs text-slate-400 italic">None</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="editUser({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm">Edit</button>
                                <button wire:click="confirmDelete({{ $user->id }})" class="text-rose-600 hover:text-rose-900 font-medium text-sm">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>

    <!-- User Modal -->
    @if($showUserModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
            <h3 class="text-lg font-bold mb-4">{{ $editingUserId ? 'Edit User' : 'New User' }}</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700">Name</label>
                    <input wire:model="name" type="text" class="w-full mt-1 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700">Email</label>
                    <input wire:model="email" type="email" class="w-full mt-1 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    @error('email') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700">Password {{ $editingUserId ? '(Leave blank to keep current)' : '' }}</label>
                    <input wire:model="password" type="password" class="w-full mt-1 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                    @error('password') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Roles</label>
                    <div class="space-y-2 max-h-40 overflow-y-auto border border-slate-100 rounded-xl p-3">
                        @foreach($roles as $role)
                            <label class="flex items-center space-x-3 cursor-pointer p-1 hover:bg-slate-50 rounded-lg">
                                <input wire:model="selectedRoles" type="checkbox" value="{{ $role->name }}" class="rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                <span class="text-sm font-medium text-slate-700">{{ ucfirst($role->name) }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-8">
                <button wire:click="$set('showUserModal', false)" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg font-medium">Cancel</button>
                <button wire:click="saveUser" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold">Save User</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-100 flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl">⚠️</span>
            </div>
            <h3 class="text-lg font-bold mb-2">Delete User?</h3>
            <p class="text-sm text-slate-500 mb-6">Are you sure you want to delete this user? This action cannot be undone.</p>
            
            <div class="flex justify-center space-x-3">
                <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg font-medium">Cancel</button>
                <button wire:click="deleteConfirmed" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 font-bold">Delete</button>
            </div>
        </div>
    </div>
    @endif
</div>
