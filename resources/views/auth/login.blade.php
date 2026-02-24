<x-guest-layout>
    @section('title', 'Sign In')

    @slot('header')
    <div class="auth-header">
        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-subtitle">Sign in to your account to continue</p>
    </div>
    @endslot

    {{-- Session Status --}}
    @if (session('status'))
    <div class="auth-status auth-status--success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <span>{{ session('status') }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        {{-- Email --}}
        <div class="auth-field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Password --}}
        <div class="auth-field">
            <div class="auth-field-header">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">{{ __('Forgot password?') }}</a>
                @endif
            </div>
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Remember Me --}}
        <div class="auth-checkbox-group">
            <label for="remember_me" class="auth-checkbox-label">
                <input id="remember_me" type="checkbox" class="auth-checkbox" name="remember">
                <span class="auth-checkbox-text">{{ __('Remember me') }}</span>
            </label>
        </div>

        {{-- Submit --}}
        <div class="auth-actions">
            <x-primary-button class="auth-btn">
                <span>{{ __('Sign in') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14"></path>
                    <path d="m12 5 7 7-7 7"></path>
                </svg>
            </x-primary-button>
        </div>

        @if (Route::has('register'))
        <div class="auth-switch">
            <span>{{ __("Don't have an account?") }}</span>
            <a href="{{ route('register') }}" class="auth-link auth-link--primary">{{ __('Create account') }}</a>
        </div>
        @endif
    </form>
</x-guest-layout>