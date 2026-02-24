<?php

namespace App\Services;

use App\Models\Visit;
use Illuminate\Support\Str;

class QrCodeService
{
    public function generateQrCode(): string
    {
        return Str::random(32);
    }

    public function generateQrCodeUrl(Visit $visit): string
    {
        return url("/mobile/check-in/{$visit->qr_code}");
    }

    public function validateQrCode(string $qrCode): ?Visit
    {
        return Visit::where('qr_code', $qrCode)
            ->where('status', 'pending')
            ->first();
    }
}