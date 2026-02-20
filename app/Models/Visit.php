<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $fillable = [
        'tenant_id',
        'visitor_id',
        'meeting_room_id',
        'host_user_id',
        'scheduled_at',
        'check_in_time',
        'check_out_time',
        'purpose',
        'status',
        'nda_signature',
        'invite_code',
        'qr_code_path',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'check_in_time' => 'datetime',
            'check_out_time' => 'datetime',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function meetingRoom(): BelongsTo
    {
        return $this->belongsTo(MeetingRoom::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }
}
