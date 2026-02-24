<?php

namespace App\Livewire\Kiosk;

use App\Models\Visit;
use Livewire\Component;

class Success extends Component
{
    public Visit $visit;
    public string $message = 'Check-in successful!';
    public int $redirectDelay = 10;

    public function mount(Visit $visit): void
    {
        $this->visit = $visit->loadMissing(['host', 'visitor.company', 'entrance.building']);
    }

    public function done(): void
    {
        $this->redirect(route('kiosk.welcome', $this->visit->entrance->kiosk_identifier));
    }

    public function render()
    {
        return view('livewire.kiosk.success');
    }
}