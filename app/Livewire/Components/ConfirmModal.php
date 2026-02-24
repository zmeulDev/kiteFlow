<?php

namespace App\Livewire\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class ConfirmModal extends Component
{
    public string $modalId = 'confirm-modal';
    public string $title = 'Confirm Action';
    public string $message = 'Are you sure you want to proceed?';
    public string $confirmText = 'Confirm';
    public string $cancelText = 'Cancel';
    public string $confirmMethod = '';
    public string $confirmColor = 'danger';
    public array $params = [];
    public bool $show = false;

    #[On('showConfirmModal')]
    public function showConfirmModal(array $data): void
    {
        $this->modalId = $data['modalId'] ?? 'confirm-modal';
        $this->title = $data['title'] ?? 'Confirm Action';
        $this->message = $data['message'] ?? 'Are you sure you want to proceed?';
        $this->confirmText = $data['confirmText'] ?? 'Confirm';
        $this->cancelText = $data['cancelText'] ?? 'Cancel';
        $this->confirmMethod = $data['confirmMethod'] ?? '';
        $this->confirmColor = $data['confirmColor'] ?? 'danger';
        $this->params = $data['params'] ?? [];
        $this->show = true;
    }

    #[On('hideConfirmModal')]
    public function hideConfirmModal(): void
    {
        $this->show = false;
    }

    public function confirm(): void
    {
        if ($this->confirmMethod) {
            $this->dispatch($this->confirmMethod, ...$this->params);
        }
        $this->hideConfirmModal();
    }

    public function render()
    {
        return view('livewire.components.confirm-modal');
    }
}
