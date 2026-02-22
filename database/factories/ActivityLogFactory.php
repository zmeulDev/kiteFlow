<?php

namespace Database\Factories;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        $logTypes = ['user.login', 'user.logout', 'visitor.check_in', 'visitor.check_out', 'meeting.created', 'meeting.cancelled', 'tenant.updated'];

        return [
            'uuid' => Str::uuid(),
            'tenant_id' => null, // Will be set by forTenant
            'user_id' => null,   // Will be set by forUser
            'action' => fake()->randomElement($logTypes),
            'description' => fake()->sentence(),
            'subject_type' => null,
            'subject_id' => null,
            'old_values' => [],
            'new_values' => [],
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function forTenant($tenant): static
    {
        return $this->state(fn (array $attributes) => [
            'tenant_id' => $tenant instanceof \App\Models\Tenant ? $tenant->id : $tenant,
        ]);
    }

    public function forUser($user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user instanceof \App\Models\User ? $user->id : $user,
            'tenant_id' => $user instanceof \App\Models\User ? ($user->tenants()->first()->id ?? null) : null,
        ]);
    }
}