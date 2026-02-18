<!-- projects/visiflow/resources/views/kiosk.blade.php -->
<x-kiosk-layout :tenant="\App\Models\Tenant::first()">
    @livewire('kiosk.kiosk-main')
</x-kiosk-layout>
