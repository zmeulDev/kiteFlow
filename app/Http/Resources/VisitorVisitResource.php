<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitorVisitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'visitor' => new VisitorResource($this->whenLoaded('visitor')),
            'host' => new UserResource($this->whenLoaded('host')),
            'meeting' => new MeetingResource($this->whenLoaded('meeting')),
            'purpose' => $this->purpose,
            'check_in_method' => $this->check_in_method,
            'check_in_at' => $this->check_in_at->toISOString(),
            'check_out_at' => $this->check_out_at?->toISOString(),
            'badge_number' => $this->badge_number,
            'badge_type' => $this->badge_type,
            'status' => $this->status,
            'duration' => $this->check_out_at ? $this->getDurationFormatted() : null,
            'duration_minutes' => $this->getDurationInMinutes(),
            'notes' => $this->notes,
            'is_active' => $this->isActive(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}