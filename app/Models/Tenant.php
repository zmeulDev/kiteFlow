<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\Billable;

class Tenant extends Model
{
    use Billable;

    protected $fillable = [
        'parent_id',
        'name', 
        'is_hub',
        'contact_name', 
        'contact_email', 
        'contact_phone',
        'billing_address',
        'vat_id',
        'contract_notes',
        'monthly_rate',
        'slug', 
        'settings', 
        'logo_path', 
        'plan', 
        'status', 
        'trial_ends_at', 
        'subscription_ends_at'
    ];

    protected $casts = [
        'is_hub' => 'boolean',
        'settings' => 'array',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'monthly_rate' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($tenant) {
            // Cascade delete related records via Eloquent to trigger child events
            $tenant->users->each->delete();
            $tenant->locations->each->delete();
            $tenant->visitors->each->delete();
            
            // Note: meetingRooms, visits, and bookings are handled by cascading 
            // deletes in Location and Visitor models.
        });
    }

    /**
     * Check if the tenant has reached their visitor limit for the current month.
     */
    public function hasReachedLimit(): bool
    {
        if ($this->plan === 'pro' || $this->plan === 'enterprise') {
            return false;
        }

        // Default 'free' limit: 50 visitors
        $limit = 50;
        $count = $this->visits()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return $count >= $limit;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Tenant::class, 'parent_id');
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(Visitor::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(Location::class);
    }

    public function meetingRooms(): HasMany
    {
        return $this->hasMany(MeetingRoom::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=4f46e5&background=f8faff';
    }
}

