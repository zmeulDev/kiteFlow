<?php

namespace App\Livewire\Settings;

use App\Models\ActivityLog;
use Livewire\Component;
use Livewire\WithPagination;

class SystemLogs extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterAction = '';
    public string $filterUser = '';
    public string $filterDateFrom = '';
    public string $filterDateTo = '';

    protected $queryString = ['search', 'filterAction', 'filterUser'];

    public function getLogsProperty()
    {
        return ActivityLog::with(['user', 'tenant'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('description', 'like', "%{$this->search}%")
                        ->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$this->search}%"));
                });
            })
            ->when($this->filterAction, function ($query) {
                $query->where('action', $this->filterAction);
            })
            ->when($this->filterUser, function ($query) {
                $query->where('user_id', $this->filterUser);
            })
            ->when($this->filterDateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->filterDateFrom);
            })
            ->when($this->filterDateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->filterDateTo);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(30);
    }

    public function getActionTypesProperty()
    {
        return [
            'login' => 'Login',
            'logout' => 'Logout',
            'create' => 'Create',
            'update' => 'Update',
            'delete' => 'Delete',
            'view' => 'View',
            'check_in' => 'Check In',
            'check_out' => 'Check Out',
            'blacklist' => 'Blacklist',
            'export' => 'Export',
        ];
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterAction', 'filterUser', 'filterDateFrom', 'filterDateTo']);
    }

    public function render()
    {
        // Only super-admins can access system logs
        if (!auth()->user()->isSuperAdmin()) {
            abort(403, 'Access denied. Only Super Admins can view system logs.');
        }

        return view('livewire.settings.system-logs', [
            'logs' => $this->logs,
            'actionTypes' => $this->actionTypes,
        ])->layout('layouts.app');
    }
}