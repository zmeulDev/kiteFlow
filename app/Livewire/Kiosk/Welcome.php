<?php

namespace App\Livewire\Kiosk;

use App\Models\Entrance;
use Livewire\Component;

class Welcome extends Component
{
    public Entrance $entrance;

    public string $welcomeMessage = '';
    public string $primaryColor = '#3b82f6';
    public string $buildingName = '';

    public function mount(Entrance $entrance): void
    {
        $entrance->loadMissing('building');
        $this->entrance = $entrance;
        $this->welcomeMessage = $entrance->kioskSetting?->welcome_message ?? 'Welcome! Please check in below.';
        $this->primaryColor = $entrance->kioskSetting?->primary_color ?? '#3b82f6';
        $this->buildingName = $entrance->building->name ?? '';
    }

    public function startManualCheckIn(): void
    {
        $this->redirect(route('kiosk.checkin', $this->entrance->kiosk_identifier));
    }

    public function showQrCode(): void
    {
        $this->redirect(route('kiosk.welcome', $this->entrance->kiosk_identifier) . '?mode=qr');
    }

    public function checkInWithCode(): void
    {
        $this->redirect(route('kiosk.check-in-code', $this->entrance->kiosk_identifier));
    }

    public function render()
    {
        $mode = request()->get('mode', 'welcome');

        if ($mode === 'qr') {
            return view('livewire.kiosk.qr-scanner', [
                'entrance' => $this->entrance,
            ]);
        }

        return view('livewire.kiosk.welcome', [
            'entrance' => $this->entrance,
        ]);
    }
}