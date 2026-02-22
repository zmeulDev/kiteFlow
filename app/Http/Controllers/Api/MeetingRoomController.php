<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\MeetingRoomRequest;
use App\Http\Resources\MeetingRoomResource;
use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingRoomController extends BaseApiController
{
    public function index(Request $request, Tenant $tenant): JsonResponse
    {
        $query = $tenant->meetingRooms()->with(['building']);

        if ($request->has('building_id')) {
            $query->where('building_id', $request->building_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $rooms = $query->orderBy('name', 'asc')->paginate($request->per_page ?? $this->perPage);

        return $this->paginatedResponse($rooms, MeetingRoomResource::class);
    }

    public function store(MeetingRoomRequest $request, Tenant $tenant): JsonResponse
    {
        $room = $tenant->meetingRooms()->create($request->validated());

        return $this->success(new MeetingRoomResource($room->load('building')), 'Meeting room created successfully', 201);
    }

    public function show(Tenant $tenant, MeetingRoom $meetingRoom): JsonResponse
    {
        if ($meetingRoom->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting room');
        }

        return $this->success(new MeetingRoomResource($meetingRoom->load('building')));
    }

    public function update(MeetingRoomRequest $request, Tenant $tenant, MeetingRoom $meetingRoom): JsonResponse
    {
        if ($meetingRoom->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting room');
        }

        $meetingRoom->update($request->validated());

        return $this->success(new MeetingRoomResource($meetingRoom->fresh()->load('building')), 'Meeting room updated successfully');
    }

    public function destroy(Tenant $tenant, MeetingRoom $meetingRoom): JsonResponse
    {
        if ($meetingRoom->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting room');
        }

        // Check if there are any scheduled meetings
        $hasUpcomingMeetings = $meetingRoom->meetings()
            ->where('start_at', '>', now())
            ->where('status', 'scheduled')
            ->exists();

        if ($hasUpcomingMeetings) {
            return $this->error('Cannot delete meeting room with upcoming meetings', 422);
        }

        $meetingRoom->delete();

        return $this->success(null, 'Meeting room deleted successfully');
    }

    public function availability(Request $request, Tenant $tenant, MeetingRoom $meetingRoom): JsonResponse
    {
        if ($meetingRoom->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting room');
        }

        $request->validate([
            'date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
        ]);

        $date = $request->date;
        $startTime = $request->start_time ? \Carbon\Carbon::parse("$date $request->start_time") : null;
        $endTime = $request->end_time ? \Carbon\Carbon::parse("$date $request->end_time") : null;

        // Get existing meetings for the day
        $query = Meeting::where('meeting_room_id', $meetingRoom->id)
            ->whereDate('start_at', $date)
            ->where('status', '!=', 'cancelled');

        if ($startTime && $endTime) {
            // Check specific time slot availability
            $isAvailable = $meetingRoom->isAvailable($startTime, $endTime);

            return $this->success([
                'available' => $isAvailable,
                'meetings' => MeetingResource::collection($query->get()),
            ]);
        }

        // Get all meetings for the day
        $meetings = $query->orderBy('start_at')->get();

        // Calculate available slots
        $slots = $meetingRoom->getAvailableSlots($date);

        return $this->success([
            'meetings' => MeetingResource::collection($meetings),
            'available_slots' => $slots,
        ]);
    }
}