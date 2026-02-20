<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

class Visitor extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['tenant_id', 'first_name', 'last_name', 'email', 'phone', 'company', 'signature_path', 'signature_signed_at', 'agreed_to_nda', 'agreed_to_terms', 'last_visit_at'];

    protected function casts(): array
    {
        return [
            'agreed_to_nda' => 'boolean',
            'agreed_to_terms' => 'boolean',
            'signature_signed_at' => 'datetime',
            'last_visit_at' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function visits(): HasMany { return $this->hasMany(Visit::class); }
    public function checkIns(): HasMany { return $this->hasMany(CheckIn::class); }
    public function getFullNameAttribute(): string { return "{$this->first_name} {$this->last_name}"; }
}
