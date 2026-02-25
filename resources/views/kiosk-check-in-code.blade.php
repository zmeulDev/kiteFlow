<x-kiosk-layout :entrance="$entrance" :backgroundColor="$entrance->kioskSetting?->background_color ?? '#ffffff'">
    <x-slot name="title">Check-in Code</x-slot>

    <livewire:kiosk.check-in-code :entrance="$entrance" />
</x-kiosk-layout>
