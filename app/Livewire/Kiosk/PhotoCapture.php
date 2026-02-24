<?php

namespace App\Livewire\Kiosk;

use Livewire\Component;
use Livewire\WithFileUploads;

class PhotoCapture extends Component
{
    use WithFileUploads;

    public $photo;
    public string $capturedPhoto = '';
    public bool $showCamera = true;

    public function capturePhoto(string $photoData): void
    {
        $this->capturedPhoto = $photoData;
        $this->showCamera = false;
    }

    public function retakePhoto(): void
    {
        $this->capturedPhoto = '';
        $this->showCamera = true;
    }

    public function submit(): void
    {
        $this->dispatch('photo-captured', $this->capturedPhoto);
    }

    public function skip(): void
    {
        $this->dispatch('photo-skipped');
    }

    public function render()
    {
        return view('livewire.kiosk.photo-capture');
    }
}