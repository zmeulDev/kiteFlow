<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class UserList extends Component
{
    use WithPagination;

    public string $search = '';
    public string $role_filter = '';
    public string $company_filter = '';
    public bool $showModal = false;
    public ?int $editingUserId = null;

    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $role = 'viewer';
    public bool $is_active = true;
    public ?int $company_id = null;
    public string $notes = '';

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'nullable|string|max:50',
            'password' => 'required|string|min:8',
            'role' => 'required|in:' . implode(',', array_keys(\App\Models\User::getRoles())),
            'is_active' => 'boolean',
            'company_id' => 'nullable|exists:companies,id',
            'notes' => 'nullable|string|max:1000',
        ];

        if ($this->editingUserId) {
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $this->editingUserId;
            $rules['password'] = 'nullable|string|min:8';
        }

        return $rules;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function createUser(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editUser(int $userId): void
    {
        $query = User::query();
        if (!auth()->user()->isAdmin()) {
            $query->where('company_id', auth()->user()->company_id);
        }
        $user = $query->findOrFail($userId);
        
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->role = $user->role;
        $this->is_active = $user->is_active;
        $this->company_id = $user->company_id;
        $this->notes = $user->notes ?? '';
        $this->password = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingUserId) {
            $query = User::query();
            if (!auth()->user()->isAdmin()) {
                $query->where('company_id', auth()->user()->company_id);
            }
            $user = $query->findOrFail($this->editingUserId);
            
            // Re-enforce company_id for non-admins to prevent them moving a user to another company
            if (!auth()->user()->isAdmin()) {
                $this->company_id = auth()->user()->company_id;
            }

            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone ?: null,
                'role' => $this->role,
                'is_active' => $this->is_active,
                'company_id' => $this->company_id,
                'notes' => $this->notes ?: null,
            ];
            if ($this->password) {
                $data['password'] = bcrypt($this->password);
            }
            $user->update($data);
            session()->flash('message', 'User updated successfully.');
        } else {
            // Force scoped properties
            if (!auth()->user()->isAdmin()) {
                $this->company_id = auth()->user()->company_id;
                
                // Tenants should not be able to create God Mode admins
                if ($this->role === 'admin') {
                    $this->role = 'viewer'; 
                }
            }

            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'phone' => $this->phone ?: null,
                'role' => $this->role,
                'is_active' => $this->is_active,
                'company_id' => $this->company_id,
                'notes' => $this->notes ?: null,
            ]);
            session()->flash('message', 'User created successfully.');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    #[On('confirmDeleteUser')]
    public function confirmDeleteUser(int $userId): void
    {
        $this->deleteUser($userId);
    }

    public function showDeleteConfirm(int $userId, string $userName): void
    {
        if ($userId === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $this->dispatch('showConfirmModal', [
            'modalId' => 'delete-user',
            'title' => 'Delete User',
            'message' => "Are you sure you want to delete \"{$userName}\"? This action cannot be undone.",
            'confirmText' => 'Delete',
            'cancelText' => 'Cancel',
            'confirmMethod' => 'confirmDeleteUser',
            'confirmColor' => 'danger',
            'params' => ['userId' => $userId],
        ]);
    }

    public function deleteUser(int $userId): void
    {
        if ($userId === auth()->id()) {
            session()->flash('error', 'You cannot delete your own account.');
            return;
        }

        $query = User::query();
        if (!auth()->user()->isAdmin()) {
            $query->where('company_id', auth()->user()->company_id);
        }
        $query->findOrFail($userId)->delete();
        session()->flash('message', 'User deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->role = 'viewer';
        $this->is_active = true;
        $this->company_id = null;
        $this->notes = '';
    }

    public function render()
    {
        $query = User::query()->with('company');
        
        if (!auth()->user()->isAdmin()) {
            $query->where('company_id', auth()->user()->company_id);
        }

        $users = $query->clone()
            ->when($this->search, fn($q) => $q->where(fn($sq) => $sq->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")))
            ->when($this->role_filter, fn($q) => $q->where('role', $this->role_filter))
            ->when($this->company_filter !== '', function($q) {
                if ($this->company_filter === 'global') {
                    return $q->whereNull('company_id');
                }
                return $q->where('company_id', $this->company_filter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Stats for header
        $totalUsers = $query->clone()->count();
        $activeUsers = $query->clone()->where('is_active', true)->count();
        $adminUsers = $query->clone()->where('role', 'admin')->count();

        $companiesQuery = \App\Models\Company::where('is_active', true)->orderBy('name');
        if (!auth()->user()->isAdmin()) {
            $companiesQuery->where('id', auth()->user()->company_id);
        }
        $companies = $companiesQuery->get();

        return view('livewire.admin.users.user-list', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'adminUsers',
            'companies'
        ));
    }
}