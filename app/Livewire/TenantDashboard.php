<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\Building;
use App\Models\MeetingRoom;
use App\Models\SubTenant;
use Illuminate\Support\Facades\Auth;

class TenantDashboard extends Component
{
    public ?Tenant $tenant;
    public string $tab = 'overview';
    public array $stats = [];
    
    // Settings form
    public string $name = '';
    public string $address = '';
    public string $city = '';
    public string $country = '';
    public string $phone = '';
    public string $email = '';
    public string $contact_person = '';
    public int $gdpr_retention_months = 6;
    public string $nda_text = '';
    public string $terms_text = '';

    public function mount()
    {
        $this->tenant = Auth::user()->tenant;
        $this->loadStats();
        $this->loadSettings();
    }

    public function loadStats()
    {
        if (!$this->tenant) return;

        $this->stats = [
            'total_visits_today' => $this->tenant->visits()
                ->whereDate('scheduled_start', today())->count(),
            'checked_in' => $this->tenant->visits()
                ->where('status', 'checked_in')->count(),
            'total_visitors' => $this->tenant->visitors()->count(),
            'total_rooms' => $this->tenant->meetingRooms()->count(),
            'total_buildings' => $this->tenant->buildings()->count(),
        ];
    }

    public function loadSettings()
    {
        if (!$this->tenant) return;

        $this->name = $this->tenant->name;
        $this->address = $this->tenant->address ?? '';
        $this->city = $this->tenant->city ?? '';
        $this->country = $this->tenant->country ?? '';
        $this->phone = $this->tenant->phone ?? '';
        $this->email = $this->tenant->email ?? '';
        $this->contact_person = $this->tenant->contact_person ?? '';
        $this->gdpr_retention_months = $this->tenant->gdpr_retention_months ?? 6;
        $this->nda_text = $this->tenant->nda_text ?? '';
        $this->terms_text = $this->tenant->terms_text ?? '';
    }

    public function saveSettings()
    {
        $this->tenant->update([
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'phone' => $this->phone,
            'email' => $this->email,
            'contact_person' => $this->contact_person,
            'gdpr_retention_months' => $this->gdpr_retention_months,
            'nda_text' => $this->nda_text,
            'terms_text' => $this->terms_text,
        ]);

        session()->flash('message', 'Settings saved successfully');
    }

    public function render()
    {
        return view('livewire.tenant-dashboard');
    }
}
