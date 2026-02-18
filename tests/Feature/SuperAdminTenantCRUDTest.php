<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Livewire\Livewire;
use App\Livewire\Superadmin\TenantShow;

class SuperAdminTenantCRUDTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_super_admin_can_update_tenant_details()
    {
        $tenant = Tenant::create([
            'name' => 'Original Name',
            'slug' => 'original-slug',
            'plan' => 'free',
            'status' => 'active',
        ]);

        $superAdmin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Super Admin',
            'email' => 'admin@kiteflow.io',
            'password' => bcrypt('password'),
            'is_super_admin' => true,
        ]);

        $this->actingAs($superAdmin);

        Livewire::test(TenantShow::class, ['id' => $tenant->id])
            ->set('name', 'Updated Name')
            ->set('slug', 'updated-slug')
            ->set('plan', 'pro')
            ->set('status', 'active')
            ->call('save')
            ->assertHasNoErrors();

        $tenant->refresh();
        $this->assertEquals('Updated Name', $tenant->name);
        $this->assertEquals('updated-slug', $tenant->slug);
        $this->assertEquals('pro', $tenant->plan);
        
        $this->assertAuthenticatedAs($superAdmin);
    }
}
