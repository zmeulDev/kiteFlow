<?php

namespace App\Livewire\Components;

use App\Models\Meeting;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Livewire\Component;

class SidebarStats extends Component
{
    public ?int $tenantId = null;

    public function mount(): void
    {
        $this->tenantId = auth()->user()?->getCurrentTenant()?->id;
    }

    public function getStatsProperty(): array
    {
        if (!$this->tenantId) {
            return [
                'visitors' => 0,
                'checked_in' => 0,
                'meetings_today' => 0,
            ];
        }

        return [
            'visitors' => Visitor::where('tenant_id', $this->tenantId)->count(),
            'checked_in' => VisitorVisit::where('tenant_id', $this->tenantId)
                ->whereNull('check_out_at')
                ->where('status', 'checked_in')
                ->count(),
            'meetings_today' => Meeting::where('tenant_id', $this->tenantId)
                ->whereDate('start_at', today())
                ->count(),
        ];
    }

    public function render()
    {
        return view('livewire.components.sidebar-stats', [
            'stats' => $this->stats,
        ]);
    }
}