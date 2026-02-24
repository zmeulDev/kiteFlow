<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-main min-h-screen flex flex-col justify-center items-center">
        <div class="card text-center max-w-lg">
            <h1 class="text-4xl font-bold mb-4">
                {{ config('app.name', 'Laravel') }}
            </h1>

            <p class="text-secondary mb-6">
                Welcome to your Laravel application.
            </p>

            @auth
                <a href="{{ route('dashboard') }}" class="btn">
                    Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn">
                    Get Started
                </a>
            @endauth
        </div>
    </body>
</html>