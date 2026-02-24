<div class="profile-form-footer">
    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="profile-btn profile-btn--danger">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"></polyline>
            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
        </svg>
        Delete Account
    </button>
</div>

<x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <div class="profile-modal">
        <div class="profile-modal-header">
            <div class="profile-modal-icon profile-modal-icon--danger">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <div>
                <h3 class="profile-modal-title">Delete Account</h3>
                <p class="profile-modal-subtitle">This action cannot be undone</p>
            </div>
        </div>

        <form method="post" action="{{ route('profile.destroy') }}" class="profile-modal-body">
            @csrf
            @method('delete')

            <p class="profile-modal-text">Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.</p>

            <div class="profile-form-field">
                <label class="profile-form-label">Password</label>
                <input type="password" name="password" class="profile-form-input" placeholder="Enter your password">
                @error('userDeletion.password') <p class="profile-form-error">{{ $message }}</p> @enderror
            </div>

            <div class="profile-modal-footer">
                <button type="button" x-on:click="$dispatch('close')" class="profile-btn profile-btn--secondary">Cancel</button>
                <button type="submit" class="profile-btn profile-btn--danger">Delete Account</button>
            </div>
        </form>
    </div>
</x-modal>
