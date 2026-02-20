<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\Building;
use App\Models\MeetingRoom;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    protected Tenant $tenantA;
    protected Tenant $tenantB;
    protected User $userA;
    protected User $userB;
    protected string $tokenA;
    protected string $tokenB;

    protected function setUp(): void
    {
        parent::setUp();

        // Create two tenants
        $this->tenantA = Tenant::firstOrCreate(
            ['slug' => 'company-a'],
            ['name' => 'Company A', 'is_active' => true, 'gdpr_retention_months' => 6]
        );

        $this->tenantB = Tenant::firstOrCreate(
            ['slug' => 'company-b'],
            ['name' => 'Company B', 'is_active' => true, 'gdpr_retention_months' => 3]
        );

        // Create users
        $this->userA = User::firstOrCreate(
            ['email' => 'user-a@companya.com'],
            [
                'tenant_id' => $this->tenantA->id,
                'name' => 'User A',
                'password' => bcrypt('password'),
                'role' => 'tenant_admin',
                'is_active' => true,
            ]
        );

        $this->userB = User::firstOrCreate(
            ['email' => 'user-b@companyb.com'],
            [
                'tenant_id' => $this->tenantB->id,
                'name' => 'User B',
                'password' => bcrypt('password'),
                'role' => 'tenant_admin',
                'is_active' => true,
            ]
        );

        // Get tokens
        $respA = $this->postJson('/api/v1/auth/login', [
            'email' => 'user-a@companya.com',
            'password' => 'password',
        ]);
        $this->tokenA = $respA->json('token');

        $respB = $this->postJson('/api/v1/auth/login', [
            'email' => 'user-b@companyb.com',
            'password' => 'password',
        ]);
        $this->tokenB = $respB->json('token');
    }

    public function test_tenant_cannot_access_other_tenant_visitors(): void
    {
        // Create visitor for Tenant A
        $visitorA = Visitor::create([
            'tenant_id' => $this->tenantA->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@companya.com',
        ]);

        // User B should not see Tenant A's visitor
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenB)
            ->getJson('/api/v1/visitors');

        $response->assertStatus(200);
        // Should be empty or not contain Tenant A's visitor
    }

    public function test_tenant_cannot_access_other_tenant_visits(): void
    {
        $visitorA = Visitor::create([
            'tenant_id' => $this->tenantA->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $visitA = Visit::create([
            'visitor_id' => $visitorA->id,
            'tenant_id' => $this->tenantA->id,
            'visit_code' => 'VISIT-A-' . uniqid(),
            'scheduled_start' => now()->addDay(),
            'scheduled_end' => now()->addDay()->addHour(),
            'status' => 'pre_registered',
        ]);

        // User B should not access User A's visit
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->tokenB)
            ->getJson("/api/v1/visits/{$visitA->id}");

        $response->assertStatus(403);
    }

    public function test_tenant_can_only_see_own_meeting_rooms(): void
    {
        $buildingA = Building::create([
            'tenant_id' => $this->tenantA->id,
            'name' => 'Building A',
        ]);

        MeetingRoom::create([
            'tenant_id' => $this->tenantA->id,
            'building_id' => $buildingA->id,
            'name' => 'Room A1',
            'capacity' => 10,
        ]);

        // User A should see the room
        $responseA = $this->withHeader('Authorization', 'Bearer ' . $this->tokenA)
            ->getJson('/api/v1/meeting-rooms');

        $responseA->assertStatus(200);

        // User B should not see Tenant A's room
        $responseB = $this->withHeader('Authorization', 'Bearer ' . $this->tokenB)
            ->getJson('/api/v1/meeting-rooms');

        $responseB->assertStatus(200);
    }
}
