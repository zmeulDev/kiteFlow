<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class Visit extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['visitor_id', 'tenant_id', 'sub_tenant_id', 'host_user_id', 'meeting_room_id', 'building_id', 'visit_code', 'qr_code_path', 'scheduled_start', 'scheduled_end', 'purpose', 'status', 'checked_in_at', 'checked_out_at', 'notes'];

    protected function casts(): array
    {
        return ['scheduled_start' => 'datetime', 'scheduled_end' => 'datetime', 'checked_in_at' => 'datetime', 'checked_out_at' => 'datetime', 'status' => 'string'];
    }

    public function visitor(): BelongsTo { return $this->belongsTo(Visitor::class); }
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function subTenant(): BelongsTo { return $this->belongsTo(SubTenant::class); }
    public function hostUser(): BelongsTo { return $this->belongsTo(User::class, 'host_user_id'); }
    public function meetingRoom(): BelongsTo { return $this->belongsTo(MeetingRoom::class); }
    public function building(): BelongsTo { return $this->belongsTo(Building::class); }
    public function latestCheckIn(): HasOne { return $this->hasOne(CheckIn::class)->latestOfMany(); }
    public function activeCheckIn(): HasOne { return $this->hasOne(CheckIn::class)->whereNull('check_out_time'); }
    public function checkIns(): HasMany { return $this->hasMany(CheckIn::class); }

    public function doCheckIn(User $checkedInBy, ?int $roomId = null, string $method = 'manual'): CheckIn
    {
        $this->update(['status' => 'checked_in', 'checked_in_at' => now()]);
        return CheckIn::create([
            'visit_id' => $this->id,
            'visitor_id' => $this->visitor_id,
            'meeting_room_id' => $roomId ?? $this->meeting_room_id,
            'checked_in_by' => $checkedInBy->id,
            'check_in_time' => now(),
            'check_in_method' => $method,
        ]);
    }

    public function doCheckOut(User $checkedOutBy, string $method = 'manual'): void
    {
        $this->update(['status' => 'checked_out', 'checked_out_at' => now()]);
        $this->activeCheckIn?->update(['checked_out_by' => $checkedOutBy->id, 'check_out_time' => now(), 'check_out_method' => $method]);
    }

    public function cancel(): void { $this->update(['status' => 'cancelled']); }
    public function markNoShow(): void { $this->update(['status' => 'no_show']); }

    public static function generateVisitCode(): string
    {
        do { $code = strtoupper(Str::random(8)); } while (self::where('visit_code', $code)->exists());
        return $code;
    }
}
