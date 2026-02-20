<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;

class CheckIn extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['visit_id', 'visitor_id', 'meeting_room_id', 'checked_in_by', 'checked_out_by', 'check_in_time', 'check_out_time', 'check_in_method', 'check_out_method', 'notes'];

    protected function casts(): array
    {
        return ['check_in_time' => 'datetime', 'check_out_time' => 'datetime'];
    }

    public function visit(): BelongsTo { return $this->belongsTo(Visit::class); }
    public function visitor(): BelongsTo { return $this->belongsTo(Visitor::class); }
    public function meetingRoom(): BelongsTo { return $this->belongsTo(MeetingRoom::class); }
    public function checkedInBy(): BelongsTo { return $this->belongsTo(User::class, 'checked_in_by'); }
    public function checkedOutBy(): BelongsTo { return $this->belongsTo(User::class, 'checked_out_by'); }
    public function isActive(): bool { return is_null($this->check_out_time); }
}
