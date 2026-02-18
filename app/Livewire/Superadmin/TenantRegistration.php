<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantRegistration extends Component
{
    public $company_name;
    public $slug;
    public $plan = 'free';
    
    public $admin_name;
    public $admin_email;
    public $admin_password;

    public $isOpen = false;

    protected $rules = [
        'company_name' => 'required|string|min:3|max:255',
        'slug' => 'required|string|unique:tenants,slug',
        'plan' => 'required|in:free,pro,enterprise',
        'admin_name' => 'required|string|max:255',
        'admin_email' => 'required|email|unique:users,email',
        'admin_password' => 'required|string|min:8',
    ];

    public function updatedCompanyName($value)
    {
        $this->slug = Str::slug($value);
    }

    public function register()
    {
        $this->validate();

        // 1. Create the Tenant
        $tenant = Tenant::create([
            'name' => $this->company_name,
            'slug' => $this->slug,
            'plan' => $this->plan,
            'status' => 'active',
        ]);

        // 2. Create the Admin User for this Tenant
        User::create([
            'tenant_id' => $tenant->id,
            'name' => $this->admin_name,
            'email' => $this->admin_email,
            'password' => Hash::make($this->admin_password),
            'is_super_admin' => false,
        ]);

        $this->reset();
        $this->isOpen = false;
        
        $this->dispatch('tenantUpdated');
        $this->dispatch('notify', type: 'success', message: 'New tenant registered successfully.');
    }

    public function render()
    {
        return view('livewire.superadmin.tenant-registration');
    }
}
