<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Livewire\Livewire;
use App\Livewire\Superadmin\TenantRegistration;

class SuperAdminTenantRegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_super_admin_can_manually_register_a_tenant()
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@kiteflow.io',
            'password' => bcrypt('password'),
            'is_super_admin' => true,
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(TenantRegistration::class)
            ->set('company_name', 'Manual Corp')
            ->set('admin_name', 'Manual Admin')
            ->set('admin_email', 'admin@manual.com')
            ->set('admin_password', 'secret-password')
            ->call('register')
            ->assertHasNoErrors()
            ->assertDispatched('tenantUpdated');

        $this->assertDatabaseHas('tenants', [
            'name' => 'Manual Corp',
            'slug' => 'manual-corp',
        ]);

        $tenant = Tenant::where('slug', 'manual-corp')->first();

        $this->assertDatabaseHas('users', [
            'tenant_id' => $tenant->id,
            'name' => 'Manual Admin',
            'email' => 'admin@manual.com',
        ]);
    }
}
