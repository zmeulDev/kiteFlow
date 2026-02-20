<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'VisiFlow Enterprise' }}</title>
    
    <!-- Vite injected Tailwind v4 and JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col">
    <main class="flex-grow">
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
