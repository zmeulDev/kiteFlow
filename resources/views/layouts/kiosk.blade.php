<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Visitor Check-in' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="kiosk-body antialiased">
        <div class="min-h-screen flex flex-col">
            @if(isset($entrance) && $entrance->kioskSetting)
                @if($entrance->kioskSetting->logo_path)
                    <div class="text-center pt-10 pb-4">
                        <div class="inline-flex items-center justify-center p-4 bg-surface rounded-2xl shadow-sm">
                            <img src="{{ asset('storage/' . $entrance->kioskSetting->logo_path) }}" alt="Logo" class="h-16">
                        </div>
                    </div>
                @endif
            @endif

            <main class="flex-1 flex items-center justify-center p-6">
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>