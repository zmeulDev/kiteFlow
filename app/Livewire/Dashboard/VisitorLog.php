<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Visit;

class VisitorLog extends Component
{
    use WithPagination;

    public $search = '';

    #[On('visitor-pre-registered')]
    #[On('visitor-updated')]
    public function refresh()
    {
        // Simply refreshes the component
    }

    public function forceCheckout($visitId)
    {
        $visit = Visit::find($visitId);
        if ($visit && !$visit->checked_out_at) {
            $visit->update([
                'checked_out_at' => now(),
                'checked_in_at' => $visit->checked_in_at ?? now()->subMinutes(1)
            ]);
            $this->dispatch('visitor-updated');
            $this->dispatch('notify', type: 'success', message: 'Visitor checked out successfully.');
        }
    }

    public function exportCsv()
    {
        // Relying on TenantScope for automatic isolation
        $visits = Visit::with(['visitor', 'host'])->latest()->get();
        $filename = "kiteflow-visitors-" . now()->format('Y-m-d') . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($visits) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Visitor Name', 'Email', 'Host', 'Purpose', 'Check In', 'Check Out']);

            foreach ($visits as $visit) {
                fputcsv($file, [
                    $visit->visitor->full_name,
                    $visit->visitor->email,
                    $visit->host->name ?? 'System',
                    $visit->purpose,
                    $visit->checked_in_at,
                    $visit->checked_out_at ?? 'Still in building'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function render()
    {
        // Relying on TenantScope for automatic isolation (Hub owners see sub-tenants via scope logic)
        $visits = Visit::with(['visitor', 'host', 'tenant'])
            ->whereHas('visitor', function($query) {
                $query->where('first_name', 'like', "%{$this->search}%")
                      ->orWhere('last_name', 'like', "%{$this->search}%");
            })
            ->latest()
            ->paginate(10);

        return view('livewire.dashboard.visitor-log', [
            'visits' => $visits
        ]);
    }
}
