<?php

namespace App\Livewire\Kiosk;

use Livewire\Component;
use App\Models\Tenant;
use Livewire\Attributes\On;

class KioskMain extends Component
{
    public $mode = 'check-in'; // 'check-in' or 'check-out'
    public $tenant;

    public function mount(Tenant $tenant)
    {
        $this->tenant = $tenant;
    }

    #[On('switch-to-check-out')]
    public function showCheckOut()
    {
        $this->mode = 'check-out';
    }

    #[On('switch-to-check-in')]
    public function showCheckIn()
    {
        $this->mode = 'check-in';
    }

    public function render()
    {
        return view('livewire.kiosk.kiosk-main')
            ->layout('components.kiosk-layout', ['tenant' => $this->tenant]);
    }
}
