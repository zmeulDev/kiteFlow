<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MeetingRoomController;
use App\Http\Controllers\Api\V1\VisitController;
use App\Http\Controllers\Api\V1\LocationController;
use App\Http\Controllers\Api\V1\VisitorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function() {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function() {
        Route::middleware('subscribed')->group(function() {
            Route::apiResource('tenants.locations', LocationController::class);
            Route::apiResource('tenants.rooms', MeetingRoomController::class);
            Route::get('/tenants/{tenant}/visitors', [VisitorController::class, 'index']);
            Route::get('/tenants/{tenant}/visitors/{visitor}', [VisitorController::class, 'show']);
            
            Route::post('/tenants/{tenant}/visits', [VisitController::class, 'store']);
            Route::get('/tenants/{tenant}/visits/{code}', [VisitController::class, 'showByCode']);
            Route::post('/tenants/{tenant}/visits/{visit}/check-in', [VisitController::class, 'checkIn']);
            Route::post('/tenants/{tenant}/visits/{visit}/check-out', [VisitController::class, 'checkOut']);
        });
    });
});
