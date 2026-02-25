<?php

namespace App\Livewire\Admin\Visits;

use App\Models\Building;
use App\Models\Company;
use App\Models\Entrance;
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
        $this->schedule_purpose = '';
        $this->schedule_date = '';
        $this->schedule_time = '';
        $this->resetErrorBag();
    }

    public function getEditingVisitProperty(): ?Visit
    {
        return $this->editingVisitId
            ? Visit::with(['visitor.company', 'entrance.building', 'host'])->find($this->editingVisitId)
            : null;
    }

    public function getHostUsersProperty()
    {
        if (!$this->schedule_company_id) {
            return collect();
        }
        return User::where('company_id', $this->schedule_company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
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
        $visit = Visit::with('visitor')->findOrFail($visitId);
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
        $visit = Visit::with('visitor')->findOrFail($visitId);
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
        $visit = Visit::findOrFail($visitId);
        $visitService->checkIn($visit);
        session()->flash('message', 'Visitor checked in successfully.');
        $this->closeModal();
    }

    public function checkOut(int $visitId, VisitService $visitService): void
    {
        $visit = Visit::findOrFail($visitId);
        $visitService->checkOut($visit);
        session()->flash('message', 'Visitor checked out successfully.');
    }

    public function scheduleVisit(VisitSchedulingService $schedulingService): void
    {
        $this->validate();

        $entrance = Entrance::findOrFail($this->schedule_entrance_id);
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

        $visit = $schedulingService->scheduleVisit(
            $visitorData,
            $visitData,
            $entrance,
            $host,
            $visitorCompany
        );

        session()->flash('message', "Visit scheduled successfully. Check-in code: {$visit->check_in_code}");
        $this->closeScheduleModal();
    }

    public function render()
    {
        $visits = Visit::with(['visitor.company', 'entrance.building', 'host'])
            ->when($this->search, function ($q) {
                $q->whereHas('visitor', function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                })->orWhereHas('host', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                })->orWhere('host_name', 'like', "%{$this->search}%")
                  ->orWhere('check_in_code', 'like', "%{$this->search}%");
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
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        $allEntrances = Entrance::with('building')->where('is_active', true)->orderBy('name')->get();

        return view('livewire.admin.visits.visit-list', compact(
            'visits',
            'buildings',
            'entrances',
            'editingVisit',
            'hostUsers',
            'companies',
            'allEntrances'
        ));
    }
}