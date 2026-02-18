<x-layouts.superadmin>
    @section('title', 'Manage RBAC')

    <div class="mb-8">
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Roles & Permissions</h2>
        <p class="text-slate-500 text-sm mt-1">Configure global access control policies.</p>
    </div>

    @livewire('superadmin.role-list')
</x-layouts.superadmin>
