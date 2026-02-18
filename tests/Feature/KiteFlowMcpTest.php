<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use App\Mcp\Servers\KiteFlowServer;
use App\Mcp\Tools\GetActiveVisitorsTool;
use App\Mcp\Tools\CheckOutVisitorTool;
use App\Mcp\Tools\InviteGuestTool;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class KiteFlowMcpTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_active_visitors_via_mcp()
    {
        $tenant = Tenant::create(['name' => 'Hub', 'slug' => 'hub']);
        $user = User::create(['tenant_id' => $tenant->id, 'name' => 'Host', 'email' => 'h@t.com', 'password' => 'pass']);
        $visitor = Visitor::create(['tenant_id' => $tenant->id, 'first_name' => 'Active', 'last_name' => 'User', 'email' => 'a@v.com']);
        
        Visit::create([
            'tenant_id' => $tenant->id,
            'visitor_id' => $visitor->id,
            'user_id' => $user->id,
            'purpose' => 'Meeting',
            'checked_in_at' => now()
        ]);

        $response = KiteFlowServer::tool(GetActiveVisitorsTool::class);

        $response->assertOk()
            ->assertSee('Active Visitors')
            ->assertSee('Active User');
    }

    #[Test]
    public function it_can_check_out_a_visitor_via_mcp()
    {
        $tenant = Tenant::create(['name' => 'Hub', 'slug' => 'hub']);
        $user = User::create(['tenant_id' => $tenant->id, 'name' => 'Host', 'email' => 'h@t.com', 'password' => 'pass']);
        $visitor = Visitor::create(['tenant_id' => $tenant->id, 'first_name' => 'Exit', 'last_name' => 'User', 'email' => 'e@v.com']);
        
        Visit::create([
            'tenant_id' => $tenant->id,
            'visitor_id' => $visitor->id,
            'user_id' => $user->id,
            'purpose' => 'Meeting',
            'checked_in_at' => now()
        ]);

        $response = KiteFlowServer::tool(CheckOutVisitorTool::class, [
            'email' => 'e@v.com'
        ]);

        $response->assertOk()
            ->assertSee('Successfully checked out');

        $this->assertNotNull(Visit::first()->checked_out_at);
    }

    #[Test]
    public function it_can_invite_a_guest_via_mcp()
    {
        $tenant = Tenant::create(['name' => 'Hub', 'slug' => 'hub']);
        User::create(['tenant_id' => $tenant->id, 'name' => 'Admin', 'email' => 'a@t.com', 'password' => 'pass']);

        $response = KiteFlowServer::tool(InviteGuestTool::class, [
            'first_name' => 'New',
            'last_name' => 'Guest',
            'email' => 'new@guest.com',
            'purpose' => 'Demo'
        ]);

        $response->assertOk()
            ->assertSee('invited successfully');

        $this->assertDatabaseHas('visitors', ['email' => 'new@guest.com']);
        $this->assertDatabaseHas('visits', ['purpose' => 'Demo', 'checked_in_at' => null]);
    }
}
