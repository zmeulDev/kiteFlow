<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantContact extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'phone',
        'is_main',
    ];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
