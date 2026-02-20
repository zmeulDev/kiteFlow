<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
});

Volt::route('/kiosk/{tenant}', 'kiosk-mode');
Volt::route('/admin/dashboard', 'tenant-admin-dashboard');
Volt::route('/admin/visitors', 'visitor-profiles');
Volt::route('/super-admin/dashboard', 'super-admin-dashboard');
Volt::route('/super-admin/tenants/{tenant}', 'super-admin-tenant-detail')->name('super-admin.tenant.detail');

Route::get('/tenants/{tenant}/billing', [\App\Http\Controllers\SubscriptionController::class, 'billingPortal'])
     ->name('tenant.billing');
