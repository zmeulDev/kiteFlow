<?php

namespace App\Livewire\Admin\Visits;

use App\Models\Building;
use App\Models\Entrance;
use App\Models\Visit;
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

    public function getEditingVisitProperty(): ?Visit
    {
        return $this->editingVisitId
            ? Visit::with(['visitor.company', 'entrance.building', 'host'])->find($this->editingVisitId)
            : null;
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
                })->orWhere('host_name', 'like', "%{$this->search}%");
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

        return view('livewire.admin.visits.visit-list', compact('visits', 'buildings', 'entrances', 'editingVisit'));
    }
}