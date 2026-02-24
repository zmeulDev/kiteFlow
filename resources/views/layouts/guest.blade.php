<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Welcome') - {{ config('app.name', 'Visitor Tracker') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-body">
    <div class="auth-container">
        {{-- Background decoration --}}
        <div class="auth-bg">
            <div class="auth-bg-pattern"></div>
            <div class="auth-bg-gradient"></div>
        </div>

        {{-- Main content --}}
        <div class="auth-content">
            {{-- Logo --}}
            <a href="/" class="auth-logo">
                <svg class="auth-logo-icon" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="40" rx="12" fill="currentColor"/>
                    <path d="M12 14C12 12.8954 12.8954 12 14 12H26C27.1046 12 28 12.8954 28 14V26C28 27.1046 27.1046 28 26 28H14C12.8954 28 12 27.1046 12 26V14Z" stroke="white" stroke-width="2"/>
                    <circle cx="20" cy="20" r="4" fill="white"/>
                </svg>
                <span class="auth-logo-text">{{ config('app.name', 'Visitor Tracker') }}</span>
            </a>

            {{-- Card --}}
            <div class="auth-card">
                @yield('header')
                {{ $slot }}
            </div>

            {{-- Footer --}}
            <div class="auth-footer">
                <p>{{ config('app.name', 'Visitor Tracker') }} &copy; {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
