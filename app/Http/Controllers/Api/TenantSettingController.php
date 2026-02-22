<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateTenantSettingRequest;
use App\Http\Resources\TenantSettingResource;
use App\Models\TenantSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TenantSettingController extends Controller
{
    /**
     * Display a listing of tenant settings.
     */
    public function index(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found',
            ], 404);
        }

        $settings = TenantSetting::where('tenant_id', $tenant->id)
            ->when($request->has('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->has('key'), fn ($q) => $q->where('key', $request->key))
            ->get();

        return response()->json([
            'success' => true,
            'data' => TenantSettingResource::collection($settings),
        ]);
    }

    /**
     * Store a newly created tenant setting.
     */
    public function store(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found',
            ], 404);
        }

        $validated = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'required',
            'type' => 'in:string,integer,float,boolean,array,json',
        ]);

        $setting = TenantSetting::updateOrCreate(
            [
                'tenant_id' => $tenant->id,
                'key' => $validated['key'],
            ],
            [
                'value' => $validated['value'],
                'type' => $validated['type'] ?? 'string',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => $setting->wasRecentlyCreated ? 'Setting created' : 'Setting updated',
            'data' => new TenantSettingResource($setting),
        ], 201);
    }

    /**
     * Display the specified tenant setting.
     */
    public function show(Request $request, string $key): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found',
            ], 404);
        }

        $setting = TenantSetting::where('tenant_id', $tenant->id)
            ->where('key', $key)
            ->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new TenantSettingResource($setting),
        ]);
    }

    /**
     * Update the specified tenant setting.
     */
    public function update(UpdateTenantSettingRequest $request, string $key): JsonResponse
    {
        $tenant = $request->route('tenant');

        $setting = TenantSetting::where('tenant_id', $tenant->id)
            ->where('key', $key)
            ->firstOrFail();

        $setting->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Setting updated',
            'data' => new TenantSettingResource($setting->fresh()),
        ]);
    }

    /**
     * Remove the specified tenant setting.
     */
    public function destroy(Request $request, string $key): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found',
            ], 404);
        }

        $setting = TenantSetting::where('tenant_id', $tenant->id)
            ->where('key', $key)
            ->first();

        if (!$setting) {
            return response()->json([
                'success' => false,
                'message' => 'Setting not found',
            ], 404);
        }

        $setting->delete();

        return response()->json([
            'success' => true,
            'message' => 'Setting deleted',
        ]);
    }

    /**
     * Get all tenant settings grouped by category.
     */
    public function getGroupedSettings(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found',
            ], 404);
        }

        $settings = TenantSetting::where('tenant_id', $tenant->id)->get();
        $grouped = $settings->groupBy(function ($setting) {
            return explode('.', $setting->key)[0];
        });

        return response()->json([
            'success' => true,
            'data' => $grouped->map(function ($group) {
                return $group->pluck('value', 'key');
            }),
        ]);
    }

    /**
     * Batch update multiple tenant settings.
     */
    public function batchUpdate(Request $request): JsonResponse
    {
        $tenant = $request->attributes->get('tenant');

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Tenant not found',
            ], 404);
        }

        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string|max:255',
            'settings.*.value' => 'required',
            'settings.*.type' => 'in:string,integer,float,boolean,array,json',
        ]);

        $updated = [];
        foreach ($validated['settings'] as $settingData) {
            $setting = TenantSetting::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'key' => $settingData['key'],
                ],
                [
                    'value' => $settingData['value'],
                    'type' => $settingData['type'] ?? 'string',
                ]
            );
            $updated[] = new TenantSettingResource($setting);
        }

        return response()->json([
            'success' => true,
            'message' => count($updated) . ' settings updated',
            'data' => $updated,
        ]);
    }
}