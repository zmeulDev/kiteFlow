<?php

namespace App\Livewire\Settings;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Meeting;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use App\Models\Building;
use App\Models\MeetingRoom;
use Livewire\Component;

class SubtenantDetail extends Component
{
    public ?Tenant $subtenant = null;
    public string $tab = 'overview';

    // Basic info fields
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $slug = '';
    public string $status = 'active';

    // Address fields
    public string $address_line1 = '';
    public string $address_line2 = '';
    public string $city = '';
    public string $state = '';
    public string $postal_code = '';
    public string $country = '';

    // Contact person fields
    public string $contact_person_name = '';
    public string $contact_person_email = '';
    public string $contact_person_phone = '';

    // Space allocation fields
    public ?string $allocated_space = null;
    public ?string $allocated_floor = null;
    public ?string $allocated_zone = null;
    public ?int $allocated_seats = null;

    // Contract fields
    public string $subscription_plan = 'basic';
    public string $billing_cycle = 'monthly';
    public float $monthly_price = 0;
    public float $yearly_price = 0;
    public string $contract_start_date = '';
    public string $contract_end_date = '';
    public string $payment_status = 'paid';
    public string $notes = '';

    public bool $showEditModal = false;

    public function mount(int $subtenantId): void
    {
        $currentUser = auth()->user();
        $currentTenant = $currentUser->getCurrentTenant();

        // Verify user is a tenant admin
        if (!$currentUser->hasRole('admin')) {
            abort(403, 'Access denied. Only tenant admins can manage sub-tenants.');
        }

        $this->subtenant = Tenant::with(['users', 'buildings.zones', 'meetings', 'visitors'])->findOrFail($subtenantId);

        // Verify this subtenant belongs to the current tenant
        if ($this->subtenant->parent_id !== $currentTenant?->id) {
            abort(403, 'You do not have access to this sub-tenant.');
        }

        $this->loadSubtenantData();
    }

    protected function loadSubtenantData(): void
    {
        $this->name = $this->subtenant->name;
        $this->email = $this->subtenant->email;
        $this->phone = $this->subtenant->phone ?? '';
        $this->slug = $this->subtenant->slug;
        $this->status = $this->subtenant->status;

        // Address
        $address = $this->subtenant->address ?? [];
        $this->address_line1 = $address['line1'] ?? '';
        $this->address_line2 = $address['line2'] ?? '';
        $this->city = $address['city'] ?? '';
        $this->state = $address['state'] ?? '';
        $this->postal_code = $address['postal_code'] ?? '';
        $this->country = $address['country'] ?? '';

        // Contact person (stored in settings)
        $settings = $this->subtenant->settings ?? [];
        $this->contact_person_name = $settings['contact_person']['name'] ?? '';
        $this->contact_person_email = $settings['contact_person']['email'] ?? '';
        $this->contact_person_phone = $settings['contact_person']['phone'] ?? '';

        // Space allocation (stored in settings)
        $this->allocated_space = $settings['space_allocation']['description'] ?? null;
        $this->allocated_floor = $settings['space_allocation']['floor'] ?? null;
        $this->allocated_zone = $settings['space_allocation']['zone'] ?? null;
        $this->allocated_seats = $settings['space_allocation']['seats'] ?? null;

        // Contract (stored in settings)
        $contract = $settings['contract'] ?? [];
        $this->subscription_plan = $contract['subscription_plan'] ?? 'basic';
        $this->billing_cycle = $contract['billing_cycle'] ?? 'monthly';
        $this->monthly_price = $contract['monthly_price'] ?? 0;
        $this->yearly_price = $contract['yearly_price'] ?? 0;
        $this->contract_start_date = $contract['contract_start_date'] ?? '';
        $this->contract_end_date = $contract['contract_end_date'] ?? '';
        $this->payment_status = $contract['payment_status'] ?? 'paid';
        $this->notes = $contract['notes'] ?? '';
    }

    public function getStatsProperty(): array
    {
        return [
            'total_users' => $this->subtenant->users()->count(),
            'total_visitors' => Visitor::where('tenant_id', $this->subtenant->id)->count(),
            'total_visits' => VisitorVisit::where('tenant_id', $this->subtenant->id)->count(),
            'total_meetings' => Meeting::where('tenant_id', $this->subtenant->id)->count(),
            'total_buildings' => $this->subtenant->buildings()->count(),
            'checked_in_today' => VisitorVisit::where('tenant_id', $this->subtenant->id)
                ->whereDate('check_in_at', today())
                ->whereNull('check_out_at')
                ->count(),
        ];
    }

    public function getSubtenantUsersProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->subtenant->users()->with('roles')->orderBy('name')->get();
    }

    public function getBuildingsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->subtenant->buildings()->with('zones')->orderBy('name')->get();
    }

    public function getRecentMeetingsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Meeting::where('tenant_id', $this->subtenant->id)
            ->with(['host', 'meetingRoom'])
            ->orderBy('start_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function getRecentVisitorsProperty(): \Illuminate\Database\Eloquent\Collection
    {
        return Visitor::where('tenant_id', $this->subtenant->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function switchTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function openEditModal(): void
    {
        $this->loadSubtenantData();
        $this->showEditModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'slug' => 'required|string|max:255|unique:tenants,slug,' . $this->subtenant->id,
            'status' => 'required|in:active,inactive,suspended',
        ]);

        // Merge existing settings with new settings
        $existingSettings = $this->subtenant->settings ?? [];

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'slug' => $this->slug,
            'status' => $this->status,
            'address' => [
                'line1' => $this->address_line1,
                'line2' => $this->address_line2,
                'city' => $this->city,
                'state' => $this->state,
                'postal_code' => $this->postal_code,
                'country' => $this->country,
            ],
            'settings' => array_merge($existingSettings, [
                'contact_person' => [
                    'name' => $this->contact_person_name,
                    'email' => $this->contact_person_email,
                    'phone' => $this->contact_person_phone,
                ],
                'space_allocation' => [
                    'description' => $this->allocated_space,
                    'floor' => $this->allocated_floor,
                    'zone' => $this->allocated_zone,
                    'seats' => $this->allocated_seats,
                ],
                'contract' => [
                    'subscription_plan' => $this->subscription_plan,
                    'billing_cycle' => $this->billing_cycle,
                    'monthly_price' => $this->monthly_price,
                    'yearly_price' => $this->yearly_price,
                    'contract_start_date' => $this->contract_start_date ?: null,
                    'contract_end_date' => $this->contract_end_date ?: null,
                    'payment_status' => $this->payment_status,
                    'notes' => $this->notes,
                ],
            ]),
        ];

        $this->subtenant->update($data);
        $this->showEditModal = false;
        $this->subtenant->refresh();
        session()->flash('message', 'Sub-tenant updated successfully.');
    }

    public function render()
    {
        // Only tenant admins can access this page
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Access denied. Only tenant admins can manage sub-tenants.');
        }

        return view('livewire.settings.subtenant-detail', [
            'stats' => $this->stats,
            'users' => $this->subtenantUsers,
            'buildings' => $this->buildings,
            'recentMeetings' => $this->recentMeetings,
            'recentVisitors' => $this->recentVisitors,
        ])->layout('layouts.app');
    }
}