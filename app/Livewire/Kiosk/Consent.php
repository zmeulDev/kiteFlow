<?php

namespace App\Livewire\Kiosk;

use App\Models\Entrance;
use App\Models\Visit;
use Livewire\Component;

class Consent extends Component
{
    public Entrance $entrance;
    public Visit $visit;

    public bool $gdpr_consent = false;
    public bool $nda_consent = false;

    public string $gdprText = '';
    public string $ndaText = '';
    public bool $showNda = false;

    public function mount(Entrance $entrance, Visit $visit): void
    {
        $this->entrance = $entrance;
        $this->visit = $visit;

        $kioskSetting = $entrance->kioskSetting;
        $this->gdprText = $kioskSetting?->gdpr_text ?? 'I consent to the collection and processing of my personal data.';
        $this->ndaText = $kioskSetting?->nda_text ?? 'I agree to maintain the confidentiality of proprietary information.';
        $this->showNda = $kioskSetting?->show_nda ?? false;
    }

    public function submit(): void
    {
        $this->validate([
            'gdpr_consent' => 'accepted',
        ]);

        if ($this->showNda) {
            $this->validate([
                'nda_consent' => 'accepted',
            ]);
        }

        $this->dispatch('consent-given', [
            'gdpr' => $this->gdpr_consent,
            'nda' => $this->nda_consent,
        ]);
    }

    public function render()
    {
        return view('livewire.kiosk.consent');
    }
}