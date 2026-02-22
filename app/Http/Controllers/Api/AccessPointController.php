<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\AccessPointRequest;
use App\Http\Resources\AccessPointResource;
use App\Models\AccessPoint;
use App\Models\Building;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccessPointController extends BaseApiController
{
    public function index(Request $request, Building $building): JsonResponse
    {
        $query = $building->accessPoints();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $points = $query->orderBy('name', 'asc')->paginate($request->per_page ?? $this->perPage);

        return $this->paginatedResponse($points, AccessPointResource::class);
    }

    public function store(AccessPointRequest $request, Building $building): JsonResponse
    {
        $point = $building->accessPoints()->create($request->validated());

        return $this->success(new AccessPointResource($point), 'Access point created successfully', 201);
    }

    public function show(Building $building, AccessPoint $accessPoint): JsonResponse
    {
        if ($accessPoint->building_id !== $building->id) {
            return $this->notFound('Access point');
        }

        return $this->success(new AccessPointResource($accessPoint));
    }

    public function update(AccessPointRequest $request, Building $building, AccessPoint $accessPoint): JsonResponse
    {
        if ($accessPoint->building_id !== $building->id) {
            return $this->notFound('Access point');
        }

        $accessPoint->update($request->validated());

        return $this->success(new AccessPointResource($accessPoint->fresh()), 'Access point updated successfully');
    }

    public function destroy(Building $building, AccessPoint $accessPoint): JsonResponse
    {
        if ($accessPoint->building_id !== $building->id) {
            return $this->notFound('Access point');
        }

        $accessPoint->delete();

        return $this->success(null, 'Access point deleted successfully');
    }
}