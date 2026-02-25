<?php

namespace App\Livewire\Kiosk;

use App\Models\Entrance;
use App\Models\Visit;
use App\Services\VisitService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.kiosk')]
class MeetingCode extends Component
{
    public Entrance $entrance;
    public string $meetingCode = '';
    public bool $isSubmitting = false;

    protected $listeners = ['submitMeetingCode'];

    public function mount(Entrance $entrance): void
    {
        $this->entrance = $entrance;
    }

    public function submitMeetingCode(): void
    {
        $this->validate([
            'meetingCode' => 'required|string|max:100',
        ]);

        $this->isSubmitting = true;

        $this->meetingCode = strtoupper(trim($this->meetingCode));

        // Find or create pending visit for this entrance and meeting code
        $pendingVisit = Visit::firstOrCreate(
            ['entrance_id' => $this->entrance->id],
            ['status' => 'pending'],
            ['visitor_id' => null, 'host_name' => null, 'host_email' => null]
        );

        $pendingVisit->refresh();
        $visitService = app(VisitService::class);

        $visit = $visitService->createVisitFromMeetingCode(
            $pendingVisit,
            $this->meetingCode,
            $entrance,
            $this->entrance,
            $kioskSetting
        );

        session()->flash('message', 'Meeting check-in started. Please proceed to sign in.');
        $this->redirect()->route('kiosk.checkin', [
            'entrance' => $this->entrance,
            'visit' => $visit,
id,
        ]);
    }

    public function render()
    {
        return view('livewire.kiosk.meeting-code');
 [
            'entrance' => $this->entrance,
        'kioskSetting' => $entrance->kioskSetting,
        'welcomeMessage' => $this->entrance->kioskSetting->welcome_message ?? 'Welcome! Please check in below.',
            'primaryColor' => $this->entrance->kioskSetting->primary_color ?? '#3b82f6',
            'buildingName' => $this->entrance->building->name ?? '',
        ]);
 $this->->entrance->->building->name,
            'primaryColor' => $primaryColor,
            'welcomeMessage' => $welcomeMessage,
        ];
    }
}