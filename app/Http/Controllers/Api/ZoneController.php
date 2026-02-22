<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ZoneRequest;
use App\Http\Resources\ZoneResource;
use App\Models\Building;
use App\Models\Tenant;
use App\Models\Zone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ZoneController extends BaseApiController
{
    public function index(Request $request, Building $building): JsonResponse
    {
        $query = $building->zones();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $zones = $query->orderBy('name', 'asc')->paginate($request->per_page ?? $this->perPage);

        return $this->paginatedResponse($zones, ZoneResource::class);
    }

    public function store(ZoneRequest $request, Building $building): JsonResponse
    {
        $zone = $building->zones()->create($request->validated());

        return $this->success(new ZoneResource($zone), 'Zone created successfully', 201);
    }

    public function show(Building $building, Zone $zone): JsonResponse
    {
        if ($zone->building_id !== $building->id) {
            return $this->notFound('Zone');
        }

        return $this->success(new ZoneResource($zone));
    }

    public function update(ZoneRequest $request, Building $building, Zone $zone): JsonResponse
    {
        if ($zone->building_id !== $building->id) {
            return $this->notFound('Zone');
        }

        $zone->update($request->validated());

        return $this->success(new ZoneResource($zone->fresh()), 'Zone updated successfully');
    }

    public function destroy(Building $building, Zone $zone): JsonResponse
    {
        if ($zone->building_id !== $building->id) {
            return $this->notFound('Zone');
        }

        $zone->delete();

        return $this->success(null, 'Zone deleted successfully');
    }
}