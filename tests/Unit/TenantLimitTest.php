<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Tenant;
use App\Models\Visitor;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class TenantLimitTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_detect_when_limit_is_reached()
    {
        $tenant = Tenant::create([
            'name' => 'Free Office',
            'slug' => 'free',
            'plan' => 'free'
        ]);

        $visitor = Visitor::create([
            'tenant_id' => $tenant->id,
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com'
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Host',
            'email' => 'host@example.com',
            'password' => bcrypt('password')
        ]);

        // Create 50 visits
        for ($i = 0; $i < 50; $i++) {
            Visit::create([
                'tenant_id' => $tenant->id,
                'visitor_id' => $visitor->id,
                'user_id' => $user->id,
                'purpose' => 'Test',
                'checked_in_at' => now()
            ]);
        }

        $this->assertTrue($tenant->hasReachedLimit());
    }
}
