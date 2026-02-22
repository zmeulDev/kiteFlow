<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Dashboard;
use App\Livewire\Kiosk\KioskMode;
use App\Livewire\Visitors\VisitorList;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Test route for Livewire debugging
Route::get('/test-livewire', function () {
    return view('test-livewire');
})->name('test-livewire');

// Landing page
Route::get('/', fn () => view('welcome'))->name('home');

// Kiosk mode (public, requires tenant and access point)
Route::get('/kiosk/{tenantSlug}/{accessPointUuid}', KioskMode::class)->name('kiosk');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
    
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Visitors
    Route::get('/visitors', VisitorList::class)->name('visitors.index');
    
    // Meetings
    Route::prefix('meetings')->name('meetings.')->group(function () {
        Route::get('/', \App\Livewire\Meetings\MeetingList::class)->name('index');
        Route::get('/create', \App\Livewire\Meetings\MeetingForm::class)->name('create');
        Route::get('/{meeting}', \App\Livewire\Meetings\MeetingDetail::class)->name('show');
        Route::get('/{meeting}/edit', \App\Livewire\Meetings\MeetingForm::class)->name('edit');
    });
    
    // Meeting Rooms
    Route::get('/meeting-rooms', \App\Livewire\MeetingRooms\MeetingRoomList::class)->name('meeting-rooms.index');
    
    // Buildings & Facilities
    Route::get('/facilities', \App\Livewire\Facilities\BuildingList::class)->name('facilities.index');
    
    // Access Points
    Route::get('/access-points', \App\Livewire\AccessPoints\AccessPointList::class)->name('access-points.index');
    
    // Reports
    Route::get('/reports', \App\Livewire\Reports\ReportDashboard::class)->name('reports.index');
    
    // Settings (admin only)
    Route::middleware('role:admin|super-admin')->group(function () {
        Route::get('/settings', \App\Livewire\Settings\SettingsPage::class)->name('settings.index');
        Route::get('/settings/users', \App\Livewire\Settings\UserManagement::class)->name('settings.users');
        Route::get('/settings/subtenants', \App\Livewire\Settings\SubtenantManagement::class)->name('settings.subtenants');
        Route::get('/settings/subtenants/{subtenantId}', \App\Livewire\Settings\SubtenantDetail::class)->name('settings.subtenants.show');
        Route::get('/settings/tenants', \App\Livewire\Settings\TenantManagement::class)->name('settings.tenants');
        Route::get('/settings/tenants/{tenant}', \App\Livewire\Settings\TenantDetail::class)->name('settings.tenants.show');
        Route::get('/settings/system-logs', \App\Livewire\Settings\SystemLogs::class)->name('settings.system-logs');
        Route::get('/settings/kiosk', \App\Livewire\Settings\KioskSettings::class)->name('settings.kiosk');
        Route::get('/settings/system', \App\Livewire\Settings\SystemSettings::class)->name('settings.system');
        Route::get('/settings/integrations', \App\Livewire\Settings\Integrations::class)->name('settings.integrations');
        Route::get('/settings/billing', \App\Livewire\Settings\Billing::class)->name('settings.billing');
        Route::get('/settings/support', \App\Livewire\Settings\Support::class)->name('settings.support');
    });
});