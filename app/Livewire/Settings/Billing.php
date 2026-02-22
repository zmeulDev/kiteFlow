<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use Livewire\Component;
use Livewire\WithPagination;

class Billing extends Component
{
    use WithPagination;

    public string $tab = 'subscriptions';
    public ?Tenant $selectedTenant = null;
    
    // Subscription management
    public string $plan = 'basic';
    public string $billing_cycle = 'monthly';
    public string $status = 'active';
    public string $contract_start = '';
    public string $contract_end = '';
    public float $monthly_price = 0;
    public float $yearly_price = 0;
    
    // Payment
    public string $payment_method = 'bank_transfer';
    public string $payment_status = 'paid';
    public string $invoice_notes = '';
    
    // Plans
    public array $plans = [
        ['id' => 'starter', 'name' => 'Starter', 'monthly' => 49, 'yearly' => 490, 'features' => ['Up to 100 visitors/month', '1 Building', '5 Users', 'Email Support']],
        ['id' => 'basic', 'name' => 'Basic', 'monthly' => 99, 'yearly' => 990, 'features' => ['Up to 500 visitors/month', '3 Buildings', '15 Users', 'Email & Chat Support']],
        ['id' => 'professional', 'name' => 'Professional', 'monthly' => 199, 'yearly' => 1990, 'features' => ['Unlimited visitors', '10 Buildings', '50 Users', 'Priority Support', 'API Access']],
        ['id' => 'enterprise', 'name' => 'Enterprise', 'monthly' => 499, 'yearly' => 4990, 'features' => ['Unlimited everything', 'Unlimited buildings', 'Unlimited users', '24/7 Phone Support', 'Custom integrations', 'Dedicated account manager']],
    ];

    public function mount(?int $tenantId = null): void
    {
        if ($tenantId) {
            $this->selectedTenant = Tenant::findOrFail($tenantId);
            $this->loadTenantBilling();
        }
    }

    protected function loadTenantBilling(): void
    {
        if (!$this->selectedTenant) return;
        
        $this->plan = $this->selectedTenant->subscription_plan ?? 'basic';
        $this->billing_cycle = $this->selectedTenant->billing_cycle ?? 'monthly';
        $this->status = $this->selectedTenant->status ?? 'active';
        $this->contract_start = $this->selectedTenant->contract_start_date?->format('Y-m-d') ?? '';
        $this->contract_end = $this->selectedTenant->contract_end_date?->format('Y-m-d') ?? '';
        $this->monthly_price = $this->selectedTenant->monthly_price ?? 0;
        $this->yearly_price = $this->selectedTenant->yearly_price ?? 0;
        $this->payment_status = $this->selectedTenant->payment_status ?? 'paid';
    }

    public function getTenantsProperty()
    {
        return Tenant::withCount(['users', 'visitors'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function selectTenant(int $tenantId): void
    {
        $this->selectedTenant = Tenant::findOrFail($tenantId);
        $this->loadTenantBilling();
    }

    public function updateSubscription(): void
    {
        if (!$this->selectedTenant) return;

        $planDetails = collect($this->plans)->firstWhere('id', $this->plan);
        
        $this->selectedTenant->update([
            'subscription_plan' => $this->plan,
            'billing_cycle' => $this->billing_cycle,
            'status' => $this->status,
            'monthly_price' => $this->billing_cycle === 'monthly' ? ($planDetails['monthly'] ?? 0) : 0,
            'yearly_price' => $this->billing_cycle === 'yearly' ? ($planDetails['yearly'] ?? 0) : 0,
            'contract_start_date' => $this->contract_start,
            'contract_end_date' => $this->contract_end,
            'payment_status' => $this->payment_status,
        ]);

        session()->flash('message', 'Subscription updated successfully.');
    }

    public function render()
    {
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only Super Admins can access this page.');
        }

        return view('livewire.settings.billing', [
            'tenants' => $this->tenants,
            'plans' => $this->plans,
        ])->layout('layouts.app');
    }
}