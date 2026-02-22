<?php

namespace App\Livewire\Visitors;

use App\Models\Tenant;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class VisitorList extends Component
{
    use WithPagination, WithFileUploads;

    public ?int $tenantId = null;
    public string $search = '';
    public string $statusFilter = 'all';
    public bool $showModal = false;
    public bool $showCheckInModal = false;
    public bool $showDeleteModal = false;
    public ?Visitor $selectedVisitor = null;
    
    // Form fields
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $company = '';
    public string $notes = '';
    public string $id_number = '';
    public $photo;
    public string $is_blacklisted = 'active'; // 'active' or 'blacklisted'
    
    // Check-in fields
    public string $purpose = '';
    public $host_id = null;
    public $meeting_id = null;
    
    // Companies for dropdown (tenant + subtenants)
    public $companies = [];

    protected $queryString = ['search', 'statusFilter'];

    public function getStatsProperty(): array
    {
        if (!$this->tenantId) {
            return [
                'total_visitors' => 0,
                'checked_in_today' => 0,
                'total_visits' => 0,
                'blacklisted' => 0,
            ];
        }

        return [
            'total_visitors' => Visitor::where('tenant_id', $this->tenantId)->count(),
            'checked_in_today' => VisitorVisit::where('tenant_id', $this->tenantId)
                ->whereDate('check_in_at', today())
                ->whereNull('check_out_at')
                ->count(),
            'total_visits' => VisitorVisit::where('tenant_id', $this->tenantId)->count(),
            'blacklisted' => Visitor::where('tenant_id', $this->tenantId)
                ->where('is_blacklisted', true)
                ->count(),
        ];
    }

    protected function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'company' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function mount(?int $tenantId = null): void
    {
        $tenantId = $tenantId
            ?? request()->attributes->get('tenant_id')
            ?? auth()->user()?->getCurrentTenant()?->id;

        // Verify user has access to this tenant
        if ($tenantId && auth()->check()) {
            $user = auth()->user();
            if (!$user->belongsToOneOfTenants([$tenantId])) {
                abort(403, 'You do not have access to this tenant data.');
            }
        }

        $this->tenantId = $tenantId;

        // Load companies for dropdown (tenant + subtenants)
        if ($this->tenantId) {
            $tenant = Tenant::find($this->tenantId);
            if ($tenant) {
                $this->companies = collect([$tenant])
                    ->merge($tenant->children)
                    ->pluck('name', 'name')
                    ->toArray();
            }
        }
    }

    public function getVisitorsProperty()
    {
        if (!$this->tenantId) {
            return collect();
        }

        return Visitor::with(['visits' => fn ($q) => $q->latest('check_in_at')->limit(1)])
            ->where('tenant_id', $this->tenantId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('company', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter === 'active', fn ($q) => $q->where('is_blacklisted', false))
            ->when($this->statusFilter === 'blacklisted', fn ($q) => $q->where('is_blacklisted', true))
            ->when($this->statusFilter === 'checked-in', fn ($q) => $q->whereHas('visits', fn ($q) => $q->whereNull('check_out_at')->where('status', 'checked_in')))
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->selectedVisitor = null;
        $this->showModal = true;
    }

    public function openEditModal(int $visitorId): void
    {
        $this->selectedVisitor = Visitor::findOrFail($visitorId);
        $this->fill([
            'first_name' => $this->selectedVisitor->first_name,
            'last_name' => $this->selectedVisitor->last_name,
            'email' => $this->selectedVisitor->email,
            'phone' => $this->selectedVisitor->phone,
            'company' => $this->selectedVisitor->company,
            'notes' => $this->selectedVisitor->notes ?? '',
            'id_number' => $this->selectedVisitor->id_number ?? '',
            'is_blacklisted' => $this->selectedVisitor->is_blacklisted ? 'blacklisted' : 'active',
        ]);
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'notes' => $this->notes,
            'id_number' => $this->id_number,
            'is_blacklisted' => $this->is_blacklisted === 'blacklisted',
        ];

        if ($this->photo) {
            $data['photo'] = $this->photo->store('visitors/photos', 'public');
        }

        if ($this->selectedVisitor) {
            $this->selectedVisitor->update($data);
            session()->flash('message', 'Visitor updated successfully.');
        } else {
            $data['tenant_id'] = $this->tenantId;
            $this->selectedVisitor = Visitor::create($data);
            session()->flash('message', 'Visitor created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function openCheckInModal(int $visitorId): void
    {
        $this->selectedVisitor = Visitor::findOrFail($visitorId);
        $this->purpose = '';
        $this->host_id = null;
        $this->meeting_id = null;
        $this->showCheckInModal = true;
    }

    public function checkIn(): void
    {
        if ($this->selectedVisitor->is_blacklisted) {
            session()->flash('error', 'This visitor is blacklisted and cannot check in.');
            return;
        }

        $visit = $this->selectedVisitor->visits()->create([
            'tenant_id' => $this->tenantId,
            'host_id' => $this->host_id,
            'meeting_id' => $this->meeting_id,
            'purpose' => $this->purpose,
            'check_in_method' => 'reception',
            'check_in_at' => now(),
            'status' => 'checked_in',
        ]);

        session()->flash('message', "Visitor checked in successfully. Badge: {$visit->badge_number}");
        $this->showCheckInModal = false;
    }

    public function checkOut(int $visitId): void
    {
        $visit = VisitorVisit::findOrFail($visitId);
        $visit->checkOut(auth()->id());

        session()->flash('message', "Visitor checked out. Duration: {$visit->getDurationFormatted()}");
    }

    public function openDeleteModal(int $visitorId): void
    {
        $this->selectedVisitor = Visitor::findOrFail($visitorId);
        $this->showDeleteModal = true;
    }

    public function confirmDelete(): void
    {
        if (!$this->selectedVisitor) {
            return;
        }

        $this->selectedVisitor->delete();
        $this->showDeleteModal = false;
        $this->selectedVisitor = null;
        session()->flash('message', 'Visitor deleted successfully.');
    }

    private function resetForm(): void
    {
        $this->reset([
            'first_name', 'last_name', 'email', 'phone', 'company', 'notes', 'id_number', 'photo', 'is_blacklisted'
        ]);
        $this->is_blacklisted = 'active'; // Reset to active instead of false
    }

    public function render()
    {
        return view('livewire.visitors.visitor-list', [
            'visitors' => $this->visitors,
            'stats' => $this->stats,
        ]);
    }
}