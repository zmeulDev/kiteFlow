<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\KioskController;
use App\Http\Controllers\Api\MeetingController;
use App\Http\Controllers\Api\MeetingRoomController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VisitorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health check
Route::get('/health', fn () => response()->json(['status' => 'ok', 'timestamp' => now()->toISOString()]));

// Authentication routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('/user', [AuthController::class, 'user']);
        Route::put('/user', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'updatePassword']);
    });
});

// Kiosk routes (public, requires tenant and access point identification)
Route::prefix('kiosk/{tenant:slug}/{accessPoint:code}')->group(function () {
    Route::post('/check-in', [KioskController::class, 'checkIn']);
    Route::post('/check-out', [KioskController::class, 'checkOut']);
    Route::get('/lookup', [KioskController::class, 'lookup']);
    Route::get('/pre-registered', [KioskController::class, 'preRegisteredVisitors']);
    Route::get('/active-visitors', [KioskController::class, 'activeVisitors']);
    Route::get('/hosts', [KioskController::class, 'hostDirectory']);
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {

    // Super Admin routes (outside tenant middleware)
    Route::middleware('role:super-admin')->prefix('admin')->group(function () {
        Route::apiResource('tenants', TenantController::class);
        Route::post('tenants/{tenant}/activate', [TenantController::class, 'activate']);
        Route::post('tenants/{tenant}/suspend', [TenantController::class, 'suspend']);
        Route::put('tenants/{tenant}/status', [TenantController::class, 'updateStatus']);
        Route::post('tenants/{tenant}/users', [TenantController::class, 'addUser']);
        Route::post('tenants/{tenant}/billing', [TenantController::class, 'updateBilling']);
        Route::apiResource('users', UserController::class);

        // Analytics
        Route::get('analytics/visitors', [TenantController::class, 'visitorAnalytics']);

        // Reports
        Route::get('reports/tenant-performance', [TenantController::class, 'tenantPerformanceReport']);

        // Activity logs
        Route::get('activity-logs', [TenantController::class, 'activityLogs']);

        // System settings
        Route::put('settings/system', [TenantController::class, 'updateSystemSettings']);

        // Notification templates
        Route::post('notification-templates', [TenantController::class, 'createNotificationTemplate']);
    });

    // User profile (no tenant context required)
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'profile']);
        Route::put('/', [UserController::class, 'updateProfile']);
    });

    Route::put('/password', [UserController::class, 'updatePassword']);

    // User-specific routes (for current logged-in user)
    Route::prefix('me')->group(function () {
        Route::get('/my-meetings', [\App\Http\Controllers\Api\MeController::class, 'myMeetings']);
        Route::get('/my-visitors', [\App\Http\Controllers\Api\MeController::class, 'myVisitors']);
        Route::get('/analytics', [\App\Http\Controllers\Api\MeController::class, 'analytics']);
        Route::put('/preferences/notifications', [\App\Http\Controllers\Api\MeController::class, 'updateNotificationPreferences']);
        Route::get('/profile', [\App\Http\Controllers\Api\MeController::class, 'profile']);
        Route::put('/profile', [\App\Http\Controllers\Api\MeController::class, 'updateProfile']);
    });

    // Tenant-scoped routes (requires tenant context)
    Route::middleware('tenant')->group(function () {

        // Tenant Settings routes
        Route::prefix('tenants/{tenant:slug}')->group(function () {
            // Tenant Profile & Settings
            Route::get('/', [TenantController::class, 'show']);
            Route::put('/', [TenantController::class, 'update']);
            Route::put('/profile', [TenantController::class, 'updateProfile']);
            Route::put('/business-settings', [TenantController::class, 'updateBusinessSettings']);
            Route::get('/business-settings', [TenantController::class, 'getBusinessSettings']);
            Route::post('/logo', [TenantController::class, 'uploadLogo']);
            Route::delete('/logo', [TenantController::class, 'deleteLogo']);

            // GDPR Settings
            Route::get('/gdpr-settings', [TenantController::class, 'getGdprSettings']);
            Route::put('/gdpr-settings', [TenantController::class, 'updateGdprSettings']);

            // NDA Settings
            Route::get('/nda-settings', [TenantController::class, 'getNdaSettings']);
            Route::put('/nda-settings', [TenantController::class, 'updateNdaSettings']);

            // Data Retention Settings
            Route::get('/data-retention-settings', [TenantController::class, 'getDataRetentionSettings']);
            Route::put('/data-retention-settings', [TenantController::class, 'updateDataRetentionSettings']);

            // Tenant management
            Route::get('/users', [TenantController::class, 'users']);
            Route::post('/users', [TenantController::class, 'createUser']);
            Route::delete('/users/{userId}', [TenantController::class, 'removeUser']);
            Route::get('/sub-tenants', [TenantController::class, 'subTenants']);
            Route::post('/sub-tenants', [TenantController::class, 'createSubTenant']);
            Route::put('/sub-tenants/{id}', [TenantController::class, 'updateSubTenant']);
            Route::delete('/sub-tenants/{id}', [TenantController::class, 'deleteSubTenant']);

            // Individual Tenant Settings (CRUD)
            Route::prefix('settings')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\TenantSettingController::class, 'index']);
                Route::post('/', [\App\Http\Controllers\Api\TenantSettingController::class, 'store']);
                Route::get('/grouped', [\App\Http\Controllers\Api\TenantSettingController::class, 'getGroupedSettings']);
                Route::post('/batch', [\App\Http\Controllers\Api\TenantSettingController::class, 'batchUpdate']);
                Route::get('/{key}', [\App\Http\Controllers\Api\TenantSettingController::class, 'show']);
                Route::put('/{key}', [\App\Http\Controllers\Api\TenantSettingController::class, 'update']);
                Route::delete('/{key}', [\App\Http\Controllers\Api\TenantSettingController::class, 'destroy']);
            });

            // Notification preferences (using a different path to avoid form request conflict)
            Route::put('/notifications-preferences', [TenantController::class, 'updateNotificationPreferences']);

            // Analytics
            Route::get('/analytics', [TenantController::class, 'tenantAnalytics']);

            // Kiosk settings
            Route::put('/kiosks/{accessPoint}', [TenantController::class, 'updateKioskSettings']);

            // Visitors
            Route::prefix('visitors')->group(function () {
                Route::get('/', [VisitorController::class, 'index']);
                Route::post('/', [VisitorController::class, 'store']);
                Route::get('/current', [VisitorController::class, 'currentVisitors']);
                Route::post('/check-in', [VisitorController::class, 'checkIn']);
                Route::post('/check-out/{visit}', [VisitorController::class, 'checkOut']);
                Route::get('/{visitor}', [VisitorController::class, 'show']);
                Route::put('/{visitor}', [VisitorController::class, 'update']);
                Route::delete('/{visitor}', [VisitorController::class, 'destroy']);
                Route::post('/{visitor}/blacklist', [VisitorController::class, 'blacklist']);
                Route::post('/{visitor}/unblacklist', [VisitorController::class, 'unblacklist']);
                Route::get('/{visitor}/visits', [VisitorController::class, 'visits']);
            });

            // Meetings
            Route::prefix('meetings')->group(function () {
                Route::get('/', [MeetingController::class, 'index']);
                Route::post('/', [MeetingController::class, 'store']);
                Route::get('/today', [MeetingController::class, 'today']);
                Route::get('/upcoming', [MeetingController::class, 'upcoming']);
                Route::get('/{meeting}', [MeetingController::class, 'show']);
                Route::put('/{meeting}', [MeetingController::class, 'update']);
                Route::delete('/{meeting}', [MeetingController::class, 'destroy']);
                Route::post('/{meeting}/cancel', [MeetingController::class, 'cancel']);
                Route::get('/{meeting}/attendees', [MeetingController::class, 'attendees']);
                Route::post('/{meeting}/attendees', [MeetingController::class, 'addAttendee']);
                Route::delete('/{meeting}/attendees/{attendeeId}', [MeetingController::class, 'removeAttendee']);
                Route::post('/{meeting}/invite-visitor', [MeetingController::class, 'inviteVisitor']);
            });

            // Meeting Rooms
            Route::apiResource('meeting-rooms', MeetingRoomController::class);
            Route::get('meeting-rooms/{meetingRoom}/availability', [MeetingRoomController::class, 'availability']);

            // Buildings & Facilities
            Route::apiResource('buildings', BuildingController::class);
            Route::prefix('buildings/{building}')->group(function () {
                Route::apiResource('zones', \App\Http\Controllers\Api\ZoneController::class);
                Route::apiResource('access-points', \App\Http\Controllers\Api\AccessPointController::class);
                Route::apiResource('parking-spots', \App\Http\Controllers\Api\ParkingSpotController::class);
            });
        });
    });
});