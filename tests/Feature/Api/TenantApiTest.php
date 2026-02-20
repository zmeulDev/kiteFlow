<?php

namespace Tests\Feature\Api;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\Building;
use App\Models\MeetingRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create super admin user
        $this->superAdmin = User::factory()->superAdmin()->create();
    }

    public function test_tenant_index_returns_200(): void
    {
        Tenant::factory()->count(3)->create();

        $response = $this->actingAs($this->superAdmin)
            ->getJson('/api/v1/tenants');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'is_active']
                ]
            ]);
    }

    public function test_tenant_can_be_created(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson('/api/v1/tenants', [
                'name' => 'Test Company',
                'email' => 'contact@testcompany.com',
                'phone' => '+1234567890',
                'gdpr_retention_months' => 12,
            ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Company']);

        $this->assertDatabaseHas('tenants', [
            'name' => 'Test Company',
            'slug' => 'test-company',
        ]);
    }

    public function test_tenant_update_returns_200(): void
    {
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->putJson("/api/v1/tenants/{$tenant->id}", [
                'name' => 'Updated Company',
                'gdpr_retention_months' => 3,
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Company']);

        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Updated Company',
            'gdpr_retention_months' => 3,
        ]);
    }

    public function test_tenant_delete_returns_200(): void
    {
        $tenant = Tenant::factory()->create();

        $response = $this->actingAs($this->superAdmin)
            ->deleteJson("/api/v1/tenants/{$tenant->id}");

        $response->assertStatus(200);
        $this->assertSoftDeleted('tenants', ['id' => $tenant->id]);
    }
}
