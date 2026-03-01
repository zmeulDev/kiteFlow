<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use App\Models\Visit;
use App\Models\Visitor;
use App\Services\DataRetentionService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class BusinessDetails extends Component
{
    public string $business_name = '';
    public string $business_address = '';
    public string $business_phone = '';
    public string $business_email = '';
    public int $retention_days = 90;
    
    public int $visitsToDeleteCount = 0;
    public int $visitorsToDeleteCount = 0;

    protected $listeners = ['confirmRunCleanup'];

    protected function rules(): array
    {
        return [
            'business_name' => 'required|string|max:255',
            'business_address' => 'nullable|string|max:500',
            'business_phone' => 'nullable|string|max:50',
            'business_email' => 'nullable|email|max:255',
            'retention_days' => 'required|integer|min:1|max:365',
        ];
    }

    public function mount(): void
    {
        abort_if(!auth()->user()->isAdmin(), 403);

        $this->business_name = Setting::get('business_name', '');
        $this->business_address = Setting::get('business_address', '');
        $this->business_phone = Setting::get('business_phone', '');
        $this->business_email = Setting::get('business_email', '');
        $this->retention_days = Setting::get('data_retention_days', 90);

        $this->calculateCleanupStats();
    }

    public function updatedRetentionDays(): void
    {
        // Re-calculate when the user changes retention_days
        $this->calculateCleanupStats();
    }

    public function calculateCleanupStats(): void
    {
        // Safe check for retention days locally
        $days = (int) $this->retention_days;
        if ($days < 1) return;

        $cutoffDate = now()->subDays($days);

        $this->visitsToDeleteCount = Visit::where('check_in_at', '<', $cutoffDate)
            ->orWhere('check_out_at', '<', $cutoffDate)
            ->count();
            
        // Orphaned visitors don't directly depend on retention days, but they are cleaned up in the same job
        $this->visitorsToDeleteCount = Visitor::doesntHave('visits')->count();
    }

    public function save(): void
    {
        $this->validate();

        Setting::set('business_name', $this->business_name);
        Setting::set('business_address', $this->business_address);
        Setting::set('business_phone', $this->business_phone);
        Setting::set('business_email', $this->business_email);
        Setting::set('data_retention_days', $this->retention_days);

        session()->flash('message', 'Settings updated successfully.');
        $this->calculateCleanupStats();
    }

    public function confirmRunCleanup(): void
    {
        // Only run actual cleanup based on saved database settings, or immediately? 
        // We will run using the DataRetentionService which uses the DB setting by default.
        // It's safer to use the DB setting to avoid race conditions. Users should save first.
        $this->runCleanup(app(DataRetentionService::class));
    }

    public function showCleanupConfirm(): void
    {
        $this->dispatch('showConfirmModal', [
            'modalId' => 'cleanup-confirm',
            'title' => 'Run Data Cleanup',
            'message' => 'Are you sure you want to run cleanup? This will permanently delete ' . $this->visitsToDeleteCount . ' old visit records and ' . $this->visitorsToDeleteCount . ' orphaned visitors. This action cannot be undone.',
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
        $this->calculateCleanupStats();
    }

    public function render()
    {
        return view('livewire.admin.settings.business-details');
    }
}