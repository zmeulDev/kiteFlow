<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use Livewire\Livewire;
use App\Livewire\Kiosk\CheckIn;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class KioskFlowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_visitor_can_complete_check_in_at_the_kiosk()
    {
        $tenant = Tenant::create([
            'name' => 'KiteFlow Hub',
            'slug' => 'kiteflow-hub',
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Manager',
            'email' => 'manager@kiteflow.io',
            'password' => bcrypt('password'),
        ]);

        Livewire::test(CheckIn::class)
            ->set('first_name', 'Alex')
            ->set('last_name', 'Vance')
            ->set('selected_company', $tenant->id)
            ->set('purpose', 'Interview')
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('visitors', [
            'first_name' => 'Alex',
            'last_name' => 'Vance',
        ]);

        $this->assertDatabaseHas('visits', [
            'purpose' => 'Interview',
            'tenant_id' => $tenant->id,
        ]);
    }

    #[Test]
    public function it_fails_check_in_if_required_fields_are_missing()
    {
        Livewire::test(CheckIn::class)
            ->call('submit')
            ->assertHasErrors(['first_name', 'last_name', 'selected_company', 'purpose']);
    }
}
