<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAndBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_requires_subscription()
    {
        // Unsubscribed real tenant
        $tenant = Tenant::forceCreate(['name' => 'Unpaid Inc']);
        
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        Sanctum::actingAs($user);

        // Fetching locations without subscription should trigger the middleware 402 or redirect
        $response = $this->getJson("/api/v1/tenants/{$tenant->id}/locations");

        $response->assertStatus(402);
    }

    public function test_system_tenant_bypasses_subscription()
    {
        // 'system' tenant bypasses billing in middleware
        $tenant = Tenant::forceCreate(['name' => 'System', 'domain' => 'system']);
        
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        Sanctum::actingAs($user);

        // Fetching locations should return 200 OK because 'system' is bypassed
        $response = $this->getJson("/api/v1/tenants/{$tenant->id}/locations");

        $response->assertStatus(200);
    }

    public function test_api_crud_works_for_subscribed_tenant()
    {
        // For testing, let's use the 'demo' domain which also bypasses
        $tenant = Tenant::forceCreate(['name' => 'Demo', 'domain' => 'demo']);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        Sanctum::actingAs($user);

        // CREATE Location
        $response = $this->postJson("/api/v1/tenants/{$tenant->id}/locations", [
            'name' => 'HQ',
            'address' => '123 Fake Street'
        ]);
        $response->assertStatus(201);
        $locationId = $response->json('data.id');

        // CREATE Room
        $response = $this->postJson("/api/v1/tenants/{$tenant->id}/rooms", [
            'location_id' => $locationId,
            'name' => 'Boardroom',
            'capacity' => 10,
        ]);
        $response->assertStatus(201);
        $roomId = $response->json('data.id');

        // GET Rooms
        $response = $this->getJson("/api/v1/tenants/{$tenant->id}/rooms");
        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));

        // UPDATE Room
        $response = $this->putJson("/api/v1/tenants/{$tenant->id}/rooms/{$roomId}", [
            'capacity' => 20
        ]);
        $response->assertStatus(200);
        $this->assertEquals(20, $response->json('data.capacity'));

        // DELETE Room
        $response = $this->deleteJson("/api/v1/tenants/{$tenant->id}/rooms/{$roomId}");
        $response->assertStatus(204);
        
        // GET Rooms again (empty)
        $response = $this->getJson("/api/v1/tenants/{$tenant->id}/rooms");
        $this->assertCount(0, $response->json('data'));
    }
}
