<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use App\Services\DataRetentionService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class DataRetention extends Component
{
    public int $retention_days = 90;

    protected $listeners = ['confirmRunCleanup'];

    protected function rules(): array
    {
        return [
            'retention_days' => 'required|integer|min:1|max:365',
        ];
    }

    public function mount(): void
    {
        $this->retention_days = Setting::get('data_retention_days', 90);
    }

    public function save(): void
    {
        $this->validate();

        Setting::set('data_retention_days', $this->retention_days);

        session()->flash('message', 'Data retention settings updated successfully.');
    }

    public function confirmRunCleanup(): void
    {
        $this->runCleanup();
    }

    public function showCleanupConfirm(): void
    {
        $this->dispatch('showConfirmModal', [
            'modalId' => 'cleanup-confirm',
            'title' => 'Run Data Cleanup',
            'message' => 'Are you sure you want to run cleanup? This will permanently delete old visit records based on your retention settings. This action cannot be undone.',
            'confirmText' => 'Run Cleanup',
            'cancelText' => 'Cancel',
            'confirmMethod' => 'confirmRunCleanup',
            'confirmColor' => 'warning',
            'params' => [],
        ]);
    }

    public function runCleanup(DataRetentionService $retentionService): void
    {
        $visitsDeleted = $retentionService->cleanupOldVisits();
        $visitorsDeleted = $retentionService->cleanupOrphanedVisitors();

        session()->flash('message', "Cleanup complete. Deleted {$visitsDeleted} visits and {$visitorsDeleted} orphaned visitors.");
    }

    public function render()
    {
        return view('livewire.admin.settings.data-retention');
    }
}