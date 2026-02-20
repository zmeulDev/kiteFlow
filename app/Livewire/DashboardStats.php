<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Building;
use App\Models\MeetingRoom;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardStats extends Component
{
    public array $stats = [];
    public array $recentVisits = [];
    public array $todayVisits = [];
    public string $activeTab = 'overview';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $tenantId = Auth::user()->tenant_id;
        
        $this->stats = [
            'today_total' => Visit::where('tenant_id', $tenantId)->whereDate('created_at', today())->count(),
            'checked_in' => Visit::where('tenant_id', $tenantId)->where('status', 'checked_in')->count(),
            'checked_out' => Visit::where('tenant_id', $tenantId)->where('status', 'checked_out')->count(),
            'pre_registered' => Visit::where('tenant_id', $tenantId)->where('status', 'pre_registered')->count(),
            'total_visitors' => Visitor::where('tenant_id', $tenantId)->count(),
            'total_rooms' => MeetingRoom::where('tenant_id', $tenantId)->count(),
            'total_buildings' => Building::where('tenant_id', $tenantId)->count(),
        ];

        $this->todayVisits = Visit::where('tenant_id', $tenantId)
            ->whereDate('scheduled_start', today())
            ->with(['visitor', 'hostUser', 'meetingRoom'])
            ->orderBy('scheduled_start')
            ->limit(10)
            ->get()
            ->toArray();

        $this->recentVisits = Visit::where('tenant_id', $tenantId)
            ->with(['visitor', 'hostUser', 'meetingRoom'])
            ->latest()
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
