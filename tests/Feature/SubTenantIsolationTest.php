<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Auth;

class SubTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function sub_tenant_manager_only_sees_their_data()
    {
        $tenantA = Tenant::create(['name' => 'Tenant A', 'slug' => 'a']);
        $visitorA = Visitor::create(['tenant_id' => $tenantA->id, 'first_name' => 'A', 'last_name' => 'Visitor', 'email' => 'a@example.com']);
        $userA = User::create(['tenant_id' => $tenantA->id, 'name' => 'User A', 'email' => 'a@example.com', 'password' => bcrypt('password')]);
        
        $tenantB = Tenant::create(['name' => 'Tenant B', 'slug' => 'b']);
        $visitorB = Visitor::create(['tenant_id' => $tenantB->id, 'first_name' => 'B', 'last_name' => 'Visitor', 'email' => 'b@example.com']);
        $userB = User::create(['tenant_id' => $tenantB->id, 'name' => 'User B', 'email' => 'b@example.com', 'password' => bcrypt('password')]);

        // Log in as User B
        $this->actingAs($userB);
        
        // Check isolation
        $this->assertEquals(1, Visitor::count(), "User B should only see 1 visitor");
        $this->assertEquals('B', Visitor::first()->first_name);
    }
}
