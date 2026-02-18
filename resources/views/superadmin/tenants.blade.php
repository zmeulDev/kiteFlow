<!-- resources/views/superadmin/tenants.blade.php -->
<x-layouts.superadmin>
    @section('title', 'Manage Tenants')

    <div class="flex justify-end mb-8">
        @livewire('superadmin.tenant-registration')
    </div>

    @livewire('superadmin.tenant-list')
</x-layouts.superadmin>
