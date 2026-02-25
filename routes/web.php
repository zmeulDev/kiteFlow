<?php

use App\Http\Controllers\KioskController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Dashboard redirect for Breeze compatibility
Route::get('/dashboard', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Kiosk routes (no auth required)
Route::prefix('kiosk/{entrance}')->name('kiosk.')->group(function () {
    Route::get('/', [KioskController::class, 'index'])->name('welcome');
    Route::get('/check-in', [KioskController::class, 'checkIn'])->name('checkin');
    Route::get('/check-in-code', [KioskController::class, 'checkInCode'])->name('check-in-code');
    Route::get('/scheduled-check-in/{visit}', [KioskController::class, 'scheduledCheckIn'])->name('scheduled-check-in');
    Route::get('/checkout', [KioskController::class, 'checkOut'])->name('checkout');
});

// Mobile check-in routes (no auth required)
Route::prefix('mobile')->name('mobile.')->group(function () {
    Route::get('/check-in/{qrCode}', [MobileController::class, 'checkIn'])->name('checkin');
});

// Admin routes (auth required)
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('dashboard');

    // Settings
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::livewire('/business', \App\Livewire\Admin\Settings\BusinessDetails::class)->name('business');
        Route::livewire('/gdpr', \App\Livewire\Admin\Settings\GdprSettings::class)->name('gdpr');
        Route::livewire('/nda', \App\Livewire\Admin\Settings\NdaSettings::class)->name('nda');
        Route::livewire('/retention', \App\Livewire\Admin\Settings\DataRetention::class)->name('retention');
        Route::livewire('/rbac', \App\Livewire\Admin\Settings\RolePermissions::class)->name('rbac');
    });

    // Users management
    Route::livewire('/users', \App\Livewire\Admin\Users\UserList::class)->name('users');

    // Companies management
    Route::livewire('/companies', \App\Livewire\Admin\Companies\CompanyList::class)->name('companies');
    Route::livewire('/companies/{company}/edit', \App\Livewire\Admin\Companies\CompanyEdit::class)->name('companies.edit');

    // Buildings management
    Route::livewire('/buildings', \App\Livewire\Admin\Buildings\BuildingList::class)->name('buildings');

    // Visits
    Route::livewire('/visits', \App\Livewire\Admin\Visits\VisitList::class)->name('visits');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';