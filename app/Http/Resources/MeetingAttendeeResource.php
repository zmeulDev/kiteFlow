<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingAttendeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $attendeeResource = $this->attendee_type === \App\Models\Visitor::class
            ? new VisitorResource($this->attendee)
            : new UserResource($this->attendee);

        return [
            'id' => $this->id,
            'type' => $this->type,
            'status' => $this->status,
            'responded_at' => $this->responded_at?->toISOString(),
            'notes' => $this->notes,
            'attendee_type' => class_basename($this->attendee_type),
            'attendee' => $attendeeResource,
            'is_required' => $this->isRequired(),
            'has_responded' => $this->hasResponded(),
        ];
    }
}