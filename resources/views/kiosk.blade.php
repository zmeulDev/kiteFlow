<x-kiosk-layout :entrance="$entrance" :backgroundColor="$entrance->kioskSetting?->background_color ?? '#ffffff'">
    <livewire:kiosk.welcome :entrance="$entrance" />
</x-kiosk-layout>