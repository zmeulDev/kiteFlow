<?php

namespace App\Livewire\Kiosk;

use App\Models\Entrance;
use App\Models\Visit;
use App\Services\VisitService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.kiosk')]
class CheckInCode extends Component
{
    public Entrance $entrance;

    public string $checkInCode = '';
    public ?Visit $scheduledVisit = null;
    public bool $codeNotFound = false;
    public bool $alreadyCheckedIn = false;

    public function mount(Entrance $entrance): void
    {
        $this->entrance = $entrance;
    }

    public function lookupCode(VisitService $visitService): void
    {
        $this->reset(['scheduledVisit', 'codeNotFound', 'alreadyCheckedIn']);

        $this->validate([
            'checkInCode' => 'required|string|size:6',
        ]);

        $code = strtoupper(trim($this->checkInCode));
        $visit = $visitService->findByCheckInCode($code);

        if (!$visit) {
            $this->codeNotFound = true;
            return;
        }

        if ($visit->status !== 'pending') {
            $this->alreadyCheckedIn = true;
            return;
        }

        $this->scheduledVisit = $visit;
    }

    public function clearCode(): void
    {
        $this->reset(['checkInCode', 'scheduledVisit', 'codeNotFound', 'alreadyCheckedIn']);
    }

    public function render()
    {
        return view('livewire.kiosk.check-in-code', [
            'entrance' => $this->entrance,
            'kioskSetting' => $this->entrance->kioskSetting,
        ]);
    }
}