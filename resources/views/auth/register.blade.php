<x-guest-layout>
    @section('title', 'Create Account')

    @slot('header')
    <div class="auth-header">
        <h1 class="auth-title">Create account</h1>
        <p class="auth-subtitle">Get started with your free account</p>
    </div>
    @endslot

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        {{-- Name --}}
        <div class="auth-field">
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="John Doe" />
            <x-input-error :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div class="auth-field">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Password --}}
        <div class="auth-field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimum 8 characters" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Confirm Password --}}
        <div class="auth-field">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm your password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        {{-- Submit --}}
        <div class="auth-actions">
            <x-primary-button class="auth-btn">
                <span>{{ __('Create account') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <line x1="19" x2="19" y1="8" y2="14"></line>
                    <line x1="22" x2="16" y1="11" y2="11"></line>
                </svg>
            </x-primary-button>
        </div>

        <div class="auth-switch">
            <span>{{ __('Already have an account?') }}</span>
            <a href="{{ route('login') }}" class="auth-link auth-link--primary">{{ __('Sign in') }}</a>
        </div>
    </form>
</x-guest-layout>