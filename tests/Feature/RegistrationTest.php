<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Shared\RegistrationFlow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_new_user_can_register_their_company()
    {
        Livewire::test(RegistrationFlow::class)
            ->set('company_name', 'Acme Spaces')
            ->set('admin_name', 'Acme Admin')
            ->set('email', 'admin@acme.com')
            ->set('password', 'secret-password')
            ->call('register')
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('tenants', [
            'name' => 'Acme Spaces',
            'slug' => 'acme-spaces',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Acme Admin',
            'email' => 'admin@acme.com',
        ]);
    }
}
