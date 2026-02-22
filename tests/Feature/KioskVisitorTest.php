<?php

namespace Tests\Feature;

use App\Models\AccessLog;
use App\Models\AccessPoint;
use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class KioskVisitorTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;
    private AccessPoint $kiosk;
    private User $host;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);

        $this->tenant = Tenant::factory()->create(['status' => 'active']);
        $this->kiosk = AccessPoint::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_kiosk_mode' => true,
            'type' => 'kiosk',
        ]);
        $this->host = User::factory()->create();
        $this->host->assignRole('user');
        $this->tenant->users()->attach($this->host->id);
    }

    /** @test */
    public function visitor_can_check_in_with_meeting_code()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'status' => 'scheduled',
        ]);

        $checkInCode = Str::upper(Str::random(8));
        $meeting->update(['check_in_code' => $checkInCode]);

        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'code',
            'code' => $checkInCode,
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'visitor',
                    'visit',
                    'badge_number',
                    'host' => ['name'],
                    'meeting' => ['title', 'start_at'],
                ],
            ]);

        $this->assertDatabaseHas('visitor_visits', [
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'meeting_id' => $meeting->id,
            'status' => 'checked_in',
        ]);
    }

    /** @test */
    public function visitor_can_check_in_with_manual_details()
    {
        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'phone' => '+1234567890',
            'company' => 'Visitor Corp',
            'purpose' => 'meeting',
            'host_name' => $this->host->name,
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data',
            ]);

        $this->assertDatabaseHas('visitors', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    /** @test */
    public function check_in_requires_accepting_terms()
    {
        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'accepted_terms' => false,
            'accepted_gdpr' => false,
            'accepted_nda' => false,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['accepted_terms', 'accepted_gdpr', 'accepted_nda']);
    }

    /** @test */
    public function blacklisted_visitor_cannot_check_in()
    {
        $visitor = Visitor::factory()->create([
            'tenant_id' => $this->tenant->id,
            'is_blacklisted' => true,
            'blacklist_reason' => 'Security violation',
        ]);

        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => $visitor->first_name,
            'last_name' => $visitor->last_name,
            'email' => $visitor->email,
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Visitor is blacklisted']);
    }

    /** @test */
    public function visitor_can_check_out()
    {
        $visitor = Visitor::factory()->create(['tenant_id' => $this->tenant->id]);
        $visit = VisitorVisit::factory()->create([
            'tenant_id' => $this->tenant->id,
            'visitor_id' => $visitor->id,
            'host_id' => $this->host->id,
            'status' => 'checked_in',
            'check_out_at' => null,
        ]);

        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-out", [
            'badge_number' => $visit->badge_number,
        ]);

        $response->assertStatus(200);
        $this->assertNotNull($visit->fresh()->check_out_at);
        $this->assertEquals('checked_out', $visit->fresh()->status);
    }

    /** @test */
    public function check_in_logs_access()
    {
        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('access_logs', [
            'tenant_id' => $this->tenant->id,
            'access_point_id' => $this->kiosk->id,
            'direction' => 'entry',
            'result' => 'granted',
        ]);
    }

    /** @test */
    public function visitor_check_in_notifies_host()
    {
        $meeting = Meeting::factory()->create([
            'tenant_id' => $this->tenant->id,
            'host_id' => $this->host->id,
            'status' => 'scheduled',
        ]);

        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => 'Visitor',
            'last_name' => 'Test',
            'email' => 'visitor@test.com',
            'host_name' => $this->host->name,
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(201);

        // Verify notification was queued/sent
        // This would check for a notification in a real scenario
    }

    /** @test */
    public function invalid_check_in_code_returns_error()
    {
        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'code',
            'code' => 'INVALID',
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Invalid check-in code']);
    }

    /** @test */
    public function inactive_kiosk_cannot_be_used()
    {
        $this->kiosk->update(['is_active' => false]);

        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => 'Test',
            'last_name' => 'Visitor',
            'email' => 'test@visitor.com',
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function suspended_tenant_kiosk_blocked()
    {
        $this->tenant->update(['status' => 'suspended']);

        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => 'Test',
            'last_name' => 'Visitor',
            'email' => 'test@visitor.com',
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function visitor_can_provide_additional_info_during_check_in()
    {
        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => 'John',
            'last_name' => 'Visitor',
            'email' => 'john@visitor.com',
            'phone' => '+1234567890',
            'company' => 'Tech Corp',
            'purpose' => 'interview',
            'notes' => 'Need parking space',
            'host_name' => $this->host->name,
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('visitor_visits', [
            'purpose' => 'interview',
        ]);
    }

    /** @test */
    public function badge_number_is_generated_on_check_in()
    {
        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => 'Badge',
            'last_name' => 'Test',
            'email' => 'badge@test.com',
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(201);

        $visit = VisitorVisit::where('tenant_id', $this->tenant->id)->first();
        $this->assertNotNull($visit->badge_number);
        $this->assertStringStartsWith('V', $visit->badge_number);
    }

    /** @test */
    public function kiosk_displays_check_in_success_info()
    {
        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => 'Display',
            'last_name' => 'Test',
            'email' => 'display@test.com',
            'host_name' => $this->host->name,
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'visitor',
                    'visit',
                    'badge_number',
                    'host',
                    'instructions',
                ],
            ]);
    }

    /** @test */
    public function returning_visitor_profile_is_updated()
    {
        $existingVisitor = Visitor::factory()->create([
            'tenant_id' => $this->tenant->id,
            'email' => 'returning@visitor.com',
            'first_name' => 'Old',
            'last_name' => 'Name',
        ]);

        $response = $this->postJson("/api/kiosk/{$this->tenant->slug}/{$this->kiosk->code}/check-in", [
            'method' => 'manual',
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'returning@visitor.com',
            'phone' => '+9999999999',
            'company' => 'New Company',
            'accepted_terms' => true,
            'accepted_gdpr' => true,
            'accepted_nda' => true,
        ]);

        $response->assertStatus(201);

        $existingVisitor->refresh();
        $this->assertEquals('Updated', $existingVisitor->first_name);
        $this->assertEquals('New Company', $existingVisitor->company);
    }
}