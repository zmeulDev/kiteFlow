<?php

namespace Database\Factories;

use App\Models\Tenant;
use App\Models\TenantSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantSettingFactory extends Factory
{
    protected $model = TenantSetting::class;

    private static array $usedKeys = [];

    public function definition(): array
    {
        $settings = [
            ['key' => 'visitor.checkin_required_fields', 'type' => 'json', 'value' => ['first_name', 'last_name', 'company', 'purpose']],
            ['key' => 'visitor.badge_required', 'type' => 'boolean', 'value' => true],
            ['key' => 'visitor.photo_required', 'type' => 'boolean', 'value' => false],
            ['key' => 'visitor.id_verification_required', 'type' => 'boolean', 'value' => true],
            ['key' => 'visitor.nda_required', 'type' => 'boolean', 'value' => false],
            ['key' => 'meeting.auto_reminder_enabled', 'type' => 'boolean', 'value' => true],
            ['key' => 'meeting.reminder_minutes_before', 'type' => 'integer', 'value' => 15],
            ['key' => 'meeting.max_duration_hours', 'type' => 'integer', 'value' => 8],
            ['key' => 'meeting.allow_visitor_booking', 'type' => 'boolean', 'value' => false],
            ['key' => 'parking.enabled', 'type' => 'boolean', 'value' => true],
            ['key' => 'parking.auto_assign', 'type' => 'boolean', 'value' => true],
            ['key' => 'parking.hourly_rate', 'type' => 'float', 'value' => 5.00],
            ['key' => 'access.badge_expiry_days', 'type' => 'integer', 'value' => 365],
            ['key' => 'access.kiosk_enabled', 'type' => 'boolean', 'value' => true],
            ['key' => 'access.qr_code_enabled', 'type' => 'boolean', 'value' => true],
            ['key' => 'notifications.email_enabled', 'type' => 'boolean', 'value' => true],
            ['key' => 'notifications.sms_enabled', 'type' => 'boolean', 'value' => false],
            ['key' => 'company.time_zone', 'type' => 'string', 'value' => 'UTC'],
            ['key' => 'company.locale', 'type' => 'string', 'value' => 'en'],
            ['key' => 'security.blacklist_sync_enabled', 'type' => 'boolean', 'value' => false],
        ];

        // Get available keys that haven't been used
        $availableKeys = array_filter($settings, function ($s) {
            return !in_array($s['key'], self::$usedKeys);
        });

        // If all keys used, reset
        if (empty($availableKeys)) {
            self::$usedKeys = [];
            $availableKeys = $settings;
        }

        $setting = fake()->randomElement($availableKeys);
        self::$usedKeys[] = $setting['key'];

        return [
            'tenant_id' => Tenant::factory(),
            'key' => $setting['key'],
            'value' => $setting['value'],
            'type' => $setting['type'],
        ];
    }

    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant->id,
        ]);
    }

    public function visitorSetting(): static
    {
        $visitorSettings = [
            ['key' => 'visitor.checkin_required_fields', 'type' => 'json', 'value' => ['first_name', 'last_name', 'company', 'purpose']],
            ['key' => 'visitor.badge_required', 'type' => 'boolean', 'value' => true],
            ['key' => 'visitor.photo_required', 'type' => 'boolean', 'value' => false],
            ['key' => 'visitor.id_verification_required', 'type' => 'boolean', 'value' => true],
            ['key' => 'visitor.nda_required', 'type' => 'boolean', 'value' => false],
        ];

        $setting = fake()->randomElement($visitorSettings);

        return $this->state(fn (array $attributes) => [
            'key' => $setting['key'],
            'value' => $setting['value'],
            'type' => $setting['type'],
        ]);
    }

    public function meetingSetting(): static
    {
        $meetingSettings = [
            ['key' => 'meeting.auto_reminder_enabled', 'type' => 'boolean', 'value' => true],
            ['key' => 'meeting.reminder_minutes_before', 'type' => 'integer', 'value' => 15],
            ['key' => 'meeting.max_duration_hours', 'type' => 'integer', 'value' => 8],
            ['key' => 'meeting.allow_visitor_booking', 'type' => 'boolean', 'value' => false],
        ];

        $setting = fake()->randomElement($meetingSettings);

        return $this->state(fn (array $attributes) => [
            'key' => $setting['key'],
            'value' => $setting['value'],
            'type' => $setting['type'],
        ]);
    }

    public function parkingSetting(): static
    {
        $parkingSettings = [
            ['key' => 'parking.enabled', 'type' => 'boolean', 'value' => true],
            ['key' => 'parking.auto_assign', 'type' => 'boolean', 'value' => true],
            ['key' => 'parking.hourly_rate', 'type' => 'float', 'value' => 5.00],
        ];

        $setting = fake()->randomElement($parkingSettings);

        return $this->state(fn (array $attributes) => [
            'key' => $setting['key'],
            'value' => $setting['value'],
            'type' => $setting['type'],
        ]);
    }
}