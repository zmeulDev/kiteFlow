<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class ProfileManager extends Component
{
    public $name;
    public $email;
    public $current_password;
    public $new_password;

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Profile updated successfully.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'new_password' => 'required|string|min:8',
        ]);

        auth()->user()->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->reset(['current_password', 'new_password']);
        $this->dispatch('notify', type: 'success', message: 'Password updated successfully.');
    }

    public function render()
    {
        return view('livewire.dashboard.profile-manager');
    }
}
