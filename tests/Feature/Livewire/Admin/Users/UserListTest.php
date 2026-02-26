<?php

namespace Tests\Feature\Livewire\Admin\Users;

use App\Livewire\Admin\Users\UserList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserListTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        Livewire::test(UserList::class)
            ->assertStatus(200)
            ->assertSee('Users');
    }

    public function test_can_create_user_with_valid_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $validRoles = array_keys(User::getRoles());
        $roleToTest = $validRoles[0];

        Livewire::test(UserList::class)
            ->set('name', 'Test User')
            ->set('email', 'testuser@example.com')
            ->set('password', 'password123')
            ->set('role', $roleToTest)
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors(['role']);

        $this->assertDatabaseHas('users', [
            'email' => 'testuser@example.com',
            'role' => $roleToTest,
        ]);
    }

    public function test_cannot_create_user_with_invalid_role()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        Livewire::test(UserList::class)
            ->set('name', 'Invalid User')
            ->set('email', 'invaliduser@example.com')
            ->set('password', 'password123')
            ->set('role', 'invalid_fake_role')
            ->set('is_active', true)
            ->call('save')
            ->assertHasErrors(['role']);

        $this->assertDatabaseMissing('users', [
            'email' => 'invaliduser@example.com',
        ]);
    }
}
