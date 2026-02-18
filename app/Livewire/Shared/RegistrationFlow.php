<?php

namespace App\Livewire\Shared;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RegistrationFlow extends Component
{
    public $company_name;
    public $admin_name;
    public $email;
    public $password;

    protected $rules = [
        'company_name' => 'required|string|min:3|max:255',
        'admin_name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
    ];

    public function register()
    {
        $this->validate();

        // 1. Create the Tenant
        $tenant = Tenant::create([
            'name' => $this->company_name,
            'slug' => Str::slug($this->company_name),
            'plan' => 'free',
            'status' => 'active',
        ]);

        // 2. Create the Admin User
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $this->admin_name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_super_admin' => false,
        ]);

        // 3. Log them in and redirect to dashboard
        Auth::login($user);
        session()->put('tenant_id', $tenant->id);

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.shared.registration-flow');
    }
}
