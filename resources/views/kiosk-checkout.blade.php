<x-kiosk-layout :entrance="$entrance" :backgroundColor="$entrance->kioskSetting?->background_color ?? '#ffffff'">
    <livewire:kiosk.check-out :entrance="$entrance" />
</x-kiosk-layout>