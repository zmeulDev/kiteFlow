<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;

class Support extends Component
{
    use WithPagination;

    public string $tab = 'tickets';
    public string $status_filter = '';
    public string $search = '';
    
    // Demo tickets for display
    public array $tickets = [
        ['id' => 1, 'subject' => 'Cannot access kiosk mode', 'tenant' => 'Acme Corp', 'status' => 'open', 'priority' => 'high', 'created_at' => '2026-02-20'],
        ['id' => 2, 'subject' => 'Need to add more users', 'tenant' => 'TechStart Inc', 'status' => 'in_progress', 'priority' => 'medium', 'created_at' => '2026-02-19'],
        ['id' => 3, 'subject' => 'Billing question', 'tenant' => 'GlobalTech', 'status' => 'resolved', 'priority' => 'low', 'created_at' => '2026-02-18'],
    ];

    public array $faqs = [
        ['question' => 'How do I create a new tenant?', 'answer' => 'Go to Settings > Tenants > Add Tenant. Fill in the required details and click Create.'],
        ['question' => 'How do visitors check in?', 'answer' => 'Visitors can check in using the Kiosk URL or by entering a meeting code. You can configure check-in options in Kiosk Settings.'],
        ['question' => 'Can I integrate with Google Calendar?', 'answer' => 'Yes! Go to Settings > Integrations > Calendar to connect Google Calendar or Microsoft Outlook.'],
        ['question' => 'How do I add sub-tenants?', 'answer' => 'Go to Settings > Tenants, click on a tenant, then go to the Sub-tenants tab to add new sub-tenants.'],
    ];

    public function getFilteredTicketsProperty()
    {
        $tickets = collect($this->tickets);
        
        if ($this->status_filter) {
            $tickets = $tickets->where('status', $this->status_filter);
        }
        
        if ($this->search) {
            $tickets = $tickets->filter(function ($ticket) {
                return stripos($ticket['subject'], $this->search) !== false 
                    || stripos($ticket['tenant'], $this->search) !== false;
            });
        }
        
        return $tickets;
    }

    public function render()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only Super Admins can access this page.');
        }

        return view('livewire.settings.support', [
            'tickets' => $this->filteredTickets,
            'faqs' => $this->faqs,
        ])->layout('layouts.app');
    }
}