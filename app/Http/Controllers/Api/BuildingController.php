<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BuildingResource;
use App\Models\Building;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index(Request $request, Tenant $tenant): JsonResponse
    {
        $buildings = Building::where('tenant_id', $tenant->id)
            ->orderBy('name')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => BuildingResource::collection($buildings->items()),
            'meta' => [
                'current_page' => $buildings->currentPage(),
                'last_page' => $buildings->lastPage(),
                'per_page' => $buildings->perPage(),
                'total' => $buildings->total(),
            ],
        ]);
    }

    public function store(Request $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'floors' => 'nullable|integer|min:1',
        ]);

        $building = Building::create([
            ...$validated,
            'tenant_id' => $tenant->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Building created',
            'data' => new BuildingResource($building),
        ], 201);
    }

    public function show(Building $building): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new BuildingResource($building),
        ]);
    }

    public function update(Request $request, Building $building): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'floors' => 'nullable|integer|min:1',
        ]);

        $building->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Building updated',
            'data' => new BuildingResource($building->fresh()),
        ]);
    }

    public function destroy(Building $building): JsonResponse
    {
        $building->delete();

        return response()->json([
            'success' => true,
            'message' => 'Building deleted',
        ]);
    }
}