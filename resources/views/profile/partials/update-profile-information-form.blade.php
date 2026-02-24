<form method="post" action="{{ route('profile.update') }}" class="profile-form">
    @csrf
    @method('patch')

    <div class="profile-form-grid">
        <div class="profile-form-field">
            <label class="profile-form-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="profile-form-input" required autofocus autocomplete="name">
            @error('name') <p class="profile-form-error">{{ $message }}</p> @enderror
        </div>

        <div class="profile-form-field">
            <label class="profile-form-label">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="profile-form-input" required autocomplete="username">
            @error('email') <p class="profile-form-error">{{ $message }}</p> @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="profile-form-verify">
                <p class="profile-form-verify-text">Your email address is unverified.</p>
                <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="profile-form-verify-link">Click here to re-send the verification email.</button>
                </form>
                @if (session('status') === 'verification-link-sent')
                <p class="profile-form-verify-success">A new verification link has been sent to your email address.</p>
                @endif
            </div>
            @endif
        </div>
    </div>

    <div class="profile-form-footer">
        <button type="submit" class="profile-btn profile-btn--primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            Save Changes
        </button>

        @if (session('status') === 'profile-updated')
        <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="profile-form-saved">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            Saved successfully
        </p>
        @endif
    </div>
</form>
