<div class="space-y-6">
    <!-- Notifications -->
    <x-app-toast /> 

    <!-- Roles Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-slate-900">Roles</h3>
            <button wire:click="createRole" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-indigo-700 transition-all">
                + New Role
            </button>
        </div>

        <div class="space-y-4">
            @foreach($roles as $role)
                <div class="bg-white p-6 rounded-[24px] border border-slate-200 shadow-sm hover:shadow-md transition-all group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="h-10 w-10 rounded-xl bg-indigo-100 flex items-center justify-center text-xl">🛡️</div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-lg">{{ ucfirst($role->name) }}</h4>
                                <p class="text-xs text-slate-500 uppercase font-bold tracking-wider">{{ $role->guard_name }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                             <button wire:click="editRole({{ $role->id }})" class="px-3 py-1.5 text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">✏️ Edit</button>
                             <button wire:click="confirmDelete({{ $role->id }}, 'role')" class="px-3 py-1.5 text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-lg transition-colors">🗑️ Delete</button>
                        </div>
                    </div>
                    
                    <div class="border-t border-slate-100 pt-4 mt-4">
                        <p class="text-xs font-bold text-slate-400 uppercase mb-2">Permissions</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($permissions as $permission)
                                <button 
                                    wire:click="togglePermission({{ $role->id }}, '{{ $permission->name }}')"
                                    class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium border transition-all
                                    {{ $role->hasPermissionTo($permission->name) 
                                        ? 'bg-indigo-100 text-indigo-700 border-indigo-200 hover:bg-indigo-200' 
                                        : 'bg-slate-50 text-slate-400 border-slate-200 hover:border-indigo-300 hover:text-indigo-600' 
                                    }}">
                                    {{ $permission->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Permissions Section -->
    <div class="space-y-6 pt-8 border-t border-slate-200 mt-12">
         <div class="flex items-center justify-between">
            <h3 class="text-xl font-bold text-slate-900">Available Permissions</h3>
            <button wire:click="createPermission" class="px-4 py-2 bg-emerald-600 text-white rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-emerald-700 transition-all">
                + New Permission
            </button>
        </div>

        <div class="bg-white p-6 rounded-[24px] border border-slate-200 shadow-sm">
            <div class="flex flex-wrap gap-2">
                @foreach($permissions as $permission)
                    <div class="inline-flex items-center px-3 py-1.5 rounded-xl text-sm font-medium bg-slate-50 text-slate-700 border border-slate-200 group hover:border-indigo-300 transition-all">
                         <span class="mr-2">🔑</span> {{ $permission->name }}
                         <button wire:click="confirmDelete({{ $permission->id }}, 'permission')" class="ml-2 text-slate-400 hover:text-rose-500 opacity-0 group-hover:opacity-100 transition-opacity">×</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Role Modal -->
    @if($showRoleModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold mb-4">{{ $editingRoleId ? 'Edit Role' : 'New Role' }}</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700">Name</label>
                    <input wire:model="name" type="text" class="w-full mt-1 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500" autofocus>
                    @error('name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                 <div>
                    <label class="block text-sm font-bold text-slate-700">Guard</label>
                    <select wire:model="guard_name" class="w-full mt-1 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="web">web</option>
                        <option value="api">api</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end space-x-3 mt-6">
                <button wire:click="$set('showRoleModal', false)" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg font-medium">Cancel</button>
                <button wire:click="saveRole" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-bold">Save</button>
            </div>
        </div>
    </div>
    @endif

    <!-- Permission Modal -->
    @if($showPermissionModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
            <h3 class="text-lg font-bold mb-4">New Permission</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700">Name</label>
                    <input wire:model="name" type="text" placeholder="e.g., delete-posts" class="w-full mt-1 border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500" autofocus>
                     @error('name') <span class="text-xs text-rose-500">{{ $message }}</span> @enderror
                </div>
                 <div>
                    <label class="block text-sm font-bold text-slate-700">Guard</label>
                    <select wire:model="guard_name" class="w-full mt-1 border-slate-200 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="web">web</option>
                        <option value="api">api</option>
                    </select>
                </div>
            </div>
             <div class="flex justify-end space-x-3 mt-6">
                <button wire:click="$set('showPermissionModal', false)" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg font-medium">Cancel</button>
                <button wire:click="savePermission" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-bold">Save</button>
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
            <h3 class="text-lg font-bold mb-2">Delete {{ ucfirst($deleteType) }}?</h3>
            <p class="text-sm text-slate-500 mb-6">Are you sure you want to delete this {{ $deleteType }}? This action cannot be undone.</p>
            
            <div class="flex justify-center space-x-3">
                <button wire:click="$set('showDeleteModal', false)" class="px-4 py-2 text-slate-500 hover:bg-slate-100 rounded-lg font-medium">Cancel</button>
                <button wire:click="deleteConfirmed" class="px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 font-bold">Delete</button>
            </div>
        </div>
    </div>
    @endif
</div>
