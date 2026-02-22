<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * List all users (admin only)
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('roles', 'tenants');

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $request->role));
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Get user details
     */
    public function show(User $user): JsonResponse
    {
        $user->load(['roles', 'tenants']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Create new user (admin only)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'nullable|string|exists:roles,name',
            'tenant_id' => 'nullable|exists:tenants,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (isset($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        if (isset($validated['tenant_id'])) {
            $user->tenants()->attach($validated['tenant_id']);
        }

        $user->load(['roles', 'tenants']);

        return response()->json([
            'success' => true,
            'message' => 'User created',
            'data' => $user,
        ], 201);
    }

    /**
     * Update user (admin only)
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'role' => 'nullable|string|exists:roles,name',
            'is_active' => 'nullable|boolean',
        ]);

        $user->update($validated);

        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        $user->load(['roles', 'tenants']);

        return response()->json([
            'success' => true,
            'message' => 'User updated',
            'data' => $user->fresh()->load(['roles', 'tenants']),
        ]);
    }

    /**
     * Delete user (admin only)
     */
    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted',
        ]);
    }

    /**
     * Get current user's profile
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user()->load(['roles', 'tenants']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Update current user's profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'avatar' => 'nullable|string',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], 403);
        }

        $user->update([
            'password' => $validated['password'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated',
        ]);
    }

    /**
     * Get user's notification preferences
     */
    public function notificationPreferences(): JsonResponse
    {
        $user = Auth::user();
        $preferences = $user->preferences['notifications'] ?? [];

        return response()->json([
            'success' => true,
            'data' => $preferences,
        ]);
    }

    /**
     * Update user's notification preferences
     */
    public function updateNotificationPreferences(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'meeting_reminders' => 'boolean',
            'visitor_check_in_alerts' => 'boolean',
        ]);

        $preferences = $user->preferences ?? [];
        foreach ($validated as $key => $value) {
            $preferences['notifications'][$key] = $value;
        }

        $user->preferences = $preferences;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated',
            'data' => $preferences,
        ]);
    }
}