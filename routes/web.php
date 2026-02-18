<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\KioskController;
use App\Livewire\Superadmin\TenantShow;
use App\Livewire\Dashboard\NotificationHistory;
use App\Livewire\Kiosk\KioskMain;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

// Superadmin Routes
Route::middleware(['auth', 'role:super-admin'])->prefix('superadmin')->group(function () {
    Route::get('/', [SuperAdminController::class, 'dashboard'])->name('superadmin.dashboard');
    Route::get('/tenants', [SuperAdminController::class, 'tenants'])->name('superadmin.tenants');
    Route::get('/tenants/{id}', TenantShow::class)->name('superadmin.tenants.show');
    Route::get('/users', [SuperAdminController::class, 'users'])->name('superadmin.users');
    Route::get('/roles', [SuperAdminController::class, 'roles'])->name('superadmin.roles');
});

// Dashboard & Tenant Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/calendar', [DashboardController::class, 'calendar'])->name('calendar');
    Route::get('/rooms', [DashboardController::class, 'rooms'])->name('rooms');
    
    // Only admins or receptionists can manage sub-tenants
    Route::middleware(['role:admin|receptionist'])->group(function () {
        Route::get('/sub-tenants', [DashboardController::class, 'subTenants'])->name('sub-tenants');
    });

    Route::get('/settings', [DashboardController::class, 'settings'])->name('settings');
    Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    
    Route::get('/notifications', NotificationHistory::class)->name('notifications');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Kiosk Routes
Route::get('/kiosk/{tenant:slug}', KioskMain::class)->name('kiosk');
Route::get('/check-in/{token}', [KioskController::class, 'checkInFastPass'])->name('check-in.fast-pass');
