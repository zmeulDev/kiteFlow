<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Services\VisitService;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function __construct(
        protected VisitService $visitService
    ) {}

    public function checkIn(Request $request, string $qrCode)
    {
        $visit = $this->visitService->findByQrCode($qrCode);

        if (!$visit) {
            abort(404, 'Invalid or expired QR code');
        }

        return view('mobile-checkin', compact('visit'));
    }
}