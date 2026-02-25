<?php

namespace App\Livewire\Kiosk;

use App\Models\Entrance;
use App\Models\Visit;
use App\Services\VisitService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.kiosk')]
class ScheduledCheckIn extends Component
{
    public Entrance $entrance;
    public Visit $visit;

    public int $step = 1;
    public int $totalSteps = 2;

    // Consent
    public bool $gdpr_consent = false;
    public bool $nda_consent = false;

    // Signature
    public string $signature_data = '';

    public bool $checkedIn = false;

    public function mount(Entrance $entrance, Visit $visit): void
    {
        $this->entrance = $entrance;
        $this->visit = $visit;

        // Ensure the visit belongs to this entrance and is pending
        if ($visit->entrance_id !== $entrance->id || $visit->status !== 'pending') {
            abort(404, 'Visit not found or already processed.');
        }

        // Adjust steps based on kiosk settings
        $kioskSetting = $entrance->kioskSetting;
        if ($kioskSetting?->require_signature || $kioskSetting?->require_photo) {
            $this->totalSteps = 2; // Consent + Signature
        } else {
            $this->totalSteps = 1; // Just consent
        }
    }

    protected function rules(): array
    {
        $rules = [
            'gdpr_consent' => 'accepted',
            'signature_data' => 'nullable|string',
        ];

        if ($this->entrance->kioskSetting?->show_nda) {
            $rules['nda_consent'] = 'accepted';
        }

        return $rules;
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $rules = ['gdpr_consent' => 'accepted'];
            if ($this->entrance->kioskSetting?->show_nda) {
                $rules['nda_consent'] = 'accepted';
            }
            $this->validate($rules);
        }

        $this->step++;
    }

    public function previousStep(): void
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submit(VisitService $visitService): void
    {
        $this->validate();

        // Handle consent and check-in
        $consentData = [
            'gdpr' => $this->gdpr_consent,
            'nda' => $this->nda_consent,
            'signature' => $this->signature_data ?: null,
        ];

        $visitService->checkIn($this->visit, $consentData);

        // Load relationships for display
        $this->visit->load(['host', 'visitor', 'entrance.building']);

        $this->checkedIn = true;
    }

    public function render()
    {
        return view('livewire.kiosk.scheduled-check-in', [
            'entrance' => $this->entrance,
            'kioskSetting' => $this->entrance->kioskSetting,
        ]);
    }
}