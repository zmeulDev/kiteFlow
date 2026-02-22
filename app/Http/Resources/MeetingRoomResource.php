<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingRoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'code' => $this->code,
            'location' => $this->location,
            'capacity' => $this->capacity,
            'description' => $this->description,
            'amenities' => $this->amenities,
            'image' => $this->image ? asset('storage/' . $this->image) : null,
            'is_active' => $this->is_active,
            'settings' => $this->settings,
            'upcoming_meetings_count' => $this->whenCounted('meetings'),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}