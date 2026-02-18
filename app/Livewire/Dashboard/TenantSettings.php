<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\Location;
use Illuminate\Support\Str;

class TenantSettings extends Component
{
    public $name;
    public $primary_color;
    public $require_photo;
    public $terms_text;
    public $locations;
    public $new_location_name;

    public function mount()
    {
        $tenant = auth()->user()->tenant;
        $this->name = $tenant->name;
        $this->primary_color = $tenant->settings['primary_color'] ?? '#4f46e5';
        $this->require_photo = $tenant->settings['require_photo'] ?? false;
        $this->terms_text = $tenant->settings['terms_text'] ?? 'I agree to the Terms and Conditions and Privacy Policy.';
        $this->loadLocations();
    }

    public function loadLocations()
    {
        $this->locations = auth()->user()->tenant->locations()->get();
    }

    public function addLocation()
    {
        $this->validate(['new_location_name' => 'required|string|min:2']);
        
        $location = auth()->user()->tenant->locations()->create([
            'name' => $this->new_location_name,
            'slug' => Str::slug($this->new_location_name),
        ]);

        $this->new_location_name = '';
        $this->locations = auth()->user()->tenant->locations()->get();
        $this->dispatch('notify', type: 'success', message: 'Zone added.');
        $this->dispatch('location-added');
    }

    public function deleteLocation($id)
    {
        $location = auth()->user()->tenant->locations()->findOrFail($id);
        $location->delete();
        $this->locations = auth()->user()->tenant->locations()->get();
        $this->dispatch('notify', type: 'success', message: 'Zone removed.');
        $this->dispatch('location-added'); // Refresh list elsewhere too
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'primary_color' => 'required|string|max:7',
            'terms_text' => 'required|string',
        ]);

        $tenant = auth()->user()->tenant;
        $tenant->update([
            'name' => $this->name,
            'settings' => array_merge($tenant->settings ?? [], [
                'primary_color' => $this->primary_color,
                'require_photo' => $this->require_photo,
                'terms_text' => $this->terms_text,
            ]),
        ]);

        $this->dispatch('notify', type: 'success', message: 'Settings updated successfully!');
        $this->dispatch('settings-updated');
    }

    public function render()
    {
        return view('livewire.dashboard.tenant-settings');
    }
}
