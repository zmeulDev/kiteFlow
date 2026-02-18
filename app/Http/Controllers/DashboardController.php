<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function calendar()
    {
        return view('calendar');
    }

    public function rooms()
    {
        return view('rooms');
    }

    public function settings()
    {
        return view('settings');
    }

    public function profile()
    {
        return view('profile');
    }

    public function subTenants()
    {
        if (!auth()->user()->tenant->is_hub) {
            return redirect()->route('dashboard');
        }
        return view('sub-tenants');
    }
}
