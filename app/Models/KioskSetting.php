<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KioskSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'entrance_id',
        'welcome_message',
        'logo_path',
        'background_color',
        'primary_color',
        'require_photo',
        'require_signature',
        'show_nda',
        'gdpr_text',
        'nda_text',
    ];

    protected function casts(): array
    {
        return [
            'require_photo' => 'boolean',
            'require_signature' => 'boolean',
            'show_nda' => 'boolean',
        ];
    }

    public function entrance(): BelongsTo
    {
        return $this->belongsTo(Entrance::class);
    }
}