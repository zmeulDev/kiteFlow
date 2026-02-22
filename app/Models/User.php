<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'timezone',
        'locale',
        'avatar',
        'phone',
        'department',
        'job_title',
        'is_active',
        'last_login_at',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'preferences' => 'array',
        'is_active' => 'boolean',
    ];

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class)
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    public function ownedTenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class)
            ->wherePivot('is_owner', true)
            ->withTimestamps();
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class, 'host_id');
    }

    public function hostedMeetings(): HasMany
    {
        return $this->meetings()->where('status', 'scheduled');
    }

    public function visitorVisitsAsHost(): HasMany
    {
        return $this->hasMany(VisitorVisit::class, 'host_id');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(AccessLog::class, 'subject_id')
            ->where('subject_type', self::class);
    }

    public function parkingRecordsCheckedIn(): HasMany
    {
        return $this->hasMany(ParkingRecord::class, 'checked_in_by');
    }

    public function parkingRecordsCheckedOut(): HasMany
    {
        return $this->hasMany(ParkingRecord::class, 'checked_out_by');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(['super-admin', 'admin']);
    }

    public function isTenantAdmin(?int $tenantId = null): bool
    {
        if ($tenantId) {
            return $this->tenants()
                ->where('tenants.id', $tenantId)
                ->wherePivot('is_owner', true)
                ->exists();
        }
        return $this->tenants()->wherePivot('is_owner', true)->exists();
    }

    public function belongsToTenant(int $tenantId): bool
    {
        return $this->tenants()->where('tenants.id', $tenantId)->exists();
    }

    public function getCurrentTenant(): ?Tenant
    {
        // First try to get tenant from request (tenant context set by middleware)
        $requestTenant = request()->attributes->get('tenant');
        if ($requestTenant) {
            // Verify user has access to this tenant
            if ($this->belongsToOneOfTenants([$requestTenant->id])) {
                return $requestTenant;
            }
        }

        // Fall back to getting the tenant where user is owner
        return $this->tenants()
            ->wherePivot('is_owner', true)
            ->first();
    }

    public function belongsToOneOfTenants(array $tenantIds): bool
    {
        return $this->tenants()
            ->whereIn('tenants.id', $tenantIds)
            ->exists();
    }

    public function getPreference(string $key, mixed $default = null): mixed
    {
        return data_get($this->preferences, $key, $default);
    }

    public function setPreference(string $key, mixed $value): void
    {
        $preferences = $this->preferences ?? [];
        data_set($preferences, $key, $value);
        $this->preferences = $preferences;
        $this->save();
    }

    public function recordLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    public function activate(): void
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false]);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTenant($query, int $tenantId)
    {
        return $query->whereHas('tenants', fn ($q) => $q->where('tenant_id', $tenantId));
    }

    public function scopeSuperAdmins($query)
    {
        return $query->role('super-admin');
    }

    public function scopeAdmins($query)
    {
        return $query->role(['super-admin', 'admin']);
    }
}