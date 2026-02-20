<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Tenant;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Tenant $tenant)
    {
        return response()->json([
            'data' => $tenant->locations()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string',
        ]);

        $location = $tenant->locations()->create($validated);

        return response()->json(['data' => $location], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tenant $tenant, Location $location)
    {
        if ($location->tenant_id !== $tenant->id) {
            abort(403);
        }

        return response()->json(['data' => $location]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tenant $tenant, Location $location)
    {
        if ($location->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'address' => 'nullable|string',
        ]);

        $location->update($validated);

        return response()->json(['data' => $location]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant, Location $location)
    {
        if ($location->tenant_id !== $tenant->id) {
            abort(403);
        }

        $location->delete();

        return response()->noContent();
    }
}
