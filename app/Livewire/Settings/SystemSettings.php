<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use Livewire\Component;

class SystemSettings extends Component
{
    public ?int $tenantId = null;
    
    // Security
    public bool $require_2fa = false;
    public string $password_min_length = '8';
    public bool $password_require_uppercase = true;
    public bool $password_require_numbers = true;
    public bool $password_require_special = false;
    public int $session_timeout = 60;
    public int $max_login_attempts = 5;
    public int $lockout_duration = 15;
    
    // Data Retention
    public int $visitor_data_retention = 365;
    public int $meeting_data_retention = 730;
    public int $log_data_retention = 90;
    public bool $auto_delete_blacklisted = false;
    
    // Notifications
    public bool $notify_admin_new_user = true;
    public bool $notify_admin_new_tenant = true;
    public bool $email_notifications = true;
    public string $notification_email = '';
    
    // Branding
    public string $app_name = 'KiteFlow';
    public string $app_url = '';
    public bool $allow_tenant_branding = true;
    
    // Features
    public bool $enable_kiosk = true;
    public bool $enable_parking = false;
    public bool $enable_catering = false;

    public function mount(?int $tenantId = null): void
    {
        $this->tenantId = $tenantId ?? auth()->user()?->getCurrentTenant()?->id;
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        // Load super-admin global settings
        $settings = config('kiteflow.system_settings', []);
        
        // Security
        $this->require_2fa = $settings['require_2fa'] ?? false;
        $this->password_min_length = $settings['password_min_length'] ?? '8';
        $this->password_require_uppercase = $settings['password_require_uppercase'] ?? true;
        $this->password_require_numbers = $settings['password_require_numbers'] ?? true;
        $this->password_require_special = $settings['password_require_special'] ?? false;
        $this->session_timeout = $settings['session_timeout'] ?? 60;
        $this->max_login_attempts = $settings['max_login_attempts'] ?? 5;
        $this->lockout_duration = $settings['lockout_duration'] ?? 15;
        
        // Data Retention
        $this->visitor_data_retention = $settings['visitor_data_retention'] ?? 365;
        $this->meeting_data_retention = $settings['meeting_data_retention'] ?? 730;
        $this->log_data_retention = $settings['log_data_retention'] ?? 90;
        $this->auto_delete_blacklisted = $settings['auto_delete_blacklisted'] ?? false;
        
        // Notifications
        $this->notify_admin_new_user = $settings['notify_admin_new_user'] ?? true;
        $this->notify_admin_new_tenant = $settings['notify_admin_new_tenant'] ?? true;
        $this->email_notifications = $settings['email_notifications'] ?? true;
        $this->notification_email = $settings['notification_email'] ?? '';
        
        // Branding
        $this->app_name = $settings['app_name'] ?? 'KiteFlow';
        $this->app_url = $settings['app_url'] ?? config('app.url', '');
        $this->allow_tenant_branding = $settings['allow_tenant_branding'] ?? true;
        
        // Features
        $this->enable_kiosk = $settings['enable_kiosk'] ?? true;
        $this->enable_parking = $settings['enable_parking'] ?? false;
        $this->enable_catering = $settings['enable_catering'] ?? false;
    }

    public function save(): void
    {
        $settings = [
            // Security
            'require_2fa' => $this->require_2fa,
            'password_min_length' => $this->password_min_length,
            'password_require_uppercase' => $this->password_require_uppercase,
            'password_require_numbers' => $this->password_require_numbers,
            'password_require_special' => $this->password_require_special,
            'session_timeout' => $this->session_timeout,
            'max_login_attempts' => $this->max_login_attempts,
            'lockout_duration' => $this->lockout_duration,
            
            // Data Retention
            'visitor_data_retention' => $this->visitor_data_retention,
            'meeting_data_retention' => $this->meeting_data_retention,
            'log_data_retention' => $this->log_data_retention,
            'auto_delete_blacklisted' => $this->auto_delete_blacklisted,
            
            // Notifications
            'notify_admin_new_user' => $this->notify_admin_new_user,
            'notify_admin_new_tenant' => $this->notify_admin_new_tenant,
            'email_notifications' => $this->email_notifications,
            'notification_email' => $this->notification_email,
            
            // Branding
            'app_name' => $this->app_name,
            'app_url' => $this->app_url,
            'allow_tenant_branding' => $this->allow_tenant_branding,
            
            // Features
            'enable_kiosk' => $this->enable_kiosk,
            'enable_parking' => $this->enable_parking,
            'enable_catering' => $this->enable_catering,
        ];
        
        // Save to config file or database
        file_put_contents(
            base_path('config/kiteflow.php'),
            "<?php\n\nreturn " . var_export(['system_settings' => $settings], true) . ";\n"
        );
        
        // Update runtime config
        config(['kiteflow.system_settings' => $settings]);
        
        session()->flash('message', 'System settings saved successfully.');
    }

    public function render()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only Super Admins can access this page.');
        }

        return view('livewire.settings.system-settings')->layout('layouts.app');
    }
}