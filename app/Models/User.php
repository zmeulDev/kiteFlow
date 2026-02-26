<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
        'company_id',
        'notes',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isAdministrator(): bool
    {
        return $this->role === 'administrator';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    public static function getRoles(): array
    {
        return [
            'admin' => 'System Administrator',
            'administrator' => 'Company Administrator',
            'tenant' => 'Location Administrator',
            'receptionist' => 'Receptionist',
            'viewer' => 'Employee'
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function hasPermission(string $permission): bool
    {
        // Admin gets everything by default unless explicitly disabled, or we just trust the settings.
        // Let's resolve what's configured in settings.
        $permissionsMatrix = Setting::get('rbac_permissions', []);

        // If no settings exist yet, default to admin having it all.
        if (empty($permissionsMatrix) && $this->isAdmin()) {
            return true;
        }

        // Get the permissions for this user's role.
        $rolePermissions = $permissionsMatrix[$this->role] ?? [];

        return in_array($permission, $rolePermissions);
    }

    public function hostedVisits(): HasMany
    {
        return $this->hasMany(Visit::class, 'host_id');
    }
}