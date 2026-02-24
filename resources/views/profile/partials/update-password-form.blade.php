<form method="post" action="{{ route('password.update') }}" class="profile-form">
    @csrf
    @method('put')

    <div class="profile-form-grid">
        <div class="profile-form-field profile-form-field--full">
            <label class="profile-form-label">Current Password</label>
            <input type="password" name="current_password" class="profile-form-input" autocomplete="current-password">
            @error('updatePassword.current_password') <p class="profile-form-error">{{ $message }}</p> @enderror
        </div>

        <div class="profile-form-field">
            <label class="profile-form-label">New Password</label>
            <input type="password" name="password" class="profile-form-input" autocomplete="new-password">
            @error('updatePassword.password') <p class="profile-form-error">{{ $message }}</p> @enderror
        </div>

        <div class="profile-form-field">
            <label class="profile-form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" class="profile-form-input" autocomplete="new-password">
            @error('updatePassword.password_confirmation') <p class="profile-form-error">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="profile-form-footer">
        <button type="submit" class="profile-btn profile-btn--primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
            </svg>
            Update Password
        </button>

        @if (session('status') === 'password-updated')
        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="profile-form-saved">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            Password updated
        </p>
        @endif
    </div>
</form>
