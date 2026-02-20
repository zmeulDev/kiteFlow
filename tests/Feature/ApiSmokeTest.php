<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\Building;
use App\Models\MeetingRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $tenant;
    protected User $user;
    protected string $token;

    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations
        $this->artisan('migrate', ['--force' => true]);

        // Create tenant
        $this->tenant = Tenant::create([
            'name' => 'Test Company',
            'slug' => 'test-company-' . uniqid(),
            'is_active' => true,
            'gdpr_retention_months' => 6,
        ]);

        // Create user
        $this->user = User::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Test User',
            'email' => 'test' . uniqid() . '@example.com',
            'password' => bcrypt('password'),
            'role' => 'tenant_admin',
            'is_active' => true,
        ]);

        // Get token
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);
        
        $this->token = $response->json('token') ?? '';
    }

    // ========== AUTH TESTS ==========

    public function test_login_returns_token(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(422);
    }

    public function test_me_returns_current_user(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJson(['email' => $this->user->email]);
    }

    // ========== TENANT TESTS ==========

    public function test_can_create_tenant(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/tenants', [
                'name' => 'New Tenant',
                'gdpr_retention_months' => 6,
            ]);

        $response->assertStatus(201);
    }

    public function test_can_list_visitors(): void
    {
        Visitor::create([
            'tenant_id' => $this->tenant->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@test.com',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/visitors');

        $response->assertStatus(200);
    }

    public function test_can_create_visitor(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/visitors', [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'jane@test.com',
                'phone' => '+1234567890',
            ]);

        $response->assertStatus(201);
    }

    public function test_can_create_visit(): void
    {
        $visitor = Visitor::create([
            'tenant_id' => $this->tenant->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/visits', [
                'tenant_id' => $this->tenant->id,
                'visitor' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                ],
                'scheduled_start' => now()->addDay()->format('Y-m-d H:i:s'),
                'scheduled_end' => now()->addDay()->addHour()->format('Y-m-d H:i:s'),
                'purpose' => 'Business meeting',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['visit']);
    }

    public function test_can_check_in_visit(): void
    {
        $visitor = Visitor::create([
            'tenant_id' => $this->tenant->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $visit = Visit::create([
            'visitor_id' => $visitor->id,
            'tenant_id' => $this->tenant->id,
            'visit_code' => 'TEST001',
            'scheduled_start' => now(),
            'scheduled_end' => now()->addHour(),
            'status' => 'pre_registered',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/v1/visits/{$visit->id}/check-in", [
                'checked_in_by' => $this->user->id,
            ]);

        $response->assertStatus(200);
        $this->assertEquals('checked_in', $visit->fresh()->status);
    }

    public function test_can_check_out_visit(): void
    {
        $visitor = Visitor::create([
            'tenant_id' => $this->tenant->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $visit = Visit::create([
            'visitor_id' => $visitor->id,
            'tenant_id' => $this->tenant->id,
            'visit_code' => 'TEST002',
            'scheduled_start' => now()->subHours(2),
            'scheduled_end' => now()->subHour(),
            'status' => 'checked_in',
            'checked_in_at' => now()->subHours(2),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson("/api/v1/visits/{$visit->id}/check-out", [
                'checked_out_by' => $this->user->id,
            ]);

        $response->assertStatus(200);
        $this->assertEquals('checked_out', $visit->fresh()->status);
    }

    public function test_can_generate_qr_code(): void
    {
        $visitor = Visitor::create([
            'tenant_id' => $this->tenant->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $visit = Visit::create([
            'visitor_id' => $visitor->id,
            'tenant_id' => $this->tenant->id,
            'visit_code' => 'QR001',
            'scheduled_start' => now()->addDay(),
            'scheduled_end' => now()->addDay()->addHour(),
            'status' => 'pre_registered',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson("/api/v1/visits/{$visit->id}/qr");

        $response->assertStatus(200)
            ->assertJsonStructure(['visit_code', 'qr_code']);
    }

    public function test_analytics_quick_stats(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->getJson('/api/v1/analytics/quick');

        $response->assertStatus(200);
    }

    // ========== BUILDING & ROOM TESTS ==========

    public function test_can_create_building(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/buildings', [
                'tenant_id' => $this->tenant->id,
                'name' => 'Main Office',
                'address' => '123 Test St',
            ]);

        $response->assertStatus(201);
    }

    public function test_can_create_meeting_room(): void
    {
        $building = Building::create([
            'tenant_id' => $this->tenant->id,
            'name' => 'Main Office',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->token)
            ->postJson('/api/v1/meeting-rooms', [
                'tenant_id' => $this->tenant->id,
                'building_id' => $building->id,
                'name' => 'Conference Room A',
                'capacity' => 10,
            ]);

        $response->assertStatus(201);
    }

    // ========== KIOSK PUBLIC TESTS ==========

    public function test_kiosk_visit_lookup(): void
    {
        $visitor = Visitor::create([
            'tenant_id' => $this->tenant->id,
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $visit = Visit::create([
            'visitor_id' => $visitor->id,
            'tenant_id' => $this->tenant->id,
            'visit_code' => 'KIOSK01',
            'scheduled_start' => now()->addDay(),
            'scheduled_end' => now()->addDay()->addHour(),
            'status' => 'pre_registered',
        ]);

        $response = $this->getJson("/api/v1/kiosk/visits/{$visit->visit_code}");

        $response->assertStatus(200)
            ->assertJson(['visit_code' => 'KIOSK01']);
    }
}
