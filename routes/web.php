<?php

use App\Livewire\Kiosk\FastPass;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Visit;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/', function () {
        return view('superadmin.dashboard');
    })->name('superadmin.dashboard');

    Route::get('/tenants', function () {
        return view('superadmin.tenants');
    })->name('superadmin.tenants');

    Route::get('/tenants/{id}', \App\Livewire\Superadmin\TenantShow::class)->name('superadmin.tenants.show');
});

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (auth()->attempt($credentials)) {
        $request->session()->regenerate();
        session()->put('tenant_id', auth()->user()->tenant_id);
        return redirect()->intended('dashboard');
    }

    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
})->name('login.post');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/calendar', function () {
        return view('calendar');
    })->name('calendar');

    Route::get('/rooms', function () {
        return view('rooms');
    })->name('rooms');

    Route::get('/sub-tenants', function () {
        if (!auth()->user()->tenant->is_hub) return redirect()->route('dashboard');
        return view('sub-tenants');
    })->name('sub-tenants');

    Route::get('/settings', function () {
        return view('settings');
    })->name('settings');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::get('/notifications', \App\Livewire\Dashboard\NotificationHistory::class)->name('notifications');

    Route::post('/logout', function (\Illuminate\Http\Request $request) {
        if (session()->has('impersonator_id')) {
            $superAdmin = \App\Models\User::find(session('impersonator_id'));
            if ($superAdmin) {
                auth()->login($superAdmin);
                session()->forget(['impersonator_id', 'tenant_id']);
                return redirect()->route('superadmin.dashboard');
            }
        }

        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

Route::get('/kiosk/{tenant:slug}', \App\Livewire\Kiosk\KioskMain::class)->name('kiosk');

Route::get('/check-in/{token}', function ($token) {
    return view('kiosk-fast-pass', ['token' => $token]);
})->name('check-in.fast-pass');
