<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Livewire;
use App\Livewire\Kiosk\CheckIn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class KioskCheckInTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_visitor_can_check_in_at_the_kiosk()
    {
        $tenant = Tenant::create([
            'name' => 'Test Hub',
            'slug' => 'test-hub',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Host User',
            'email' => 'host@example.com',
            'password' => bcrypt('password'),
        ]);

        Livewire::test(CheckIn::class)
            ->set('first_name', 'Jane')
            ->set('last_name', 'Smith')
            ->set('selected_company', $tenant->id)
            ->set('purpose', 'Business Meeting')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('visitors', [
            'first_name' => 'Jane',
            'last_name' => 'Smith',
        ]);

        $this->assertDatabaseHas('visits', [
            'purpose' => 'Business Meeting',
            'tenant_id' => $tenant->id,
        ]);
    }
}
