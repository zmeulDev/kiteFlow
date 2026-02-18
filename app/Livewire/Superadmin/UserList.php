<?php

namespace App\Livewire\Superadmin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserList extends Component
{
    use WithPagination;

    public $search = '';
    public $showUserModal = false;
    public $showDeleteModal = false;
    public $editingUserId = null;
    public $deleteId = null;

    // Form fields
    public $name = '';
    public $email = '';
    public $password = '';
    public $selectedRoles = [];

    protected function rules()
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $this->editingUserId,
            'selectedRoles' => 'array'
        ];

        if (!$this->editingUserId) {
            $rules['password'] = 'required|min:8';
        }

        return $rules;
    }

    public function createUser()
    {
        $this->reset(['name', 'email', 'password', 'selectedRoles', 'editingUserId']);
        $this->showUserModal = true;
    }

    public function editUser($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $this->editingUserId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->selectedRoles = $user->roles->pluck('name')->toArray();
        $this->showUserModal = true;
    }

    public function saveUser()
    {
        $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $userData['password'] = bcrypt($this->password);
        }

        if ($this->editingUserId) {
            $user = User::findOrFail($this->editingUserId);
            $user->update($userData);
        } else {
            $user = User::create($userData);
        }

        $user->syncRoles($this->selectedRoles);

        $this->showUserModal = false;
        $this->reset(['name', 'email', 'password', 'selectedRoles', 'editingUserId']);
        $this->dispatch('notify', type: 'success', message: 'User saved successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function deleteConfirmed()
    {
        User::findOrFail($this->deleteId)->delete();
        $this->showDeleteModal = false;
        $this->reset(['deleteId']);
        $this->dispatch('notify', type: 'success', message: 'User deleted successfully.');
    }

    public function render()
    {
        $users = User::with(['tenant', 'roles'])
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate(10);
            
        $roles = Role::all();

        return view('livewire.superadmin.user-list', [
            'users' => $users,
            'roles' => $roles
        ]);
    }
}
