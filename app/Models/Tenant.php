<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Tenant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'parent_id',
        'name',
        'slug',
        'domain',
        'email',
        'phone',
        'logo',
        'timezone',
        'locale',
        'currency',
        'settings',
        'address',
        'status',
        'trial_ends_at',
        'subscription_ends_at',
        // Contract details
        'subscription_plan',
        'billing_cycle',
        'monthly_price',
        'yearly_price',
        'contract_start_date',
        'contract_end_date',
        'payment_status',
        'notes',
    ];

    protected $casts = [
        'settings' => 'array',
        'address' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'monthly_price' => 'decimal:2',
        'yearly_price' => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function (self $tenant): void {
            if (empty($tenant->uuid)) {
                $tenant->uuid = Str::uuid();
            }
            if (empty($tenant->slug)) {
                $tenant->slug = Str::slug($tenant->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Tenant::class, 'parent_id');
    }

    /**
     * Get all descendants (recursive)
     * Note: This is a simple recursive relationship.
     * For large hierarchies, consider using a closure table or nested set pattern.
     */
    public function descendants(): HasMany
    {
        return $this->hasMany(Tenant::class, 'parent_id')->with('descendants');
    }

    /**
     * Get all descendants as a flat array
     */
    public function getAllDescendants(): \Illuminate\Support\Collection
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }

        return $descendants;
    }

    /**
     * Get all ancestors (parent + grandparents, etc.)
     */
    public function getAncestors(): \Illuminate\Support\Collection
    {
        $ancestors = collect();
        $parent = $this->parent;

        while ($parent) {
            $ancestors->push($parent);
            $parent = $parent->parent;
        }

        return $ancestors;
    }

    /**
     * Check if this tenant is a descendant of another tenant
     */
    public function isDescendantOf(int $tenantId): bool
    {
        $parent = $this->parent;

        while ($parent) {
            if ($parent->id === $tenantId) {
                return true;
            }
            $parent = $parent->parent;
        }

        return false;
    }

    /**
     * Check if this tenant can access another tenant's data
     * (can access own data and all descendants' data)
     */
    public function canAccessTenant(int $tenantId): bool
    {
        // Can access own data
        if ($this->id === $tenantId) {
            return true;
        }

        // Can access descendants' data
        return \App\Models\Tenant::where('parent_id', $this->id)
            ->where('id', $tenantId)
            ->exists();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('is_owner')
            ->withTimestamps();
    }

    public function owners(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('is_owner', true)
            ->withTimestamps();
    }

    public function settings(): HasMany
    {
        return $this->hasMany(TenantSetting::class);
    }

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function meetingRooms(): HasMany
    {
        return $this->hasMany(MeetingRoom::class);
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(Visitor::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(Meeting::class);
    }

    public function accessPoints(): HasMany
    {
        return $this->hasMany(AccessPoint::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isOnTrial(): bool
    {
        return $this->status === 'trial' && 
            ($this->trial_ends_at === null || $this->trial_ends_at->isFuture());
    }

    public function hasValidSubscription(): bool
    {
        return $this->isActive() || 
            ($this->subscription_ends_at !== null && $this->subscription_ends_at->isFuture());
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function setSetting(string $key, mixed $value): void
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get all accessible tenant IDs for a given tenant (self + all descendants)
     */
    public static function getAccessibleTenantIds(int $tenantId): array
    {
        $tenant = self::with('children')->find($tenantId);

        if (!$tenant) {
            return [$tenantId];
        }

        $ids = [$tenant->id];

        // Get all descendant IDs recursively
        $queue = [$tenant->id];
        while (!empty($queue)) {
            $currentId = array_shift($queue);
            $children = self::where('parent_id', $currentId)->get(['id']);

            foreach ($children as $child) {
                if (!in_array($child->id, $ids)) {
                    $ids[] = $child->id;
                    $queue[] = $child->id;
                }
            }
        }

        return $ids;
    }

    public function scopeChildren($query)
    {
        return $query->whereNotNull('parent_id');
    }
}