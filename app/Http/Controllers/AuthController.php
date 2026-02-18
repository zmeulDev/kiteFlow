<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            session()->put('tenant_id', Auth::user()->tenant_id);
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        if (session()->has('impersonator_id')) {
            $superAdmin = User::find(session('impersonator_id'));
            if ($superAdmin) {
                Auth::login($superAdmin);
                session()->forget(['impersonator_id', 'tenant_id']);
                return redirect()->route('superadmin.dashboard');
            }
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function registerForm()
    {
        return view('register');
    }
}
