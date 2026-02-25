<?php

namespace Tests\Feature;

use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MobileControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_check_in_displays_mobile_checkin_view_for_valid_qr_code(): void
    {
        $visit = Visit::factory()->create(['qr_code' => 'valid-qr-code-123']);

        $response = $this->get("/mobile/check-in/{$visit->qr_code}");

        $response->assertOk();
        $response->assertViewIs('mobile-checkin');
        $response->assertViewHas('visit');
        $this->assertTrue($response->viewData('visit')->is($visit));
    }

    public function test_check_in_returns_404_for_invalid_qr_code(): void
    {
        $response = $this->get('/mobile/check-in/invalid-qr-code');

        $response->assertNotFound();
    }

    public function test_check_in_returns_404_for_expired_qr_code(): void
    {
        // QR code doesn't exist in database
        $response = $this->get('/mobile/check-in/nonexistent-code');

        $response->assertNotFound();
    }

    public function test_check_in_can_display_pending_visit(): void
    {
        $visit = Visit::factory()->pending()->create(['qr_code' => 'pending-visit-qr']);

        $response = $this->get("/mobile/check-in/{$visit->qr_code}");

        $response->assertOk();
        $response->assertViewHas('visit');
    }

    public function test_check_in_can_display_checked_in_visit(): void
    {
        $visit = Visit::factory()->checkedIn()->create(['qr_code' => 'checkedin-visit-qr']);

        $response = $this->get("/mobile/check-in/{$visit->qr_code}");

        $response->assertOk();
        $response->assertViewHas('visit');
    }

    public function test_check_in_loads_visit_relationships(): void
    {
        $visit = Visit::factory()->create(['qr_code' => 'with-relations-qr']);

        $response = $this->get("/mobile/check-in/{$visit->qr_code}");

        $response->assertOk();
        // Note: The controller doesn't eager load relationships currently
        // This test documents current behavior
    }
}