<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = ['tenant_id', 'key', 'value'];

    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
}
