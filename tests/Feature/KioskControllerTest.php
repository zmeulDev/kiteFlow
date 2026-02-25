<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Entrance;
use App\Models\KioskSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KioskControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_kiosk_index_displays_kiosk_view_for_active_entrance(): void
    {
        $building = Building::factory()->create();
        $entrance = Entrance::factory()->create([
            'building_id' => $building->id,
            'kiosk_identifier' => 'test-kiosk-123',
            'is_active' => true,
        ]);
        KioskSetting::factory()->create(['entrance_id' => $entrance->id]);

        $response = $this->get("/kiosk/{$entrance->kiosk_identifier}");

        $response->assertOk();
        $response->assertViewIs('kiosk');
        $response->assertViewHas('entrance');
        $this->assertTrue($response->viewData('entrance')->is($entrance));
    }

    public function test_kiosk_index_loads_entrance_with_relationships(): void
    {
        $entrance = Entrance::factory()->create(['is_active' => true]);
        KioskSetting::factory()->create(['entrance_id' => $entrance->id]);

        $response = $this->get("/kiosk/{$entrance->kiosk_identifier}");

        $response->assertOk();
        $entranceData = $response->viewData('entrance');
        $this->assertTrue($entranceData->relationLoaded('building'));
        $this->assertTrue($entranceData->relationLoaded('kioskSetting'));
    }

    public function test_kiosk_index_returns_404_for_inactive_entrance(): void
    {
        $entrance = Entrance::factory()->create([
            'kiosk_identifier' => 'inactive-kiosk',
            'is_active' => false,
        ]);

        $response = $this->get("/kiosk/{$entrance->kiosk_identifier}");

        $response->assertNotFound();
    }

    public function test_kiosk_index_returns_404_for_nonexistent_identifier(): void
    {
        $response = $this->get('/kiosk/nonexistent-identifier');

        $response->assertNotFound();
    }

    public function test_check_in_displays_checkin_view_for_active_entrance(): void
    {
        $entrance = Entrance::factory()->create(['is_active' => true]);
        KioskSetting::factory()->create(['entrance_id' => $entrance->id]);

        $response = $this->get("/kiosk/{$entrance->kiosk_identifier}/check-in");

        $response->assertOk();
        $response->assertViewIs('kiosk-checkin');
        $response->assertViewHas('entrance');
    }

    public function test_check_in_returns_404_for_inactive_entrance(): void
    {
        $entrance = Entrance::factory()->create(['is_active' => false]);

        $response = $this->get("/kiosk/{$entrance->kiosk_identifier}/check-in");

        $response->assertNotFound();
    }

    public function test_check_out_displays_checkout_view_for_active_entrance(): void
    {
        $entrance = Entrance::factory()->create(['is_active' => true]);
        KioskSetting::factory()->create(['entrance_id' => $entrance->id]);

        $response = $this->get("/kiosk/{$entrance->kiosk_identifier}/checkout");

        $response->assertOk();
        $response->assertViewIs('kiosk-checkout');
        $response->assertViewHas('entrance');
    }

    public function test_check_out_returns_404_for_inactive_entrance(): void
    {
        $entrance = Entrance::factory()->create(['is_active' => false]);

        $response = $this->get("/kiosk/{$entrance->kiosk_identifier}/checkout");

        $response->assertNotFound();
    }
}