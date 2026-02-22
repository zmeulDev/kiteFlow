<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class SettingsPage extends Component
{
    public ?Tenant $tenant = null;
    
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $timezone = 'UTC';
    public string $locale = 'en';
    public string $currency = 'USD';
    public array $address = [];

    public function mount(): void
    {
        $this->tenant = auth()->user()?->getCurrentTenant();
        
        if ($this->tenant) {
            $this->fill([
                'name' => $this->tenant->name,
                'email' => $this->tenant->email,
                'phone' => $this->tenant->phone ?? '',
                'timezone' => $this->tenant->timezone,
                'locale' => $this->tenant->locale,
                'currency' => $this->tenant->currency,
                'address' => $this->tenant->address ?? [],
            ]);
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'timezone' => 'required|string|max:50',
            'locale' => 'required|string|max:10',
            'currency' => 'required|string|size:3',
        ]);

        if ($this->tenant) {
            $this->tenant->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'timezone' => $this->timezone,
                'locale' => $this->locale,
                'currency' => $this->currency,
                'address' => $this->address,
            ]);

            session()->flash('message', 'Settings updated successfully.');
        }
    }

    public function render()
    {
        // Allow both super-admins and tenant admins
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Access denied.');
        }

        return view('livewire.settings.settings-page')->layout('layouts.app');
    }
}