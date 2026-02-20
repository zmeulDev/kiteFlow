<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class KioskController extends Controller
{
    public function show(Tenant $tenant)
    {
        return view('kiosk', ['tenant' => $tenant]);
    }

    public function index()
    {
        $tenant = Tenant::where('is_active', true)->first();
        return view('kiosk', ['tenant' => $tenant]);
    }
}
