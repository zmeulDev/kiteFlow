<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens, SoftDeletes;

    protected $fillable = ['tenant_id', 'sub_tenant_id', 'name', 'email', 'password', 'role', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed', 'is_active' => 'boolean'];
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function subTenant(): BelongsTo { return $this->belongsTo(SubTenant::class, 'sub_tenant_id'); }
    public function hostedVisits(): HasMany { return $this->hasMany(Visit::class, 'host_user_id'); }
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isTenantAdmin(): bool { return in_array($this->role, ['admin', 'tenant_admin']); }
}
