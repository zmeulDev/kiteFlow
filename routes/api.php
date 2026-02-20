<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\TenantController;
use App\Http\Controllers\Api\V1\SubTenantController;
use App\Http\Controllers\Api\V1\BuildingController;
use App\Http\Controllers\Api\V1\MeetingRoomController;
use App\Http\Controllers\Api\V1\VisitorController;
use App\Http\Controllers\Api\V1\VisitController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\AnalyticsController;
use App\Http\Controllers\Api\V1\QrCodeController;

// Public routes
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);
    
    // Kiosk
    Route::get('/kiosk/visits/{code}', [VisitController::class, 'lookup']);
    Route::post('/kiosk/visits/{code}/check-in', [VisitController::class, 'kioskCheckIn']);
});

// Protected routes
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Users
    Route::apiResource('users', UserController::class);

    // Tenants
    Route::apiResource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/logo', [TenantController::class, 'uploadLogo']);

    // Sub-Tenants
    Route::apiResource('sub-tenants', SubTenantController::class);

    // Facilities
    Route::apiResource('buildings', BuildingController::class);
    Route::apiResource('meeting-rooms', MeetingRoomController::class);
    Route::get('meeting-rooms/available', [MeetingRoomController::class, 'available']);

    // Visitors
    Route::apiResource('visitors', VisitorController::class);
    Route::get('visitors/lookup', [VisitorController::class, 'lookup']);
    Route::post('visitors/{visitor}/sign', [VisitorController::class, 'sign']);

    // Visits
    Route::apiResource('visits', VisitController::class);
    Route::get('visits/lookup', [VisitController::class, 'lookup']);
    Route::get('visits/today', [VisitController::class, 'today']);
    Route::post('visits/{visit}/check-in', [VisitController::class, 'checkIn']);
    Route::post('visits/{visit}/check-out', [VisitController::class, 'checkOut']);
    Route::post('visits/{visit}/cancel', [VisitController::class, 'cancel']);

    // Analytics
    Route::prefix('analytics')->group(function () {
        Route::get('quick', [AnalyticsController::class, 'quickStats']);
        Route::get('visits/summary', [AnalyticsController::class, 'visitSummary']);
        Route::get('visits/peak-hours', [AnalyticsController::class, 'peakHours']);
        Route::get('visits/frequency', [AnalyticsController::class, 'visitorFrequency']);
    });

    // QR Codes
    Route::get('visits/{visit}/qr', [QrCodeController::class, 'generate']);
    Route::get('visits/{visit}/qr/image', [QrCodeController::class, 'image']);
});
