<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed roles and permissions
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_super_admin_can_access_superadmin_dashboard()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $user->assignRole('super-admin');

        $response = $this->actingAs($user)->get('/superadmin');

        $response->assertStatus(200);
    }

    public function test_regular_admin_cannot_access_superadmin_dashboard()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get('/superadmin');

        $response->assertStatus(403);
    }

    public function test_receptionist_can_access_sub_tenants_route()
    {
        $tenant = Tenant::factory()->create(['is_hub' => true]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $user->assignRole('receptionist');

        $response = $this->actingAs($user)->get('/sub-tenants');

        $response->assertStatus(200);
    }
    
    public function test_employee_cannot_access_sub_tenants_route()
    {
        $tenant = Tenant::factory()->create(['is_hub' => true]);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $user->assignRole('employee');

        $response = $this->actingAs($user)->get('/sub-tenants');

        $response->assertStatus(403);
    }
}
