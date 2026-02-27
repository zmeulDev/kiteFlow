<?php

namespace App\Livewire\Admin\Visits;

use App\Models\Building;
use App\Models\Company;
use App\Models\Entrance;
use App\Models\Space;
use App\Models\User;
use App\Models\Visit;
use App\Services\VisitSchedulingService;
use App\Services\VisitService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class VisitList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $status_filter = '';
    public ?int $building_filter = null;
    public ?int $entrance_filter = null;
    public string $date_from = '';
    public string $date_to = '';

    public bool $showModal = false;
    public ?int $editingVisitId = null;

    // Scheduling modal properties
    public bool $showScheduleModal = false;
    public ?int $schedule_company_id = null;
    public ?int $schedule_host_id = null;
    public string $schedule_first_name = '';
    public string $schedule_last_name = '';
    public string $schedule_email = '';
    public string $schedule_phone = '';
    public ?int $schedule_visitor_company_id = null;
    public ?int $schedule_entrance_id = null;
    public ?int $schedule_space_id = null;
    public string $schedule_purpose = '';
    public string $schedule_date = '';
    public string $schedule_time = '';

    protected function rules(): array
    {
        return [
            'schedule_company_id' => 'required|exists:companies,id',
            'schedule_host_id' => 'nullable|exists:users,id',
            'schedule_first_name' => 'required|string|max:255',
            'schedule_last_name' => 'required|string|max:255',
            'schedule_email' => 'required|email|max:255',
            'schedule_phone' => 'nullable|string|max:50',
            'schedule_visitor_company_id' => 'nullable|exists:companies,id',
            'schedule_entrance_id' => 'required|exists:entrances,id',
            'schedule_space_id' => 'nullable|exists:spaces,id',
            'schedule_purpose' => 'nullable|string|max:255',
            'schedule_date' => 'required|date|after_or_equal:today',
            'schedule_time' => 'required',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedBuildingFilter(): void
    {
        $this->entrance_filter = null;
        $this->resetPage();
    }

    public function updatedScheduleCompanyId(): void
    {
        // Reset host when company changes
        $this->schedule_host_id = null;
    }

    public function updatedScheduleEntranceId(): void
    {
        // Reset space when entrance changes
        $this->schedule_space_id = null;
    }

    public function editVisit(int $visitId): void
    {
        $this->editingVisitId = $visitId;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingVisitId = null;
    }

    public function openScheduleModal(): void
    {
        $this->resetScheduleForm();
        
        // Auto-select host and company for non-admin users
        $user = auth()->user();
        if (!$user->isAdmin()) {
            $this->schedule_company_id = $user->company_id;
            
            // Viewers can only schedule for themselves
            if ($user->role === 'viewer') {
                $this->schedule_host_id = $user->id;
            }
        }
        
        $this->showScheduleModal = true;
    }

    public function closeScheduleModal(): void
    {
        $this->showScheduleModal = false;
        $this->resetScheduleForm();
    }

    public function resetScheduleForm(): void
    {
        $this->schedule_company_id = null;
        $this->schedule_host_id = null;
        $this->schedule_first_name = '';
        $this->schedule_last_name = '';
        $this->schedule_email = '';
        $this->schedule_phone = '';
        $this->schedule_visitor_company_id = null;
        $this->schedule_entrance_id = null;
        $this->schedule_space_id = null;
        $this->schedule_purpose = '';
        $this->schedule_date = '';
        $this->schedule_time = '';
        $this->resetErrorBag();
    }

    public function getEditingVisitProperty(): ?Visit
    {
        if (!$this->editingVisitId) {
            return null;
        }
        
        $query = Visit::with(['visitor.company', 'entrance.building', 'host', 'space']);
        if (!auth()->user()->isAdmin()) {
            if (auth()->user()->role === 'viewer') {
                 $query->where('host_id', auth()->id());
            } else {
                 $query->whereHas('host', function ($q) {
                     $q->where('company_id', auth()->user()->company_id);
                 });
            }
        }
        
        return $query->find($this->editingVisitId);
    }

    public function getHostUsersProperty()
    {
        if (!$this->schedule_company_id) {
            return collect();
        }
        
        $user = auth()->user();
        $query = User::where('company_id', $this->schedule_company_id)
            ->where('is_active', true)
            ->orderBy('name');
            
        // If the user is a viewer, they should only see themselves in the host list
        if (!$user->isAdmin() && $user->role === 'viewer') {
            $query->where('id', $user->id);
        }
            
        return $query->get();
    }

    #[On('confirmCheckOut')]
    public function confirmCheckOut(int $visitId): void
    {
        $this->checkOut($visitId);
        $this->closeModal();
    }

    #[On('confirmCheckIn')]
    public function confirmCheckIn(int $visitId): void
    {
        $this->checkIn($visitId);
    }

    public function showCheckOutConfirm(int $visitId): void
    {
        $query = Visit::with('visitor');
        if (!auth()->user()->isAdmin()) {
            $query->whereHas('host', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            });
        }
        $visit = $query->findOrFail($visitId);
        $visitorName = $visit->visitor->full_name ?? 'Unknown';

        $this->dispatch('showConfirmModal', [
            'modalId' => 'checkout-confirm',
            'title' => 'Confirm Check Out',
            'message' => "Are you sure you want to check out {$visitorName}?",
            'confirmText' => 'Check Out',
            'cancelText' => 'Cancel',
            'confirmMethod' => 'confirmCheckOut',
            'confirmColor' => 'danger',
            'params' => ['visitId' => $visitId],
        ]);
    }

    public function showCheckInConfirm(int $visitId): void
    {
        $query = Visit::with('visitor');
        if (!auth()->user()->isAdmin()) {
            $query->whereHas('host', function ($q) {
                $q->where('company_id', auth()->user()->company_id);
            });
        }
        $visit = $query->findOrFail($visitId);
        $visitorName = $visit->visitor->full_name ?? 'Unknown';

        $this->dispatch('showConfirmModal', [
            'modalId' => 'checkin-confirm',
            'title' => 'Confirm Check In',
            'message' => "Are you sure you want to check in {$visitorName}?",
            'confirmText' => 'Check In',
            'cancelText' => 'Cancel',
            'confirmMethod' => 'confirmCheckIn',
            'confirmColor' => 'success',
            'params' => ['visitId' => $visitId],
        ]);
    }

    public function checkIn(int $visitId, VisitService $visitService): void
    {
        $query = Visit::query();
        if (!auth()->user()->isAdmin()) {
            if (auth()->user()->role === 'viewer') {
                $query->where('host_id', auth()->id());
            } else {
                $query->whereHas('host', function ($q) {
                    $q->where('company_id', auth()->user()->company_id);
                });
            }
        }
        $visit = $query->findOrFail($visitId);
        $visitService->checkIn($visit);
        session()->flash('message', 'Visitor checked in successfully.');
        $this->closeModal();
    }

    public function checkOut(int $visitId, VisitService $visitService): void
    {
        $query = Visit::query();
        if (!auth()->user()->isAdmin()) {
            if (auth()->user()->role === 'viewer') {
                $query->where('host_id', auth()->id());
            } else {
                $query->whereHas('host', function ($q) {
                    $q->where('company_id', auth()->user()->company_id);
                });
            }
        }
        $visit = $query->findOrFail($visitId);
        $visitService->checkOut($visit);
        session()->flash('message', 'Visitor checked out successfully.');
    }

    public function scheduleVisit(VisitSchedulingService $schedulingService): void
    {
        $user = auth()->user();
        
        // Enforce company ID strictly if not admin
        if (!$user->isAdmin()) {
            $this->schedule_company_id = $user->company_id;
            
            // Viewers MUST be the host
            if ($user->role === 'viewer') {
                $this->schedule_host_id = $user->id;
            }
        }

        $this->validate();

        $entrance = Entrance::findOrFail($this->schedule_entrance_id);
        $space = $this->schedule_space_id ? Space::find($this->schedule_space_id) : null;
        $host = $this->schedule_host_id ? User::find($this->schedule_host_id) : null;
        $visitorCompany = $this->schedule_visitor_company_id ? Company::find($this->schedule_visitor_company_id) : null;

        $scheduledAt = \Carbon\Carbon::parse("{$this->schedule_date} {$this->schedule_time}");

        $visitorData = [
            'first_name' => $this->schedule_first_name,
            'last_name' => $this->schedule_last_name,
            'email' => $this->schedule_email,
            'phone' => $this->schedule_phone ?: null,
        ];

        $visitData = [
            'purpose' => $this->schedule_purpose ?: null,
            'scheduled_at' => $scheduledAt,
        ];

        try {
            $visit = $schedulingService->scheduleVisit(
                $visitorData,
                $visitData,
                $entrance,
                $host,
                $visitorCompany,
                $space
            );
        } catch (\Exception $e) {
            dd($e->getMessage());
        }

        session()->flash('message', "Visit scheduled successfully. Check-in code: {$visit->check_in_code}");
        $this->closeScheduleModal();
    }

    public function render()
    {
        $query = Visit::with(['visitor.company', 'entrance.building', 'host', 'space']);
        
        if (!auth()->user()->isAdmin()) {
            if (auth()->user()->role === 'viewer') {
                $query->where('host_id', auth()->id());
            } else {
                $query->whereHas('host', function ($q) {
                    $q->where('company_id', auth()->user()->company_id);
                });
            }
        }

        $visits = $query->when($this->search, function ($q) {
                $q->where(function ($sq) {
                    $sq->whereHas('visitor', function ($q) {
                        $q->where('first_name', 'like', "%{$this->search}%")
                            ->orWhere('last_name', 'like', "%{$this->search}%")
                            ->orWhere('email', 'like', "%{$this->search}%");
                    })->orWhereHas('host', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    })->orWhere('host_name', 'like', "%{$this->search}%")
                      ->orWhere('check_in_code', 'like', "%{$this->search}%");
                });
            })
            ->when($this->status_filter, fn($q) => $q->where('status', $this->status_filter))
            ->when($this->building_filter, function ($q) {
                $q->whereHas('entrance', fn($q) => $q->where('building_id', $this->building_filter));
            })
            ->when($this->entrance_filter, fn($q) => $q->where('entrance_id', $this->entrance_filter))
            ->when($this->date_from, fn($q) => $q->whereDate('check_in_at', '>=', $this->date_from))
            ->when($this->date_to, fn($q) => $q->whereDate('check_in_at', '<=', $this->date_to))
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $buildings = Building::where('is_active', true)->orderBy('name')->get();

        $entrances = collect();
        if ($this->building_filter) {
            $entrances = Entrance::where('building_id', $this->building_filter)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        $editingVisit = $this->editingVisit;
        $hostUsers = $this->hostUsers;
        
        $companiesQuery = Company::where('is_active', true)->orderBy('name');
        if (!auth()->user()->isAdmin()) {
            $companiesQuery->where('id', auth()->user()->company_id);
        }
        $companies = $companiesQuery->get();
        
        $allEntrances = Entrance::with('building')->where('is_active', true)->orderBy('name')->get();

        $availableSpaces = collect();
        if ($this->schedule_entrance_id) {
            $selectedEntrance = Entrance::find($this->schedule_entrance_id);
            if ($selectedEntrance) {
                $availableSpaces = Space::where('building_id', $selectedEntrance->building_id)
                    ->where('is_active', true)
                    ->orderBy('name')
                    ->get();
            }
        }

        return view('livewire.admin.visits.visit-list', compact(
            'visits',
            'buildings',
            'entrances',
            'editingVisit',
            'hostUsers',
            'companies',
            'allEntrances',
            'availableSpaces'
        ));
    }
}