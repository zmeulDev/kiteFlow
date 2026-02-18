<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        return view('superadmin.dashboard');
    }

    public function tenants()
    {
        return view('superadmin.tenants');
    }

    public function users()
    {
        return view('superadmin.users');
    }

    public function roles()
    {
        return view('superadmin.roles');
    }
}
