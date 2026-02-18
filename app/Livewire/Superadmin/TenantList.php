<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class TenantList extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $planFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'planFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function impersonate($id)
    {
        $tenant = Tenant::findOrFail($id);
        $admin = User::where('tenant_id', $tenant->id)->first();
        
        if ($admin) {
            session()->put('impersonator_id', auth()->id());
            Auth::login($admin);
            session()->put('tenant_id', $tenant->id);
            return redirect()->route('dashboard');
        }
        
        $this->dispatch('notify', type: 'error', message: 'No admin user found for this tenant.');
    }

    #[On('tenantUpdated')]
    public function refresh()
    {
        // Handled by render
    }

    public function render()
    {
        $query = Tenant::query()
            ->whereNull('parent_id') // Only show Main Tenants in God Mode
            ->withCount('visits')
            ->when($this->search, function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_email', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->planFilter, fn($q) => $q->where('plan', $this->planFilter))
            ->latest();

        return view('livewire.superadmin.tenant-list', [
            'tenants' => $query->paginate(10)
        ]);
    }
}
