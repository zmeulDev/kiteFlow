<?php

namespace App\Livewire\Mobile;

use App\Models\Company;
use App\Models\Visit;
use App\Models\Visitor;
use App\Services\VisitService;
use Livewire\Component;

class CheckIn extends Component
{
    public Visit $visit;

    public int $step = 1;

    // Visitor details
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public ?int $company_id = null;
    public string $new_company_name = '';

    // Visit details
    public string $host_name = '';
    public string $host_email = '';
    public string $purpose = '';

    // Consent
    public bool $gdpr_consent = false;
    public bool $nda_consent = false;

    // Signature
    public string $signature_data = '';

    public bool $completed = false;

    public function mount(Visit $visit): void
    {
        $this->visit = $visit;

        // Check if visit already has visitor (already filled)
        if ($visit->visitor_id) {
            $this->completed = $visit->status === 'checked_in';
        }
    }

    protected function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'host_name' => 'required|string|max:255',
            'host_email' => 'nullable|email|max:255',
            'purpose' => 'nullable|string|max:500',
            'gdpr_consent' => 'accepted',
        ];
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
                'host_name' => 'required|string|max:255',
                'host_email' => 'nullable|email|max:255',
                'purpose' => 'nullable|string|max:500',
            ]);
        } elseif ($this->step === 3) {
            $rules = ['gdpr_consent' => 'accepted'];
            $kioskSetting = $this->visit->entrance->kioskSetting;
            if ($kioskSetting?->show_nda) {
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

        // Create or update visitor
        $visitor = Visitor::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email ?: null,
            'phone' => $this->phone ?: null,
            'company_id' => $companyId,
        ]);

        // Update visit with visitor
        $this->visit->update([
            'visitor_id' => $visitor->id,
            'host_name' => $this->host_name,
            'host_email' => $this->host_email ?: null,
            'purpose' => $this->purpose ?: null,
        ]);

        // Handle consent and check-in
        $consentData = [
            'gdpr' => $this->gdpr_consent,
            'nda' => $this->nda_consent,
            'signature' => $this->signature_data ?: null,
        ];

        $visitService->checkIn($this->visit, $consentData);

        $this->completed = true;
    }

    public function render()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $kioskSetting = $this->visit->entrance->kioskSetting;

        return view('livewire.mobile.check-in', [
            'companies' => $companies,
            'kioskSetting' => $kioskSetting,
        ]);
    }
}