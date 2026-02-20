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
}
