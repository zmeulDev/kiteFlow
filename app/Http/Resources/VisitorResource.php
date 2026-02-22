<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'company' => $this->company,
            'id_type' => $this->id_type,
            'id_number' => $this->id_number,
            'photo' => $this->photo ? asset('storage/' . $this->photo) : null,
            'notes' => $this->notes,
            'is_blacklisted' => $this->is_blacklisted,
            'blacklist_reason' => $this->blacklist_reason,
            'latest_visit' => new VisitorVisitResource($this->whenLoaded('latestVisit')),
            'visits' => VisitorVisitResource::collection($this->whenLoaded('visits')),
            'visits_count' => $this->whenCounted('visits'),
            'is_checked_in' => $this->isCheckedIn(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}