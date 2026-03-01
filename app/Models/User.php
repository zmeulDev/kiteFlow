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

    public function canManageAllTenants(): bool
    {
        return $this->role === 'admin';
    }

    public function getManagedCompanyIds(): array
    {
        if ($this->canManageAllTenants()) {
            return Company::pluck('id')->toArray();
        }

        if (!$this->company_id) {
            return [];
        }

        if (in_array($this->role, ['administrator', 'receptionist'])) {
            return array_merge([$this->company_id], $this->getAllChildCompanyIds((int)$this->company_id));
        }

        return [$this->company_id];
    }

    protected function getAllChildCompanyIds(int $parentId): array
    {
        $childIds = Company::where('parent_id', $parentId)->pluck('id')->toArray();
        $allIds = $childIds;

        foreach ($childIds as $childId) {
            $allIds = array_merge($allIds, $this->getAllChildCompanyIds($childId));
        }

        return $allIds;
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

    public function getAssignableRoles(): array
    {
        $allRoles = static::getRoles();

        if ($this->isAdmin()) {
            return $allRoles;
        }

        if ($this->isAdministrator()) {
            unset($allRoles['admin']);
            return $allRoles;
        }

        if ($this->isTenant()) {
            return [
                'tenant' => $allRoles['tenant'],
                'viewer' => $allRoles['viewer'],
            ];
        }

        return ['viewer' => $allRoles['viewer']];
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