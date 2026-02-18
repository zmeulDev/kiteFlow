<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class VisitorCheckInTest extends TestCase
{
    use RefreshDatabase;

    public function test_kiosk_main_page_renders()
    {
        $tenant = Tenant::factory()->create(['slug' => 'acme-inc']);
        
        $response = $this->get("/kiosk/{$tenant->slug}");
        // KioskMain is a Livewire component, so we check if the route is reachable
        $response->assertStatus(200);
    }

    public function test_fast_pass_check_in_screen_renders()
    {
        $response = $this->get('/check-in/some-valid-token');
        
        $response->assertStatus(200);
        $response->assertViewIs('kiosk-fast-pass');
        $response->assertViewHas('token', 'some-valid-token');
    }
}
