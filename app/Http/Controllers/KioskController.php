<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KioskController extends Controller
{
    public function checkInFastPass($token)
    {
        return view('kiosk-fast-pass', ['token' => $token]);
    }
}
