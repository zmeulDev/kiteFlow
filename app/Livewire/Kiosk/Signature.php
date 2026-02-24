<?php

namespace App\Livewire\Kiosk;

use Livewire\Component;

class Signature extends Component
{
    public string $signature_data = '';
    public bool $showPad = true;

    public function captureSignature(string $data): void
    {
        $this->signature_data = $data;
        $this->showPad = false;
    }

    public function clearSignature(): void
    {
        $this->signature_data = '';
        $this->showPad = true;
    }

    public function submit(): void
    {
        if (empty($this->signature_data)) {
            $this->addError('signature', 'Please provide your signature.');
            return;
        }

        $this->dispatch('signature-captured', $this->signature_data);
    }

    public function render()
    {
        return view('livewire.kiosk.signature');
    }
}