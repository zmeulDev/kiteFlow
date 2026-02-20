<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tenant;
use App\Models\MeetingRoom;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_public_routes_render()
    {
        $this->get('/')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
    }

    public function test_dashboard_routes_render_for_authenticated_user()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $user->assignRole('admin');

        $routes = [
            '/dashboard',
            '/calendar',
            '/rooms',
            '/settings',
            '/profile',
            '/notifications',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($user)->get($route);
            $response->assertStatus(200, "Route {$route} failed to render.");
        }
    }

    public function test_sub_tenants_api_route_renders()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'role' => 'tenant_admin']);

        // Check if API route is accessible
        $response = $this->actingAs($user)->getJson('/api/v1/sub-tenants');
        $response->assertStatus(200);
    }

    public function test_superadmin_api_routes_render_for_superadmin()
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create(['tenant_id' => $tenant->id, 'role' => 'super_admin']);

        // Tenants list
        $this->actingAs($user)->getJson('/api/v1/tenants')->assertStatus(200);

        // Tenant detail
        $targetTenant = Tenant::factory()->create();
        $this->actingAs($user)->getJson("/api/v1/tenants/{$targetTenant->id}")->assertStatus(200);
    }

    public function test_kiosk_routes_render()
    {
        $tenant = Tenant::factory()->create();
        
        // Kiosk Main
        $this->get("/kiosk/{$tenant->slug}")->assertStatus(200);

        // Fast Pass (simulated token)
        $token = 'test-token-123';
        $this->get("/check-in/{$token}")->assertStatus(200);
    }
}
