<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingRoom extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['building_id', 'tenant_id', 'name', 'capacity', 'amenities', 'floor', 'floor_plan_path', 'is_active'];

    protected function casts(): array
    {
        return ['amenities' => 'array', 'is_active' => 'boolean', 'capacity' => 'integer'];
    }

    public function building(): BelongsTo { return $this->belongsTo(Building::class); }
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function visits(): HasMany { return $this->hasMany(Visit::class); }
    public function checkIns(): HasMany { return $this->hasMany(CheckIn::class); }
}
