<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Location;
use App\Models\MeetingRoom;
use Laravel\Sanctum\Sanctum;

class ApiContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_login_and_receive_sanctum_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        
        $response->assertStatus(200)->assertJsonStructure(['token', 'user']);
    }

    public function test_can_fetch_tenant_rooms(): void
    {
        $tenant = Tenant::forceCreate(['name' => 'Demo', 'domain' => 'demo']);
        $user = User::factory()->create(['tenant_id' => $tenant->id]);
        $location = Location::forceCreate(['tenant_id' => $tenant->id, 'name' => 'HQ']);
        MeetingRoom::forceCreate(['tenant_id' => $tenant->id, 'location_id' => $location->id, 'name' => 'Room 1']);
        
        Sanctum::actingAs($user);
        
        $response = $this->getJson("/api/v1/tenants/{$tenant->id}/rooms");
        
        $response->assertStatus(200)->assertJsonCount(1, 'data');
    }
}
