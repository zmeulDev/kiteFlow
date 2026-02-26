<?php

namespace App\Livewire\Admin\Companies;

use App\Models\Company;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CompanyEdit extends Component
{
    public Company $company;

    // Company form fields
    public string $name = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public string $contact_person = '';
    public ?string $contact_person_email = null;
    public ?string $contact_person_phone = null;
    public bool $is_active = true;
    public ?string $contract_start_date = null;
    public ?string $contract_end_date = null;
    public ?int $main_contact_user_id = null;

    // Company users list
    public $companyUsers = [];

    // User modal state
    public bool $showUserModal = false;
    public ?int $editingUserId = null;

    // User form fields
    public string $user_name = '';
    public string $user_email = '';
    public string $user_phone = '';
    public string $user_password = '';
    public string $user_role = 'viewer';
    public bool $user_is_active = true;
    public string $user_notes = '';

    public function mount(Company $company): void
    {
        if (!auth()->user()->isAdmin() && auth()->user()->company_id !== $company->id) {
            abort(403, 'Unauthorized action.');
        }

        $this->company = $company;
        $this->name = $company->name;
        $this->address = $company->address ?? '';
        $this->phone = $company->phone ?? '';
        $this->email = $company->email ?? '';
        $this->contact_person = $company->contact_person ?? '';
        $this->contact_person_email = $company->contact_person_email;
        $this->contact_person_phone = $company->contact_person_phone;
        $this->is_active = $company->is_active;
        $this->contract_start_date = $company->contract_start_date?->format('Y-m-d');
        $this->contract_end_date = $company->contract_end_date?->format('Y-m-d');
        $this->main_contact_user_id = $company->main_contact_user_id;

        $this->loadCompanyUsers();
    }

    public function loadCompanyUsers(): void
    {
        $this->companyUsers = User::where('company_id', $this->company->id)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone', 'role', 'is_active']);
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'contact_person' => 'nullable|string|max:255',
            'contact_person_email' => 'nullable|email|max:255',
            'contact_person_phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'contract_start_date' => 'nullable|date',
            'contract_end_date' => 'nullable|date|after_or_equal:contract_start_date',
            'main_contact_user_id' => 'nullable|exists:users,id',
        ];
    }

    protected function userRules(): array
    {
        $rules = [
            'user_name' => 'required|string|max:255',
            'user_email' => 'required|email|max:255|unique:users,email',
            'user_phone' => 'nullable|string|max:50',
            'user_password' => 'required|string|min:8',
            'user_role' => 'required|in:' . implode(',', array_keys(\App\Models\User::getRoles())),
            'user_is_active' => 'boolean',
            'user_notes' => 'nullable|string|max:1000',
        ];

        if ($this->editingUserId) {
            $rules['user_email'] = 'required|email|max:255|unique:users,email,' . $this->editingUserId;
            $rules['user_password'] = 'nullable|string|min:8';
        }

        return $rules;
    }

    public function save(): void
    {
        $this->validate();

        $this->company->update([
            'name' => $this->name,
            'address' => $this->address ?: null,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'contact_person' => $this->contact_person ?: null,
            'contact_person_email' => $this->contact_person_email ?: null,
            'contact_person_phone' => $this->contact_person_phone ?: null,
            'is_active' => $this->is_active,
            'contract_start_date' => $this->contract_start_date ?: null,
            'contract_end_date' => $this->contract_end_date ?: null,
            'main_contact_user_id' => $this->main_contact_user_id,
        ]);

        session()->flash('message', 'Company updated successfully.');
    }

    public function createCompanyUser(): void
    {
        $this->resetUserForm();
        $this->showUserModal = true;
    }

    public function editCompanyUser(int $userId): void
    {
        $user = User::findOrFail($userId);

        // Ensure user belongs to this company
        if ($user->company_id !== $this->company->id) {
            session()->flash('error', 'User does not belong to this company.');
            return;
        }

        $this->editingUserId = $user->id;
        $this->user_name = $user->name;
        $this->user_email = $user->email;
        $this->user_phone = $user->phone ?? '';
        $this->user_role = $user->role;
        $this->user_is_active = $user->is_active;
        $this->user_notes = $user->notes ?? '';
        $this->user_password = '';
        $this->showUserModal = true;
    }

    public function saveUser(): void
    {
        $this->validate($this->userRules());

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $data = [
                'name' => $this->user_name,
                'email' => $this->user_email,
                'phone' => $this->user_phone ?: null,
                'role' => $this->user_role,
                'is_active' => $this->user_is_active,
                'company_id' => $this->company->id,
                'notes' => $this->user_notes ?: null,
            ];
            if ($this->user_password) {
                $data['password'] = bcrypt($this->user_password);
            }
            $user->update($data);
            session()->flash('message', 'User updated successfully.');
        } else {
            // Prevent non-admin tenants from upgrading to God Mode (already blocked in UserList as well, adding here for consistency)
            if (!auth()->user()->isAdmin() && $this->user_role === 'admin') {
                $this->user_role = 'viewer';
            }

            User::create([
                'name' => $this->user_name,
                'email' => $this->user_email,
                'password' => bcrypt($this->user_password),
                'phone' => $this->user_phone ?: null,
                'role' => $this->user_role,
                'is_active' => $this->user_is_active,
                'company_id' => $this->company->id,
                'notes' => $this->user_notes ?: null,
            ]);
            session()->flash('message', 'User created successfully.');
        }

        $this->showUserModal = false;
        $this->resetUserForm();
        $this->loadCompanyUsers();
    }

    public function resetUserForm(): void
    {
        $this->editingUserId = null;
        $this->user_name = '';
        $this->user_email = '';
        $this->user_phone = '';
        $this->user_password = '';
        $this->user_role = 'viewer';
        $this->user_is_active = true;
        $this->user_notes = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.admin.companies.company-edit');
    }
}