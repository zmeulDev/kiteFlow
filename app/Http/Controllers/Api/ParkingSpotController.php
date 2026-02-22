<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ParkingSpotRequest;
use App\Http\Resources\ParkingSpotResource;
use App\Models\Building;
use App\Models\ParkingSpot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ParkingSpotController extends BaseApiController
{
    public function index(Request $request, Building $building): JsonResponse
    {
        $query = $building->parkingSpots();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('spot_type')) {
            $query->where('spot_type', $request->spot_type);
        }

        $spots = $query->orderBy('number', 'asc')->paginate($request->per_page ?? $this->perPage);

        return $this->paginatedResponse($spots, ParkingSpotResource::class);
    }

    public function store(ParkingSpotRequest $request, Building $building): JsonResponse
    {
        $spot = $building->parkingSpots()->create($request->validated());

        return $this->success(new ParkingSpotResource($spot), 'Parking spot created successfully', 201);
    }

    public function show(Building $building, ParkingSpot $parkingSpot): JsonResponse
    {
        if ($parkingSpot->building_id !== $building->id) {
            return $this->notFound('Parking spot');
        }

        return $this->success(new ParkingSpotResource($parkingSpot));
    }

    public function update(ParkingSpotRequest $request, Building $building, ParkingSpot $parkingSpot): JsonResponse
    {
        if ($parkingSpot->building_id !== $building->id) {
            return $this->notFound('Parking spot');
        }

        $parkingSpot->update($request->validated());

        return $this->success(new ParkingSpotResource($parkingSpot->fresh()), 'Parking spot updated successfully');
    }

    public function destroy(Building $building, ParkingSpot $parkingSpot): JsonResponse
    {
        if ($parkingSpot->building_id !== $building->id) {
            return $this->notFound('Parking spot');
        }

        $parkingSpot->delete();

        return $this->success(null, 'Parking spot deleted successfully');
    }
}