<x-guest-layout>
    @section('title', 'Confirm Password')

    @slot('header')
    <div class="auth-header">
        <div class="auth-header-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            </svg>
        </div>
        <h1 class="auth-title">Confirm your password</h1>
        <p class="auth-subtitle">This is a secure area. Please confirm your password to continue.</p>
    </div>
    @endslot

    <form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
        @csrf

        {{-- Password --}}
        <div class="auth-field">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Submit --}}
        <div class="auth-actions">
            <x-primary-button class="auth-btn">
                <span>{{ __('Confirm') }}</span>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>