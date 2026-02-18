<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use App\Models\Tenant;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class TenantEditor extends Component
{
    public $tenantId;
    public $name, $slug, $plan, $status, $subscription_ends_at, $is_hub;
    public $contact_name, $contact_email, $contact_phone;
    public $isOpen = false;

    #[On('editTenant')]
    public function editTenant($id)
    {
        $tenant = Tenant::findOrFail($id);
        $this->tenantId = $tenant->id;
        $this->name = $tenant->name;
        $this->slug = $tenant->slug;
        $this->plan = $tenant->plan;
        $this->status = $tenant->status;
        $this->is_hub = $tenant->is_hub;
        $this->contact_name = $tenant->contact_name;
        $this->contact_email = $tenant->contact_email;
        $this->contact_phone = $tenant->contact_phone;
        $this->subscription_ends_at = $tenant->subscription_ends_at?->format('Y-m-d');
        $this->isOpen = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|min:3',
            'slug' => 'required|string',
            'plan' => 'required|in:free,pro,enterprise',
            'status' => 'required|in:active,suspended',
            'is_hub' => 'boolean',
            'contact_email' => 'nullable|email',
        ]);

        Tenant::where('id', $this->tenantId)->update([
            'name' => $this->name,
            'slug' => Str::slug($this->slug),
            'plan' => $this->plan,
            'status' => $this->status,
            'is_hub' => $this->is_hub,
            'contact_name' => $this->contact_name,
            'contact_email' => $this->contact_email,
            'contact_phone' => $this->contact_phone,
            'subscription_ends_at' => $this->subscription_ends_at,
        ]);

        $this->isOpen = false;
        $this->dispatch('tenantUpdated');
        $this->dispatch('notify', type: 'success', message: 'Tenant updated successfully.');
    }

    public function render()
    {
        return view('livewire.superadmin.tenant-editor');
    }
}
