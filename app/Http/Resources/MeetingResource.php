<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'purpose' => $this->purpose,
            'meeting_room' => new MeetingRoomResource($this->whenLoaded('meetingRoom')),
            'host' => new UserResource($this->whenLoaded('host')),
            'attendees' => MeetingAttendeeResource::collection($this->whenLoaded('attendees')),
            'start_at' => $this->start_at->toISOString(),
            'end_at' => $this->end_at->toISOString(),
            'timezone' => $this->timezone,
            'duration' => $this->getDurationFormatted(),
            'duration_minutes' => $this->getDurationInMinutes(),
            'is_all_day' => $this->is_all_day,
            'is_recurring' => $this->is_recurring,
            'recurrence_rule' => $this->recurrence_rule,
            'status' => $this->status,
            'meeting_type' => $this->meeting_type,
            'meeting_url' => $this->meeting_url,
            'is_past' => $this->isPast(),
            'is_ongoing' => $this->isOngoing(),
            'is_upcoming' => $this->isUpcoming(),
            'attendees_count' => $this->whenCounted('attendees'),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}