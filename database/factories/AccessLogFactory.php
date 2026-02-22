<?php

namespace Database\Factories;

use App\Models\AccessLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AccessLogFactory extends Factory
{
    protected $model = AccessLog::class;

    public function definition(): array
    {
        $directions = ['entry', 'exit'];
        $results = ['granted', 'denied'];
        $subjectType = fake()->randomElement([\App\Models\User::class, \App\Models\Visitor::class]);
        $subjectId = fake()->numberBetween(1, 100); // Default value, should be overridden

        return [
            'uuid' => Str::uuid(),
            'tenant_id' => null, // Will be set by other methods
            'access_point_id' => null, // Will be set by caller
            'subject_type' => $subjectType,
            'subject_id' => 0, // Default value, should be overridden
            'direction' => fake()->randomElement($directions),
            'accessed_at' => now()->subHours(fake()->numberBetween(1, 168)),
            'result' => fake()->randomElement($results),
            'denial_reason' => null,
            'metadata' => [
                'method' => fake()->randomElement(['badge', 'qr_code', 'pin', 'manual']),
                'device_info' => fake()->userAgent(),
            ],
        ];
    }

    public function forTenant($tenantId): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenantId instanceof \App\Models\Tenant ? $tenantId->id : $tenantId,
        ]);
    }

    public function forAccessPoint($accessPointId): static
    {
        return $this->state(fn (array $attributes) => [
            'access_point_id' => $accessPointId instanceof \App\Models\AccessPoint ? $accessPointId->id : $accessPointId,
        ]);
    }

    public function forUser($user): static
    {
        return $this->state(fn (array $attributes) => [
            'subject_type' => \App\Models\User::class,
            'subject_id' => $user instanceof \App\Models\User ? $user->id : $user,
            'tenant_id' => $user instanceof \App\Models\User ? ($user->tenants()->first()->id ?? null) : null,
        ]);
    }

    public function forVisitor($visitor): static
    {
        return $this->state(fn (array $attributes) => [
            'subject_type' => \App\Models\Visitor::class,
            'subject_id' => $visitor instanceof \App\Models\Visitor ? $visitor->id : $visitor,
            'tenant_id' => $visitor instanceof \App\Models\Visitor ? $visitor->tenant_id : null,
        ]);
    }

    public function entry(): static
    {
        return $this->state(fn (array $attributes) => [
            'direction' => 'entry',
        ]);
    }

    public function exit(): static
    {
        return $this->state(fn (array $attributes) => [
            'direction' => 'exit',
        ]);
    }

    public function granted(): static
    {
        return $this->state(fn (array $attributes) => [
            'result' => 'granted',
            'denial_reason' => null,
        ]);
    }

    public function denied(): static
    {
        $denialReasons = ['invalid_badge', 'expired_badge', 'blacklisted', 'outside_access_hours', 'unauthorized_zone', 'revoked_access'];
        $reason = fake()->randomElement($denialReasons);

        return $this->state(fn (array $attributes) => [
            'result' => 'denied',
            'denial_reason' => $reason,
        ]);
    }

    public function badgeAccess(): static
    {
        return $this->state(fn (array $attributes) => [
            'metadata' => [
                'method' => 'badge',
                'device_info' => fake()->userAgent(),
            ],
        ]);
    }

    public function qrCodeAccess(): static
    {
        return $this->state(fn (array $attributes) => [
            'metadata' => [
                'method' => 'qr_code',
                'device_info' => fake()->userAgent(),
            ],
        ]);
    }

    public function kioskAccess(): static
    {
        return $this->state(fn (array $attributes) => [
            'metadata' => [
                'method' => 'manual',
                'device_info' => 'Visitor Kiosk',
            ],
        ]);
    }

    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'accessed_at' => now()->subMinutes(fake()->numberBetween(5, 60)),
        ]);
    }

    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'accessed_at' => today()->addHours(fake()->numberBetween(0, 23))->addMinutes(fake()->numberBetween(0, 59)),
        ]);
    }
}