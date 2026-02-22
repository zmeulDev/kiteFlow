<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Meeting;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Livewire\Component;
use Livewire\WithPagination;

class TenantDetail extends Component
{
    public ?Tenant $tenant = null;
    public string $tab = 'overview';
    
    // Overview fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $slug = '';
    public string $status = 'active';
    public string $domain = '';
    public string $timezone = 'UTC';
    public string $locale = 'en';
    public string $currency = 'USD';
    
    // Address
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $country = '';
    
    // Contract details
    public string $subscription_plan = 'basic';
    public string $billing_cycle = 'monthly';
    public float $monthly_price = 0;
    public float $yearly_price = 0;
    public string $contract_start_date = '';
    public string $contract_end_date = '';
    public string $payment_status = 'paid';
    public string $notes = '';
    
    public bool $showEditModal = false;
    public bool $showDeleteModal = false;
    public bool $showSubtenantModal = false;
    
    // Subtenant fields
    public ?Tenant $selectedSubtenant = null;
    public string $subtenant_name = '';
    public string $subtenant_email = '';
    public string $subtenant_phone = '';
    public string $subtenant_slug = '';
    public string $subtenant_status = 'active';

    public function mount(int $tenantId): void
    {
        $this->tenant = Tenant::with(['children', 'users', 'buildings'])->findOrFail($tenantId);
        $this->loadTenantData();
    }

    protected function loadTenantData(): void
    {
        $this->name = $this->tenant->name;
        $this->email = $this->tenant->email;
        $this->phone = $this->tenant->phone ?? '';
        $this->slug = $this->tenant->slug;
        $this->status = $this->tenant->status;
        $this->domain = $this->tenant->domain ?? '';
        $this->timezone = $this->tenant->timezone ?? 'UTC';
        $this->locale = $this->tenant->locale ?? 'en';
        $this->currency = $this->tenant->currency ?? 'USD';
        
        // Address
        $address = $this->tenant->address ?? [];
        $this->address_line1 = $address['line1'] ?? '';
        $this->address_line2 = $address['line2'] ?? '';
        $this->city = $address['city'] ?? '';
        $this->state = $address['state'] ?? '';
        $this->postal_code = $address['postal_code'] ?? '';
        $this->country = $address['country'] ?? '';
        
        // Contract
        $this->subscription_plan = $this->tenant->subscription_plan ?? 'basic';
        $this->billing_cycle = $this->tenant->billing_cycle ?? 'monthly';
        $this->monthly_price = $this->tenant->monthly_price ?? 0;
        $this->yearly_price = $this->tenant->yearly_price ?? 0;
        $this->contract_start_date = $this->tenant->contract_start_date?->format('Y-m-d') ?? '';
        $this->contract_end_date = $this->tenant->contract_end_date?->format('Y-m-d') ?? '';
        $this->payment_status = $this->tenant->payment_status ?? 'paid';
        $this->notes = $this->tenant->notes ?? '';
    }

    public function getSubtenantsProperty()
    {
        return $this->tenant->children()->withCount(['users', 'visitors', 'meetings'])->get();
    }

    public function getTenantUsersProperty()
    {
        return $this->tenant->users()->with('roles')->get();
    }

    public function getRecentVisitorsProperty()
    {
        return Visitor::where('tenant_id', $this->tenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getRecentMeetingsProperty()
    {
        return Meeting::where('tenant_id', $this->tenant->id)
            ->with(['host', 'meetingRoom'])
            ->orderBy('start_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getStatsProperty(): array
    {
        return [
            'total_users' => $this->tenant->users()->count(),
            'total_visitors' => Visitor::where('tenant_id', $this->tenant->id)->count(),
            'total_visits' => VisitorVisit::where('tenant_id', $this->tenant->id)->count(),
            'total_meetings' => Meeting::where('tenant_id', $this->tenant->id)->count(),
            'total_buildings' => $this->tenant->buildings()->count(),
            'subtenants_count' => $this->tenant->children()->count(),
            'checked_in_today' => VisitorVisit::where('tenant_id', $this->tenant->id)
                ->whereDate('check_in_at', today())
                ->whereNull('check_out_at')
                ->count(),
        ];
    }

    public function switchTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function openEditModal(): void
    {
        $this->loadTenantData();
        $this->showEditModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . $this->tenant->id,
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'slug' => $this->slug,
            'status' => $this->status,
            'domain' => $this->domain,
            'timezone' => $this->timezone,
            'locale' => $this->locale,
            'currency' => $this->currency,
            'address' => [
                'line1' => $this->address_line1,
                'line2' => $this->address_line2,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
            ],
            'subscription_plan' => $this->subscription_plan,
            'billing_cycle' => $this->billing_cycle,
            'monthly_price' => $this->monthly_price,
            'yearly_price' => $this->yearly_price,
            'contract_start_date' => $this->contract_start_date,
            'contract_end_date' => $this->contract_end_date,
            'payment_status' => $this->payment_status,
            'notes' => $this->notes,
        ];

        $this->tenant->update($data);
        $this->showEditModal = false;
        session()->flash('message', 'Tenant updated successfully.');
    }

    public function openSubtenantModal(?int $subtenantId = null): void
    {
        $this->resetSubtenantFields();
        
        if ($subtenantId) {
            $this->selectedSubtenant = Tenant::findOrFail($subtenantId);
            $this->subtenant_name = $this->selectedSubtenant->name;
            $this->subtenant_email = $this->selectedSubtenant->email;
            $this->subtenant_phone = $this->selectedSubtenant->phone ?? '';
            $this->subtenant_slug = $this->selectedSubtenant->slug;
            $this->subtenant_status = $this->selectedSubtenant->status;
        }
        
        $this->showSubtenantModal = true;
    }

    protected function resetSubtenantFields(): void
    {
        $this->selectedSubtenant = null;
        $this->subtenant_name = '';
        $this->subtenant_email = '';
        $this->subtenant_phone = '';
        $this->subtenant_slug = '';
        $this->subtenant_status = 'active';
    }

    public function saveSubtenant(): void
    {
        $this->validate([
            'subtenant_name' => 'required|string|max:255',
            'subtenant_email' => 'required|email|max:255',
            'subtenant_slug' => 'required|string|max:255|unique:tenants,slug,' . ($this->selectedSubtenant?->id ?? 'NULL'),
        ]);

        $data = [
            'name' => $this->subtenant_name,
            'email' => $this->subtenant_email,
            'phone' => $this->subtenant_phone,
            'slug' => $this->subtenant_slug,
            'status' => $this->subtenant_status,
            'parent_id' => $this->tenant->id,
        ];

        if ($this->selectedSubtenant) {
            $this->selectedSubtenant->update($data);
            session()->flash('message', 'Sub-tenant updated successfully.');
        } else {
            Tenant::create($data);
            session()->flash('message', 'Sub-tenant created successfully.');
        }

        $this->showSubtenantModal = false;
        $this->resetSubtenantFields();
        $this->tenant->refresh();
    }

    public function deleteSubtenant(int $subtenantId): void
    {
        $subtenant = Tenant::findOrFail($subtenantId);
        $subtenant->delete();
        $this->tenant->refresh();
        session()->flash('message', 'Sub-tenant deleted successfully.');
    }

    public function render()
    {
        // Only super-admins can access this page
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only Super Admins can access this page.');
        }

        return view('livewire.settings.tenant-detail', [
            'stats' => $this->stats,
            'subtenants' => $this->subtenants,
            'users' => $this->tenantUsers,
            'recentVisitors' => $this->recentVisitors,
            'recentMeetings' => $this->recentMeetings,
        ])->layout('layouts.app');
    }
}