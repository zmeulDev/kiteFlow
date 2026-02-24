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
    public bool $showModal = false;
    public ?int $editingUserId = null;

    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $role = 'viewer';
    public bool $is_active = true;

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,receptionist,viewer',
            'is_active' => 'boolean',
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
        $user = User::findOrFail($userId);
        $this->editingUserId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->is_active = $user->is_active;
        $this->password = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $data = [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'is_active' => $this->is_active,
            ];
            if ($this->password) {
                $data['password'] = bcrypt($this->password);
            }
            $user->update($data);
            session()->flash('message', 'User updated successfully.');
        } else {
            User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'role' => $this->role,
                'is_active' => $this->is_active,
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

        User::findOrFail($userId)->delete();
        session()->flash('message', 'User deleted successfully.');
    }

    public function resetForm(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = 'viewer';
        $this->is_active = true;
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%"))
            ->when($this->role_filter, fn($q) => $q->where('role', $this->role_filter))
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Stats for header
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $adminUsers = User::where('role', 'admin')->count();

        return view('livewire.admin.users.user-list', compact(
            'users',
            'totalUsers',
            'activeUsers',
            'adminUsers'
        ));
    }
}