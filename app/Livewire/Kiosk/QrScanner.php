<?php

namespace App\Livewire\Kiosk;

use App\Models\Entrance;
use App\Models\Visit;
use App\Services\QrCodeService;
use Livewire\Component;

class QrScanner extends Component
{
    public Entrance $entrance;
    public string $qrCodeUrl = '';
    public ?Visit $pendingVisit = null;

    public function mount(Entrance $entrance): void
    {
        $this->entrance = $entrance;

        // Create a pending visit for QR code
        $qrCodeService = app(QrCodeService::class);
        $this->pendingVisit = Visit::create([
            'entrance_id' => $entrance->id,
            'visitor_id' => null,
            'host_name' => 'Pending',
            'status' => 'pending',
            'qr_code' => $qrCodeService->generateQrCode(),
        ]);

        $this->qrCodeUrl = $qrCodeService->generateQrCodeUrl($this->pendingVisit);
    }

    public function checkMobileCheckIn(): void
    {
        $this->pendingVisit->refresh();

        if ($this->pendingVisit->status === 'checked_in') {
            $this->dispatch('mobile-checkin-complete');
        }
    }

    public function render()
    {
        return view('livewire.kiosk.qr-scanner');
    }
}