<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use Livewire\Component;

class KioskSettings extends Component
{
    public ?int $tenantId = null;
    public array $settings = [];
    
    // Visitor fields
    public bool $require_first_name = true;
    public bool $require_last_name = true;
    public bool $require_email = false;
    public bool $require_phone = false;
    public bool $require_company = false;
    public bool $require_id_number = false;
    public bool $require_photo = false;
    public bool $require_purpose = true;
    public bool $require_host = true;
    
    // Check-in options
    public bool $allow_code_checkin = true;
    public bool $allow_manual_checkin = true;
    public bool $auto_select_host = false;
    public string $default_purpose = '';
    
    // GDPR / Terms
    public bool $require_gdpr = true;
    public string $gdpr_text = '';
    public bool $require_nda = false;
    public string $nda_text = '';
    
    // Notifications
    public bool $notify_host_email = true;
    public bool $notify_host_sms = false;
    public bool $notify_reception_email = false;
    
    // Display options
    public string $welcome_message = '';
    public string $company_logo = '';
    public bool $show_company_branding = true;
    public string $theme = 'light';

    public function mount(?int $tenantId = null): void
    {
        $this->tenantId = $tenantId ?? auth()->user()?->getCurrentTenant()?->id;
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $kioskSettings = $tenant->getSetting('kiosk', []);
        
        // Visitor fields
        $this->require_first_name = $kioskSettings['require_first_name'] ?? true;
        $this->require_last_name = $kioskSettings['require_last_name'] ?? true;
        $this->require_email = $kioskSettings['require_email'] ?? false;
        $this->require_phone = $kioskSettings['require_phone'] ?? false;
        $this->require_company = $kioskSettings['require_company'] ?? false;
        $this->require_id_number = $kioskSettings['require_id_number'] ?? false;
        $this->require_photo = $kioskSettings['require_photo'] ?? false;
        $this->require_purpose = $kioskSettings['require_purpose'] ?? true;
        $this->require_host = $kioskSettings['require_host'] ?? true;
        
        // Check-in options
        $this->allow_code_checkin = $kioskSettings['allow_code_checkin'] ?? true;
        $this->allow_manual_checkin = $kioskSettings['allow_manual_checkin'] ?? true;
        $this->auto_select_host = $kioskSettings['auto_select_host'] ?? false;
        $this->default_purpose = $kioskSettings['default_purpose'] ?? '';
        
        // GDPR / Terms
        $this->require_gdpr = $kioskSettings['require_gdpr'] ?? true;
        $this->gdpr_text = $kioskSettings['gdpr_text'] ?? 'I consent to the processing of my personal data in accordance with the privacy policy.';
        $this->require_nda = $kioskSettings['require_nda'] ?? false;
        $this->nda_text = $kioskSettings['nda_text'] ?? '';
        
        // Notifications
        $this->notify_host_email = $kioskSettings['notify_host_email'] ?? true;
        $this->notify_host_sms = $kioskSettings['notify_host_sms'] ?? false;
        $this->notify_reception_email = $kioskSettings['notify_reception_email'] ?? false;
        
        // Display options
        $this->welcome_message = $kioskSettings['welcome_message'] ?? 'Welcome! Please check in.';
        $this->show_company_branding = $kioskSettings['show_company_branding'] ?? true;
        $this->theme = $kioskSettings['theme'] ?? 'light';
    }

    public function save(): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $kioskSettings = [
            // Visitor fields
            'require_first_name' => $this->require_first_name,
            'require_last_name' => $this->require_last_name,
            'require_email' => $this->require_email,
            'require_phone' => $this->require_phone,
            'require_company' => $this->require_company,
            'require_id_number' => $this->require_id_number,
            'require_photo' => $this->require_photo,
            'require_purpose' => $this->require_purpose,
            'require_host' => $this->require_host,
            
            // Check-in options
            'allow_code_checkin' => $this->allow_code_checkin,
            'allow_manual_checkin' => $this->allow_manual_checkin,
            'auto_select_host' => $this->auto_select_host,
            'default_purpose' => $this->default_purpose,
            
            // GDPR / Terms
            'require_gdpr' => $this->require_gdpr,
            'gdpr_text' => $this->gdpr_text,
            'require_nda' => $this->require_nda,
            'nda_text' => $this->nda_text,
            
            // Notifications
            'notify_host_email' => $this->notify_host_email,
            'notify_host_sms' => $this->notify_host_sms,
            'notify_reception_email' => $this->notify_reception_email,
            
            // Display options
            'welcome_message' => $this->welcome_message,
            'show_company_branding' => $this->show_company_branding,
            'theme' => $this->theme,
        ];

        $tenant->setSetting('kiosk', $kioskSettings);
        session()->flash('message', 'Kiosk settings saved successfully.');
    }

    public function render()
    {
        if (!auth()->user()->isSuperAdmin() && !auth()->user()->hasRole('admin')) {
            abort(403, 'Access denied. Only Admins can access this page.');
        }

        return view('livewire.settings.kiosk-settings')->layout('layouts.app');
    }
}