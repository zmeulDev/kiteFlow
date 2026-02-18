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

class KiteFlowSaaSTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tenant_data_is_strictly_isolated()
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'slug' => 'a']);
        $visitorA = Visitor::create(['tenant_id' => $tenantA->id, 'first_name' => 'A', 'last_name' => 'Visitor', 'email' => 'a@example.com']);
        
        $tenantB = Tenant::create(['name' => 'Tenant B', 'slug' => 'b']);
        $visitorB = Visitor::create(['tenant_id' => $tenantB->id, 'first_name' => 'B', 'last_name' => 'Visitor', 'email' => 'b@example.com']);

        session(['tenant_id' => $tenantA->id]);
        $this->assertEquals(1, Visitor::count());
        $this->assertEquals('A', Visitor::first()->first_name);

        session(['tenant_id' => $tenantB->id]);
        $this->assertEquals(1, Visitor::count());
        $this->assertEquals('B', Visitor::first()->first_name);
    }

    #[Test]
    public function free_plan_enforces_monthly_limit()
    {
        $tenant = Tenant::create(['name' => 'Free Hub', 'slug' => 'free', 'plan' => 'free']);
        
        $visitor = Visitor::create(['tenant_id' => $tenant->id, 'first_name' => 'T', 'last_name' => 'V', 'email' => 't@v.com']);
        $user = User::create(['tenant_id' => $tenant->id, 'name' => 'A', 'email' => 'a@f.com', 'password' => bcrypt('password')]);

        for ($i = 0; $i < 50; $i++) {
            Visit::create(['tenant_id' => $tenant->id, 'visitor_id' => $visitor->id, 'user_id' => $user->id, 'purpose' => 'T', 'checked_in_at' => now()]);
        }

        $this->assertTrue($tenant->hasReachedLimit());

        Livewire::test(CheckIn::class)
            ->set('selected_company', $tenant->id)
            ->set('first_name', 'Denied')
            ->set('last_name', 'Visitor')
            ->set('purpose', 'Business')
            ->call('submit')
            ->assertDispatched('notify', function ($event, $data) {
                return $data['type'] === 'error' && str_contains($data['message'], 'reached its visitor limit');
            });
    }
}
