<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        if (!$user->is_active) {
            return $this->error('Your account has been deactivated. Please contact support.', 403);
        }

        $user->recordLogin();

        $token = $user->createToken(
            $request->device_name ?? 'api-token',
            ['*'],
            now()->addDays($request->remember ? 30 : 1)
        );

        return $this->success([
            'user' => new UserResource($user->load('roles', 'tenants')),
            'token' => $token->plainTextToken,
            'expires_at' => $token->accessToken->expires_at?->toISOString(),
        ], 'Login successful');
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'timezone' => $request->timezone ?? 'UTC',
            'locale' => $request->locale ?? 'en',
            'is_active' => true,
        ]);

        // Assign default role
        $user->assignRole('user');

        // If this is a tenant registration, create the tenant and assign ownership
        if ($request->has('tenant_name')) {
            $tenant = Tenant::create([
                'name' => $request->tenant_name,
                'email' => $request->email,
                'status' => 'trial',
                'trial_ends_at' => now()->addDays(14),
            ]);
            
            $tenant->users()->attach($user->id, ['is_owner' => true]);
            $user->assignRole('admin');
        }

        $token = $user->createToken('registration-token', ['*'], now()->addDays(30));

        return $this->success([
            'user' => new UserResource($user->load('roles', 'tenants')),
            'token' => $token->plainTextToken,
        ], 'Registration successful', 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    public function logoutAll(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return $this->success(null, 'Logged out from all devices successfully');
    }

    public function user(Request $request): JsonResponse
    {
        return $this->success(new UserResource($request->user()->load('roles', 'tenants')));
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:100',
            'timezone' => 'sometimes|string|max:50',
            'locale' => 'sometimes|string|max:10',
            'avatar' => 'nullable|image|max:2048',
            'preferences' => 'nullable|array',
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return $this->success(new UserResource($user->fresh()), 'Profile updated successfully');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        // TODO: Implement password reset email
        // For now, return success to prevent email enumeration

        return $this->success(null, 'If the email exists, a password reset link has been sent.');
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed|min:8',
        ]);

        // TODO: Implement password reset logic

        return $this->success(null, 'Password reset successfully');
    }
}