<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\CheckIn;
use Tests\TestCase;
use Carbon\Carbon;

class AutoCheckoutJobTest extends TestCase
{
    protected Tenant $tenant;
    protected User $host;
    protected Visitor $visitor;

    protected function setUp(): void
    {
        parent::setUp();

        // Use existing tenant or create one
        $this->tenant = Tenant::firstOrCreate(
            ['slug' => 'test-autocheckout'],
            [
                'name' => 'Test AutoCheckout',
                'is_active' => true,
                'gdpr_retention_months' => 6,
            ]
        );

        $this->host = User::firstOrCreate(
            ['email' => 'host-autocheckout@test.com'],
            [
                'tenant_id' => $this->tenant->id,
                'name' => 'Host User',
                'password' => bcrypt('password'),
                'role' => 'tenant_admin',
                'is_active' => true,
            ]
        );

        $this->visitor = Visitor::firstOrCreate(
            ['email' => 'visitor-autocheckout@test.com'],
            [
                'tenant_id' => $this->tenant->id,
                'first_name' => 'John',
                'last_name' => 'Doe',
            ]
        );
    }

    public function test_auto_checkout_command_runs(): void
    {
        // Create a visit that ended 2 hours ago
        $visit = Visit::create([
            'visitor_id' => $this->visitor->id,
            'tenant_id' => $this->tenant->id,
            'host_user_id' => $this->host->id,
            'visit_code' => 'AUTO' . uniqid(),
            'scheduled_start' => Carbon::now()->subHours(3),
            'scheduled_end' => Carbon::now()->subHours(2),
            'status' => 'checked_in',
            'checked_in_at' => Carbon::now()->subHours(3),
        ]);

        CheckIn::create([
            'visit_id' => $visit->id,
            'visitor_id' => $this->visitor->id,
            'checked_in_by' => $this->host->id,
            'check_in_time' => Carbon::now()->subHours(3),
        ]);

        // Run the command
        $this->artisan('visitors:auto-checkout')
            ->assertExitCode(0);

        // Verify visit was checked out
        $visit->refresh();
        $this->assertEquals('checked_out', $visit->status);
    }

    public function test_auto_checkout_does_not_affect_future_visits(): void
    {
        $visit = Visit::create([
            'visitor_id' => $this->visitor->id,
            'tenant_id' => $this->tenant->id,
            'host_user_id' => $this->host->id,
            'visit_code' => 'FUTURE' . uniqid(),
            'scheduled_start' => Carbon::now()->addDay(),
            'scheduled_end' => Carbon::now()->addDay()->addHour(),
            'status' => 'pre_registered',
        ]);

        $this->artisan('visitors:auto-checkout')
            ->assertExitCode(0);

        $visit->refresh();
        $this->assertEquals('pre_registered', $visit->status);
    }
}
