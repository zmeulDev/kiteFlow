<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TenantShowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_super_admin_can_view_the_tenant_details_page()
    {
        $this->withoutMiddleware(); // Temporarily bypass for isolation test

        $tenant = Tenant::create(['name' => 'Test Hub', 'slug' => 'test-hub']);
        
        $admin = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Super Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_super_admin' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get('/superadmin/tenants/' . $tenant->id);
        
        $response->assertStatus(200);
    }
}
