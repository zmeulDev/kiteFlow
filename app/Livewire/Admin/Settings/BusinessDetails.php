<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class BusinessDetails extends Component
{
    public string $business_name = '';
    public string $business_address = '';
    public string $business_phone = '';
    public string $business_email = '';

    protected function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'business_address' => 'nullable|string|max:500',
            'business_phone' => 'nullable|string|max:50',
            'business_email' => 'nullable|email|max:255',
        ];
    }

    public function mount(): void
    {
        $this->business_name = Setting::get('business_name', '');
        $this->business_address = Setting::get('business_address', '');
        $this->business_phone = Setting::get('business_phone', '');
        $this->business_email = Setting::get('business_email', '');
    }

    public function save(): void
    {
        $this->validate();

        Setting::set('business_name', $this->business_name);
        Setting::set('business_address', $this->business_address);
        Setting::set('business_phone', $this->business_phone);
        Setting::set('business_email', $this->business_email);

        session()->flash('message', 'Business details updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.business-details');
    }
}