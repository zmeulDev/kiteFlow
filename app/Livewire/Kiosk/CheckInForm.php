<?php

namespace App\Livewire\Kiosk;

use App\Models\Company;
use App\Models\Entrance;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Services\VisitService;
use Livewire\Component;
use Livewire\WithFileUploads;

class CheckInForm extends Component
{
    use WithFileUploads;

    public Entrance $entrance;
    public ?Visit $currentVisit = null;

    public int $step = 1;
    public int $totalSteps = 4;

    // Visitor details
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public ?int $company_id = null;
    public string $new_company_name = '';

    // Visit details
    public ?int $host_id = null;
    public string $purpose = '';

    // Consent
    public bool $gdpr_consent = false;
    public bool $nda_consent = false;

    // Signature and photo
    public string $signature_data = '';
    public $photo;

    public array $purposes = [
        'Meeting' => 'Meeting',
        'Interview' => 'Interview',
        'Delivery' => 'Delivery',
        'Maintenance' => 'Maintenance',
        'Event' => 'Event',
        'Training' => 'Training',
        'Consultation' => 'Consultation',
        'Other' => 'Other',
    ];

    public function mount(Entrance $entrance): void
    {
        $this->entrance = $entrance;
        $kioskSetting = $entrance->kioskSetting;

        // Adjust total steps based on kiosk settings
        $this->totalSteps = 4; // Visitor info, visit info, consent, signature/photo
        if (!$kioskSetting?->require_signature && !$kioskSetting?->require_photo) {
            $this->totalSteps = 3;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'host_id' => 'required|exists:users,id',
            'purpose' => 'required|string|max:500',
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
            $this->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
            ]);
        } elseif ($this->step === 2) {
            $this->validate([
                'host_id' => 'required|exists:users,id',
                'purpose' => 'required|string|max:500',
            ]);
        } elseif ($this->step === 3) {
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

        // Handle company
        $companyId = $this->company_id;
        if ($this->new_company_name && !$companyId) {
            $company = Company::create([
                'name' => $this->new_company_name,
                'is_active' => true,
            ]);
            $companyId = $company->id;
        }

        // Get host info
        $host = User::find($this->host_id);

        // Create visitor and visit
        $visitorData = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'company_id' => $companyId,
        ];

        $visitData = [
            'host_id' => $this->host_id,
            'host_name' => $host->name,
            'host_email' => $host->email,
            'purpose' => $this->purpose,
        ];

        $visit = $visitService->createVisit($visitorData, $visitData, $this->entrance);

        // Handle consent and check-in
        $consentData = [
            'gdpr' => $this->gdpr_consent,
            'nda' => $this->nda_consent,
            'signature' => $this->signature_data ?: null,
        ];

        $visitService->checkIn($visit, $consentData);

        // Load relationships for display
        $visit->load(['host', 'visitor', 'entrance.building']);

        $this->currentVisit = $visit;
        $this->step = $this->totalSteps + 1; // Success step
    }

    public function render()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $hosts = User::where('is_active', true)->orderBy('name')->get();

        return view('livewire.kiosk.check-in-form', [
            'companies' => $companies,
            'hosts' => $hosts,
        ]);
    }
}