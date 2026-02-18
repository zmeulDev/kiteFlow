<x-layouts.superadmin>
    @section('title', 'Manage Users')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">System Users</h2>
            <p class="text-slate-500 text-sm mt-1">Manage all users across all tenants.</p>
        </div>
        <button class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all active:scale-95">
            Create User
        </button>
    </div>

    @livewire('superadmin.user-list')
</x-layouts.superadmin>
