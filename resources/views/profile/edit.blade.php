<x-admin-layout>
    <div class="profile-page">
        {{-- Header --}}
        <div class="profile-header">
            <div class="profile-header-content">
                <div class="profile-avatar profile-avatar--{{ Auth::user()->role }}">
                    {{ collect(explode(' ', Auth::user()->name))->map(fn($word) => strtoupper(substr($word, 0, 1)))->take(2)->join('') }}
                </div>
                <div>
                    <h1 class="profile-title">{{ Auth::user()->name }}</h1>
                    <p class="profile-subtitle">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="profile-role">
                <span class="profile-role-badge profile-role-badge--{{ Auth::user()->role }}">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
            </div>
        </div>

        {{-- Profile Cards --}}
        <div class="profile-grid">
            {{-- Profile Information --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    </div>
                    <div>
                        <h3 class="profile-card-title">Profile Information</h3>
                        <p class="profile-card-description">Update your account's profile information and email address.</p>
                    </div>
                </div>

                @include('profile.partials.update-profile-information-form')
            </div>

            {{-- Password --}}
            <div class="profile-card">
                <div class="profile-card-header">
                    <div class="profile-card-icon profile-card-icon--amber">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="profile-card-title">Update Password</h3>
                        <p class="profile-card-description">Ensure your account is using a long, random password to stay secure.</p>
                    </div>
                </div>

                @include('profile.partials.update-password-form')
            </div>

            {{-- Delete Account --}}
            <div class="profile-card profile-card--danger">
                <div class="profile-card-header">
                    <div class="profile-card-icon profile-card-icon--danger">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 class="profile-card-title">Delete Account</h3>
                        <p class="profile-card-description">Once your account is deleted, all of its resources and data will be permanently deleted.</p>
                    </div>
                </div>

                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-admin-layout>
