<?php

namespace App\Livewire\Admin\Settings;

use App\Models\KioskSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class NdaSettings extends Component
{
    public string $default_nda_text = '';
    public bool $show_nda_globally = false;

    protected function rules(): array
    {
        return [
            'default_nda_text' => 'nullable|string',
            'show_nda_globally' => 'boolean',
        ];
    }

    public function mount(): void
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $firstKiosk = KioskSetting::first();
        $this->default_nda_text = $firstKiosk?->nda_text ?? 'I agree to maintain the confidentiality of any proprietary information I may encounter during my visit.';
        $this->show_nda_globally = KioskSetting::where('show_nda', true)->exists();
    }

    public function save(): void
    {
        $this->validate();

        KioskSetting::query()->update([
            'nda_text' => $this->default_nda_text,
            'show_nda' => $this->show_nda_globally,
        ]);

        session()->flash('message', 'NDA settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.admin.settings.nda-settings');
    }
}