<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Mobile Check-in' }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="min-h-screen">
        <div class="min-h-screen flex flex-col">
            <main class="flex-1 p-4">
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
    </body>
</html>