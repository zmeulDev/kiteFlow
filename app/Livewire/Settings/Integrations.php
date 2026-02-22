<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use Livewire\Component;

class Integrations extends Component
{
    public ?int $tenantId = null;
    public array $integrations = [];
    
    // Google Calendar
    public bool $google_enabled = false;
    public string $google_client_id = '';
    public string $google_client_secret = '';
    public bool $google_auto_sync = false;
    
    // Microsoft Outlook
    public bool $microsoft_enabled = false;
    public string $microsoft_client_id = '';
    public string $microsoft_client_secret = '';
    
    // Slack
    public bool $slack_enabled = false;
    public string $slack_webhook_url = '';
    public string $slack_channel = '';
    public bool $slack_notify_checkin = false;
    public bool $slack_notify_meeting = false;
    
    // Zapier
    public bool $zapier_enabled = false;
    public string $zapier_webhook_url = '';
    
    // Access Control
    public bool $access_control_enabled = false;
    public string $access_control_type = 'none';
    public string $access_control_api_key = '';
    public string $access_control_endpoint = '';
    
    // Webhooks
    public array $webhooks = [];

    public function mount(?int $tenantId = null): void
    {
        $this->tenantId = $tenantId ?? auth()->user()?->getCurrentTenant()?->id;
        $this->loadIntegrations();
    }

    protected function loadIntegrations(): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $settings = $tenant->getSetting('integrations', []);
        
        // Google
        $this->google_enabled = $settings['google']['enabled'] ?? false;
        $this->google_client_id = $settings['google']['client_id'] ?? '';
        $this->google_client_secret = $settings['google']['client_secret'] ?? '';
        $this->google_auto_sync = $settings['google']['auto_sync'] ?? false;
        
        // Microsoft
        $this->microsoft_enabled = $settings['microsoft']['enabled'] ?? false;
        $this->microsoft_client_id = $settings['microsoft']['client_id'] ?? '';
        $this->microsoft_client_secret = $settings['microsoft']['client_secret'] ?? '';
        
        // Slack
        $this->slack_enabled = $settings['slack']['enabled'] ?? false;
        $this->slack_webhook_url = $settings['slack']['webhook_url'] ?? '';
        $this->slack_channel = $settings['slack']['channel'] ?? '';
        $this->slack_notify_checkin = $settings['slack']['notify_checkin'] ?? false;
        $this->slack_notify_meeting = $settings['slack']['notify_meeting'] ?? false;
        
        // Zapier
        $this->zapier_enabled = $settings['zapier']['enabled'] ?? false;
        $this->zapier_webhook_url = $settings['zapier']['webhook_url'] ?? '';
        
        // Access Control
        $this->access_control_enabled = $settings['access_control']['enabled'] ?? false;
        $this->access_control_type = $settings['access_control']['type'] ?? 'none';
        $this->access_control_api_key = $settings['access_control']['api_key'] ?? '';
        $this->access_control_endpoint = $settings['access_control']['endpoint'] ?? '';
        
        // Webhooks
        $this->webhooks = $settings['webhooks'] ?? [];
    }

    public function save(): void
    {
        $tenant = Tenant::find($this->tenantId);
        if (!$tenant) return;

        $settings = [
            'google' => [
                'enabled' => $this->google_enabled,
                'client_id' => $this->google_client_id,
                'client_secret' => $this->google_client_secret,
                'auto_sync' => $this->google_auto_sync,
            ],
            'microsoft' => [
                'enabled' => $this->microsoft_enabled,
                'client_id' => $this->microsoft_client_id,
                'client_secret' => $this->microsoft_client_secret,
            ],
            'slack' => [
                'enabled' => $this->slack_enabled,
                'webhook_url' => $this->slack_webhook_url,
                'channel' => $this->slack_channel,
                'notify_checkin' => $this->slack_notify_checkin,
                'notify_meeting' => $this->slack_notify_meeting,
            ],
            'zapier' => [
                'enabled' => $this->zapier_enabled,
                'webhook_url' => $this->zapier_webhook_url,
            ],
            'access_control' => [
                'enabled' => $this->access_control_enabled,
                'type' => $this->access_control_type,
                'api_key' => $this->access_control_api_key,
                'endpoint' => $this->access_control_endpoint,
            ],
            'webhooks' => $this->webhooks,
        ];

        $tenant->setSetting('integrations', $settings);
        session()->flash('message', 'Integration settings saved successfully.');
    }

    public function addWebhook(): void
    {
        $this->webhooks[] = [
            'url' => '',
            'event' => 'visitor.checkin',
            'active' => true,
        ];
    }

    public function removeWebhook(int $index): void
    {
        unset($this->webhooks[$index]);
        $this->webhooks = array_values($this->webhooks);
    }

    public function render()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only Super Admins can access this page.');
        }

        return view('livewire.settings.integrations')->layout('layouts.app');
    }
}