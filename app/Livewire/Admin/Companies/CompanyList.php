<?php

namespace App\Livewire\Admin\Companies;

use App\Models\Company;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class CompanyList extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingCompanyId = null;

    public string $name = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public string $contact_person = '';
    public bool $is_active = true;

    public function mount(): void
    {
        abort_if(!auth()->user()->can('viewCompanies', \App\Models\User::class), 403);
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ];
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function createCompany(): void
    {
        abort_if(!auth()->user()->can('manageCompanies', \App\Models\User::class), 403);
        $this->resetForm();
        $this->showModal = true;
    }

    public function editCompany(int $companyId): void
    {
        abort_if(!auth()->user()->can('manageCompanies', \App\Models\User::class), 403);
        $query = Company::query();
        if (!auth()->user()->canManageAllTenants()) {
            $query->whereIn('id', auth()->user()->getManagedCompanyIds());
        }
        $company = $query->findOrFail($companyId);
        
        $this->editingCompanyId = $company->id;
        $this->name = $company->name;
        $this->address = $company->address ?? '';
        $this->phone = $company->phone ?? '';
        $this->email = $company->email ?? '';
        $this->contact_person = $company->contact_person ?? '';
        $this->is_active = $company->is_active;
        $this->showModal = true;
    }

    public function save(): void
    {
        abort_if(!auth()->user()->can('manageCompanies', \App\Models\User::class), 403);
        $this->validate();

        if ($this->editingCompanyId) {
            $query = Company::query();
            if (!auth()->user()->canManageAllTenants()) {
                $query->whereIn('id', auth()->user()->getManagedCompanyIds());
            }
            $query->findOrFail($this->editingCompanyId)->update([
                'name' => $this->name,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->email,
                'contact_person' => $this->contact_person,
                'is_active' => $this->is_active,
            ]);
            session()->flash('message', 'Company updated successfully.');
        } else {
            $data = [
                'name' => $this->name,
                'address' => $this->address,
                'phone' => $this->phone,
                'email' => $this->email,
                'contact_person' => $this->contact_person,
                'is_active' => $this->is_active,
            ];

            // Auto-set parent_id for non-global managers (Company Admin creating sub-tenants)
            if (!auth()->user()->canManageAllTenants()) {
                $data['parent_id'] = auth()->user()->company_id;
            }

            Company::create($data);
            session()->flash('message', 'Company created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    #[On('confirmDeleteCompany')]
    public function confirmDeleteCompany(int $companyId): void
    {
        $this->deleteCompany($companyId);
    }

    public function showDeleteConfirm(int $companyId, string $companyName): void
    {
        $this->dispatch('showConfirmModal', [
            'modalId' => 'delete-company',
            'title' => 'Delete Company',
            'message' => "Are you sure you want to delete \"{$companyName}\"? This action cannot be undone.",
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmMethod' => 'confirmDeleteCompany',
            'confirmColor' => 'danger',
            'params' => ['companyId' => $companyId],
        ]);
    }

    public function deleteCompany(int $companyId): void
    {
        abort_if(!auth()->user()->can('manageCompanies', \App\Models\User::class), 403);
        $query = Company::query();
        if (!auth()->user()->canManageAllTenants()) {
            $query->whereIn('id', auth()->user()->getManagedCompanyIds());
        }
        $query->findOrFail($companyId)->delete();
        session()->flash('message', 'Company deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->editingCompanyId = null;
        $this->name = '';
        $this->address = '';
        $this->phone = '';
        $this->email = '';
        $this->contact_person = '';
        $this->is_active = true;
    }

    public function render()
    {
        $query = Company::query();
        if (!auth()->user()->canManageAllTenants()) {
            $query->whereIn('id', auth()->user()->getManagedCompanyIds());
        }

        $companies = $query->clone()
            ->when($this->search, fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', "%{$this->search}%")
                ->orWhere('contact_person', 'like', "%{$this->search}%")))
            ->orderBy('name')
            ->paginate(10);

        // Stats for header
        $totalCompanies = $query->clone()->count();
        $activeCompanies = $query->clone()->where('is_active', true)->count();
        $inactiveCompanies = $query->clone()->where('is_active', false)->count();

        return view('livewire.admin.companies.company-list', compact(
            'companies',
            'totalCompanies',
            'activeCompanies',
            'inactiveCompanies'
        ));
    }
}