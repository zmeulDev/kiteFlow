<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Tenant;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_loads_kiosk_mode(): void
    {
        $tenant = Tenant::forceCreate(['name' => 'Test']);
        $response = $this->get("/kiosk/{$tenant->id}");
        $response->assertStatus(200);
    }

    public function test_loads_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_loads_visitor_profiles(): void
    {
        $response = $this->get('/admin/visitors');
        $response->assertStatus(200);
    }

    public function test_loads_sub_tenant_dashboard(): void
    {
        // Create parent tenant
        $parent = Tenant::forceCreate(['name' => 'Parent Tenant', 'domain' => 'parent-' . uniqid()]);
        // Create sub-tenant
        $subTenant = Tenant::forceCreate([
            'name' => 'Sub Tenant',
            'domain' => 'sub-' . uniqid(),
            'parent_id' => $parent->id,
        ]);

        $response = $this->get("/admin/dashboard?tenant_id={$subTenant->id}");
        $response->assertStatus(200);
        // Sub-tenant should see Business Details tab
        $response->assertSee('Business Details');
    }

    public function test_loads_main_tenant_dashboard(): void
    {
        // Create a main tenant (no parent_id)
        $tenant = Tenant::forceCreate([
            'name' => 'Main Tenant',
            'domain' => 'main-test-' . uniqid(),
        ]);

        $response = $this->get("/admin/dashboard?tenant_id={$tenant->id}");
        $response->assertStatus(200);
        // Main tenant should see Settings & Rooms and Users tabs
        $response->assertSee('Settings');
        $response->assertSee('Users');
    }
}
