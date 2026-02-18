<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TenantShow extends Component
{
    public $tenantId;
    
    // Form fields for easy tracking
    public $name, $is_hub, $slug, $plan, $status, $subscription_ends_at;
    public $contact_name, $contact_email, $contact_phone;
    public $billing_address, $vat_id, $contract_notes, $monthly_rate;

    public function mount($id)
    {
        $tenant = Tenant::findOrFail($id);
        $this->tenantId = $tenant->id;
        $this->fillForm($tenant);
    }

    public function fillForm($tenant)
    {
        $this->name = $tenant->name;
        $this->is_hub = $tenant->is_hub;
        $this->slug = $tenant->slug;
        $this->plan = $tenant->plan;
        $this->status = $tenant->status;
        $this->subscription_ends_at = $tenant->subscription_ends_at?->format('Y-m-d');
        $this->contact_name = $tenant->contact_name;
        $this->contact_email = $tenant->contact_email;
        $this->contact_phone = $tenant->contact_phone;
        $this->billing_address = $tenant->billing_address;
        $this->vat_id = $tenant->vat_id;
        $this->contract_notes = $tenant->contract_notes;
        $this->monthly_rate = $tenant->monthly_rate;
    }

    public function getTenantProperty()
    {
        return Tenant::with(['users', 'locations', 'meetingRooms', 'visits'])->findOrFail($this->tenantId);
    }

    public function impersonate()
    {
        $tenant = $this->getTenantProperty();
        $admin = $tenant->users()->first();
        if ($admin) {
            session()->put('impersonator_id', auth()->id());
            Auth::login($admin);
            session()->put('tenant_id', $tenant->id);
            return redirect()->route('dashboard');
        }
        
        $this->dispatch('notify', type: 'error', message: 'No admin user found.');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'slug' => 'required|string',
            'plan' => 'required|in:free,pro,enterprise',
            'status' => 'required|in:active,suspended',
            'contact_email' => 'nullable|email',
        ]);

        Tenant::where('id', $this->tenantId)->update([
            'name' => $this->name,
            'is_hub' => (bool)$this->is_hub,
            'slug' => Str::slug($this->slug),
            'plan' => $this->plan,
            'status' => $this->status,
            'subscription_ends_at' => $this->subscription_ends_at,
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'billing_address' => $this->billing_address,
            'vat_id' => $this->vat_id,
            'contract_notes' => $this->contract_notes,
            'monthly_rate' => $this->monthly_rate,
        ]);

        $this->dispatch('notify', type: 'success', message: 'Business profile saved successfully.');
    }

    public function render()
    {
        return view('livewire.superadmin.tenant-show', [
            'tenant' => $this->getTenantProperty()
        ])->layout('components.layouts.superadmin');
    }
}
