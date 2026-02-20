<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\SubTenant;
use App\Models\User;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Building;
use App\Models\MeetingRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class SubTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_admin_cannot_access_other_tenant_visits()
    {
        // Tenant A
        $tenantA = Tenant::factory()->create();
        $userA = User::factory()->create(['tenant_id' => $tenantA->id]);
        $roleA = Role::firstOrCreate(['name' => 'tenant_admin']);
        $userA->assignRole($roleA);

        $visitorA = Visitor::factory()->create(['tenant_id' => $tenantA->id]);
        $buildingA = Building::factory()->create(['tenant_id' => $tenantA->id]);
        $roomA = MeetingRoom::factory()->create(['building_id' => $buildingA->id]);

        $visitA = Visit::factory()->create([
            'tenant_id' => $tenantA->id,
            'visitor_id' => $visitorA->id,
            'meeting_room_id' => $roomA->id,
        ]);

        // Tenant B
        $tenantB = Tenant::factory()->create();
        $visitorB = Visitor::factory()->create(['tenant_id' => $tenantB->id]);
        $buildingB = Building::factory()->create(['tenant_id' => $tenantB->id]);
        $roomB = MeetingRoom::factory()->create(['building_id' => $buildingB->id]);

        $visitB = Visit::factory()->create([
            'tenant_id' => $tenantB->id,
            'visitor_id' => $visitorB->id,
            'meeting_room_id' => $roomB->id,
        ]);

        // Act As Tenant A Admin
        $response = $this->actingAs($userA)->getJson('/api/v1/visits/' . $visitB->id);
        
        // Assert they cannot see Tenant B's visit
        $response->assertStatus(403);
        
        // Assert they CAN see their own visit
        $responseOwn = $this->actingAs($userA)->getJson('/api/v1/visits/' . $visitA->id);
        $responseOwn->assertStatus(200);
    }
}
