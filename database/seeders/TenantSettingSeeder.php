<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\TenantSetting;
use Illuminate\Database\Seeder;

class TenantSettingSeeder extends Seeder
{
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // Visitor management settings
            TenantSetting::factory()->visitorSetting()->forTenant($tenant)->create([
                'key' => 'visitor.checkin_required_fields',
                'value' => ['first_name', 'last_name', 'company', 'purpose'],
                'type' => 'json',
            ]);

            TenantSetting::factory()->visitorSetting()->forTenant($tenant)->create([
                'key' => 'visitor.badge_required',
                'value' => true,
                'type' => 'boolean',
            ]);

            TenantSetting::factory()->visitorSetting()->forTenant($tenant)->create([
                'key' => 'visitor.photo_required',
                'value' => $tenant->slug === 'acme-corp',
                'type' => 'boolean',
            ]);

            TenantSetting::factory()->visitorSetting()->forTenant($tenant)->create([
                'key' => 'visitor.id_verification_required',
                'value' => true,
                'type' => 'boolean',
            ]);

            TenantSetting::factory()->visitorSetting()->forTenant($tenant)->create([
                'key' => 'visitor.nda_required',
                'value' => false,
                'type' => 'boolean',
            ]);

            // Meeting management settings
            TenantSetting::factory()->meetingSetting()->forTenant($tenant)->create([
                'key' => 'meeting.auto_reminder_enabled',
                'value' => true,
                'type' => 'boolean',
            ]);

            TenantSetting::factory()->meetingSetting()->forTenant($tenant)->create([
                'key' => 'meeting.reminder_minutes_before',
                'value' => 15,
                'type' => 'integer',
            ]);

            TenantSetting::factory()->meetingSetting()->forTenant($tenant)->create([
                'key' => 'meeting.max_duration_hours',
                'value' => 8,
                'type' => 'integer',
            ]);

            TenantSetting::factory()->meetingSetting()->forTenant($tenant)->create([
                'key' => 'meeting.allow_visitor_booking',
                'value' => false,
                'type' => 'boolean',
            ]);

            // Parking management settings
            TenantSetting::factory()->parkingSetting()->forTenant($tenant)->create([
                'key' => 'parking.enabled',
                'value' => true,
                'type' => 'boolean',
            ]);

            TenantSetting::factory()->parkingSetting()->forTenant($tenant)->create([
                'key' => 'parking.auto_assign',
                'value' => true,
                'type' => 'boolean',
            ]);

            TenantSetting::factory()->parkingSetting()->forTenant($tenant)->create([
                'key' => 'parking.hourly_rate',
                'value' => 5.00,
                'type' => 'float',
            ]);

            // Access control settings
            TenantSetting::factory()->forTenant($tenant)->create([
                'key' => 'access.badge_expiry_days',
                'value' => 365,
                'type' => 'integer',
            ]);

            TenantSetting::factory()->forTenant($tenant)->create([
                'key' => 'access.kiosk_enabled',
                'value' => true,
                'type' => 'boolean',
            ]);

            TenantSetting::factory()->forTenant($tenant)->create([
                'key' => 'access.qr_code_enabled',
                'value' => true,
                'type' => 'boolean',
            ]);

            // Notification settings
            TenantSetting::factory()->forTenant($tenant)->create([
                'key' => 'notifications.email_enabled',
                'value' => true,
                'type' => 'boolean',
            ]);

            TenantSetting::factory()->forTenant($tenant)->create([
                'key' => 'notifications.sms_enabled',
                'value' => false,
                'type' => 'boolean',
            ]);

            // Company settings
            TenantSetting::factory()->forTenant($tenant)->create([
                'key' => 'company.time_zone',
                'value' => 'UTC',
                'type' => 'string',
            ]);

            TenantSetting::factory()->forTenant($tenant)->create([
                'key' => 'company.locale',
                'value' => 'en',
                'type' => 'string',
            ]);

            // Security settings
            TenantSetting::factory()->forTenant($tenant)->create([
                'key' => 'security.blacklist_sync_enabled',
                'value' => false,
                'type' => 'boolean',
            ]);
        }

        $this->command->info('Tenant settings seeded successfully.');
    }
}