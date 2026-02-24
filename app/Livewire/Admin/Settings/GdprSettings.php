<?php

namespace App\Livewire\Admin\Settings;

use App\Models\KioskSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class GdprSettings extends Component
{
    public string $default_gdpr_text = '';
    public bool $require_gdpr_consent = true;

    protected function rules(): array
    {
        return [
            'default_gdpr_text' => 'required|string',
            'require_gdpr_consent' => 'boolean',
        ];
    }

    public function mount(): void
    {
        $firstKiosk = KioskSetting::first();
        $this->default_gdpr_text = $firstKiosk?->gdpr_text ?? 'I consent to the collection and processing of my personal data for the purpose of visitor management, in accordance with the General Data Protection Regulation (GDPR).';
        $this->require_gdpr_consent = true;
    }

    public function save(): void
    {
        $this->validate();

        KioskSetting::query()->update([
            'gdpr_text' => $this->default_gdpr_text,
        ]);

        session()->flash('message', 'GDPR settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.gdpr-settings');
    }
}