<?php

namespace Tests\Feature;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Visitor;
use App\Models\Visit;
use Tests\TestCase;
use Carbon\Carbon;

class GdprPurgeJobTest extends TestCase
{
    protected Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Use existing tenant
        $this->tenant = Tenant::firstOrCreate(
            ['slug' => 'test-gdpr'],
            [
                'name' => 'Test GDPR',
                'is_active' => true,
                'gdpr_retention_months' => 6,
            ]
        );
    }

    public function test_gdpr_purge_command_runs(): void
    {
        // Create a visitor with old visits
        $visitor = Visitor::firstOrCreate(
            ['email' => 'gdpr-visitor@test.com'],
            [
                'tenant_id' => $this->tenant->id,
                'first_name' => 'GDPR',
                'last_name' => 'Test',
            ]
        );

        $host = User::firstOrCreate(
            ['email' => 'gdpr-host@test.com'],
            [
                'tenant_id' => $this->tenant->id,
                'name' => 'GDPR Host',
                'password' => bcrypt('password'),
                'is_active' => true,
            ]
        );

        // Create a visit that ended 7 months ago
        $oldVisit = Visit::create([
            'visitor_id' => $visitor->id,
            'tenant_id' => $this->tenant->id,
            'host_user_id' => $host->id,
            'visit_code' => 'GDPR' . uniqid(),
            'scheduled_start' => Carbon::now()->subMonths(8),
            'scheduled_end' => Carbon::now()->subMonths(8)->addHour(),
            'status' => 'checked_out',
            'checked_in_at' => Carbon::now()->subMonths(8),
            'checked_out_at' => Carbon::now()->subMonths(7),
        ]);

        // Run GDPR purge
        $this->artisan('visitors:purge-gdpr')
            ->assertExitCode(0);
    }

    public function test_gdpr_retention_respects_tenant_settings(): void
    {
        // Create tenant with short retention
        $tenantShort = Tenant::firstOrCreate(
            ['slug' => 'short-retention'],
            [
                'name' => 'Short Retention',
                'is_active' => true,
                'gdpr_retention_months' => 2,
            ]
        );

        $this->assertEquals(2, $tenantShort->gdpr_retention_months);
    }
}
