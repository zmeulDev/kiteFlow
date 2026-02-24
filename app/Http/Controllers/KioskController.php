<?php

namespace App\Http\Controllers;

use App\Models\Entrance;
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
}