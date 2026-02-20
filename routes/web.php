<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\KioskController;
use App\Http\Controllers\Web\AuthController;

Route::get('/kiosk', [KioskController::class, 'index'])->name('kiosk');
Route::get('/kiosk/{tenant:slug}', [KioskController::class, 'show'])->name('kiosk.tenant');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/', fn() => redirect('/kiosk'));
