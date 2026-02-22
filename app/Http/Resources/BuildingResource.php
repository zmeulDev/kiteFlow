<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BuildingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'full_address' => $this->full_address,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'postal_code' => $this->postal_code,
            'country' => $this->country,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'floors' => $this->floors,
            'floor_list' => $this->getFloorList(),
            'is_active' => $this->is_active,
            'zones_count' => $this->whenCounted('zones'),
            'access_points_count' => $this->whenCounted('accessPoints'),
            'meeting_rooms_count' => $this->whenCounted('meetingRooms'),
            'parking_spots_count' => $this->whenCounted('parkingSpots'),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}