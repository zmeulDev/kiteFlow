<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    /**
     * Generate QR code for a visit
     */
    public function generate(Visit $visit): JsonResponse
    {
        $data = json_encode([
            'code' => $visit->visit_code,
            'tenant_id' => $visit->tenant_id,
        ]);

        $qr = base64_encode(QrCode::format('svg')->size(300)->generate($data));

        return response()->json([
            'visit_code' => $visit->visit_code,
            'qr_code' => $qr,
            'qr_data' => $data,
        ]);
    }

    /**
     * Generate QR code as image (inline)
     */
    public function image(Visit $visit)
    {
        $data = json_encode([
            'code' => $visit->visit_code,
            'tenant_id' => $visit->tenant_id,
        ]);

        return response(QrCode::format('png')->size(300)->generate($data))
            ->header('Content-Type', 'image/png');
    }
}
