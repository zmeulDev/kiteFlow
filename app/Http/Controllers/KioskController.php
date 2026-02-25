<?php

namespace App\Http\Controllers;

use App\Models\Entrance;
use App\Models\Visit;
use Illuminate\Http\Request;

class KioskController extends Controller
{
    public function index(Request $request, string $entranceIdentifier)
    {
        $entrance = Entrance::where('kiosk_identifier', $entranceIdentifier)
            ->where('is_active', true)
            ->with(['building', 'kioskSetting'])
            ->firstOrFail();

        return view('kiosk', compact('entrance'));
    }

    public function checkIn(Request $request, string $entranceIdentifier)
    {
        $entrance = Entrance::where('kiosk_identifier', $entranceIdentifier)
            ->where('is_active', true)
            ->with(['building', 'kioskSetting'])
            ->firstOrFail();

        return view('kiosk-checkin', compact('entrance'));
    }

    public function checkOut(Request $request, string $entranceIdentifier)
    {
        $entrance = Entrance::where('kiosk_identifier', $entranceIdentifier)
            ->where('is_active', true)
            ->with(['building', 'kioskSetting'])
            ->firstOrFail();

        return view('kiosk-checkout', compact('entrance'));
    }

    public function checkInCode(Request $request, string $entranceIdentifier)
    {
        $entrance = Entrance::where('kiosk_identifier', $entranceIdentifier)
            ->where('is_active', true)
            ->with(['building', 'kioskSetting'])
            ->firstOrFail();

        return view('kiosk-check-in-code', compact('entrance'));
    }

    public function scheduledCheckIn(Request $request, string $entranceIdentifier, Visit $visit)
    {
        $entrance = Entrance::where('kiosk_identifier', $entranceIdentifier)
            ->where('is_active', true)
            ->with(['building', 'kioskSetting'])
            ->firstOrFail();

        return view('kiosk-scheduled-check-in', compact('entrance', 'visit'));
    }
}