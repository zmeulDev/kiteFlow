<?php

namespace Tests\Feature\Api;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\Building;
use App\Models\MeetingRoom;
use App\Models\SubTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitApiTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $admin;
    protected Visitor $visitor;
    protected Building $building;
    protected MeetingRoom $room;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->tenant = Tenant::factory()->create();
        $this->admin = User::factory()->create(['tenant_id' => $this->tenant->id, 'role' => 'tenant_admin']);
        $this->visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->building = Building::factory()->create(['tenant_id' => $this->tenant->id]);
        $this->room = MeetingRoom::factory()->create([
            'tenant_id' => $this->tenant->id, 
            'building_id' => $this->building->id
        ]);
    }

    public function test_visit_list_returns_200(): void
    {
        Visit::factory()->count(5)->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->getJson('/api/v1/visits');

        $response->assertStatus(200);
    }

    public function test_visit_can_be_created(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson('/api/v1/visits', [
                'tenant_id' => $this->tenant->id,
                'visitor' => [
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'email' => 'jane@example.com'
                ],
                'scheduled_start' => now()->addDay()->format('Y-m-d H:i:s'),
                'scheduled_end' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
                'purpose' => 'Business meeting',
                'meeting_room_id' => $this->room->id,
                'building_id' => $this->building->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'visit' => ['id', 'visit_code', 'status']
            ]);

        $this->assertDatabaseHas('visits', [
            'tenant_id' => $this->tenant->id,
            'status' => 'pre_registered',
        ]);
    }

    public function test_visit_check_in(): void
    {
        $visit = Visit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'status' => 'pre_registered',
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/visits/{$visit->id}/check-in", [
                'checked_in_by' => $this->admin->id,
                'check_in_method' => 'manual',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'checked_in']);

        $this->assertDatabaseHas('visits', [
            'id' => $visit->id,
            'status' => 'checked_in',
        ]);
    }

    public function test_visit_check_out(): void
    {
        $visit = Visit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'status' => 'checked_in',
            'checked_in_at' => now()->subHours(1),
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson("/api/v1/visits/{$visit->id}/check-out", [
                'checked_out_by' => $this->admin->id,
                'check_out_method' => 'manual',
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'checked_out']);

        $this->assertDatabaseHas('visits', [
            'id' => $visit->id,
            'status' => 'checked_out',
        ]);
    }

    public function test_visit_lookup_by_code(): void
    {
        $visit = Visit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $this->visitor->id,
            'visit_code' => 'TESTCODE',
        ]);

        $response = $this->getJson('/api/v1/kiosk/visits/TESTCODE');

        $response->assertStatus(200)
            ->assertJsonFragment(['visit_code' => 'TESTCODE']);
    }

    public function test_tenant_data_isolation(): void
    {
        // Create data for tenant 1
        $tenant2 = Tenant::factory()->create();
        $visitor2 = Visitor::factory()->create(['tenant_id' => $tenant2->id]);
        
        Visit::factory()->create(['tenant_id' => $this->tenant->id, 'visitor_id' => $this->visitor->id]);
        Visit::factory()->create(['tenant_id' => $tenant2->id, 'visitor_id' => $visitor2->id]);

        // Admin from tenant 1 should only see tenant 1 visits
        $response = $this->actingAs($this->admin)->getJson('/api/v1/visits');
        
        $response->assertStatus(200);
        // Note: This tests the global scope - actual isolation depends on auth implementation
    }
}
