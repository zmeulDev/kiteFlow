<?php

namespace App\Livewire\Kiosk;

use App\Models\Entrance;
use App\Models\Visit;
use App\Services\VisitService;
use Livewire\Component;
use Livewire\WithPagination;

class CheckOut extends Component
{
    use WithPagination;

    public Entrance $entrance;
    public string $search = '';
    public ?int $confirmVisitId = null;

    protected $listeners = ['confirmCheckOut'];

    public function mount(Entrance $entrance): void
    {
        $this->entrance = $entrance;
    }

    public function confirmCheckOut(int $visitId): void
    {
        $this->checkOut($visitId);
    }

    public function showConfirmModal(int $visitId, string $visitorName): void
    {
        $this->confirmVisitId = $visitId;
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

    public function checkOut(int $visitId): void
    {
        $visit = Visit::findOrFail($visitId);
        app(VisitService::class)->checkOut($visit);
        session()->flash('message', 'Checked out successfully!');
    }

    public function render()
    {
        $activeVisits = Visit::with(['visitor', 'host'])
            ->where('entrance_id', $this->entrance->id)
            ->where('status', 'checked_in')
            ->when($this->search, function ($q) {
                $q->whereHas('visitor', function ($q) {
                    $q->where('first_name', 'like', "%{$this->search}%")
                        ->orWhere('last_name', 'like', "%{$this->search}%");
                })->orWhereHas('host', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                })->orWhere('host_name', 'like', "%{$this->search}%");
            })
            ->orderBy('check_in_at', 'desc')
            ->paginate(10);

        return view('livewire.kiosk.check-out', compact('activeVisits'));
    }
}