<x-kiosk-layout :entrance="$entrance" :backgroundColor="$entrance->kioskSetting?->background_color ?? '#ffffff'">
    <x-slot name="title">Complete Check-in</x-slot>

    <livewire:kiosk.scheduled-check-in :entrance="$entrance" :visit="$visit" />
</x-kiosk-layout>
