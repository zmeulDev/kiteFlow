<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Building;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $buildings = Building::query()
            ->with('tenant')
            ->when($request->tenant_id, fn($q, $id) => $q->where('tenant_id', $id))
            ->when($request->search, fn($q, $search) => 
                $q->where('name', 'like', "%{$search}%")
            )
            ->orderBy('name')
            ->paginate($request->per_page ?? 20);

        return response()->json($buildings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $building = Building::create($validated);

        return response()->json([
            'message' => 'Building created successfully',
            'building' => $building,
        ], 201);
    }

    public function show(Building $building): JsonResponse
    {
        return response()->json($building->load(['tenant', 'meetingRooms']));
    }

    public function update(Request $request, Building $building): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean',
        ]);

        $building->update($validated);

        return response()->json([
            'message' => 'Building updated successfully',
            'building' => $building->fresh(),
        ]);
    }

    public function destroy(Building $building): JsonResponse
    {
        $building->delete();

        return response()->json([
            'message' => 'Building deleted successfully',
        ]);
    }
}
