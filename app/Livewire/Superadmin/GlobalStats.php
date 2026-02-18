<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\Visit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GlobalStats extends Component
{
    public function render()
    {
        $totalTenants = Tenant::whereNull('parent_id')->count();
        $totalVisits = Visit::count();
        $activeTrials = Tenant::whereNull('parent_id')->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now())->count();
        
        // Placeholder for MRR (Monthly Recurring Revenue) based on plans
        $mrr = Tenant::whereNull('parent_id')->where('status', 'active')
            ->get()
            ->sum(function($tenant) {
                return match($tenant->plan) {
                    'pro' => 49,
                    'enterprise' => 199,
                    default => 0,
                };
            });

        // Weekly Growth
        $newTenantsThisWeek = Tenant::where('created_at', '>=', now()->startOfWeek())->count();

        return view('livewire.superadmin.global-stats', [
            'totalTenants' => $totalTenants,
            'totalVisits' => $totalVisits,
            'activeTrials' => $activeTrials,
            'mrr' => $mrr,
            'newTenantsThisWeek' => $newTenantsThisWeek,
        ]);
    }
}
