<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\MeetingRequest;
use App\Http\Resources\MeetingResource;
use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingController extends BaseApiController
{
    public function index(Request $request, Tenant $tenant): JsonResponse
    {
        $query = $tenant->meetings()->with(['meetingRoom', 'host', 'attendees']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->whereDate('start_at', $request->date);
        }

        if ($request->has('from') && $request->has('to')) {
            $query->whereBetween('start_at', [$request->from, $request->to]);
        }

        if ($request->has('meeting_room_id')) {
            $query->where('meeting_room_id', $request->meeting_room_id);
        }

        if ($request->has('host_id')) {
            $query->where('host_id', $request->host_id);
        }

        $meetings = $query->orderBy('start_at', 'asc')->paginate($request->per_page ?? $this->perPage);

        return $this->paginatedResponse($meetings, MeetingResource::class);
    }

    public function store(MeetingRequest $request, Tenant $tenant): JsonResponse
    {
        $validated = $request->validated();
        $validated['host_id'] = $validated['host_id'] ?? auth()->id();
        $validated['tenant_id'] = $tenant->id;

        // Check room availability if a room is assigned
        if (!empty($validated['meeting_room_id'])) {
            // Find room without tenant scoping to allow sub-tenants to access parent rooms
            $room = MeetingRoom::withoutGlobalScopes()->findOrFail($validated['meeting_room_id']);

            // Check if room belongs to current tenant or its parent tenant
            $accessibleTenantIds = \App\Models\Tenant::getAccessibleTenantIds($tenant->id);
            // Also check parent tenant (for sub-tenants accessing parent resources)
            if ($tenant->parent_id) {
                $accessibleTenantIds[] = $tenant->parent_id;
                // Also include all ancestors
                $parent = \App\Models\Tenant::find($tenant->parent_id);
                while ($parent && $parent->parent_id) {
                    $accessibleTenantIds[] = $parent->parent_id;
                    $parent = \App\Models\Tenant::find($parent->parent_id);
                }
            }

            if (!in_array($room->tenant_id, $accessibleTenantIds)) {
                return $this->error('The selected meeting room is not accessible', 403);
            }

            if (!$room->isAvailable($validated['start_at'], $validated['end_at'])) {
                return $this->error('The selected meeting room is not available for the specified time', 422);
            }
        }

        $meeting = Meeting::create($validated);

        // Add host as required attendee
        $meeting->addAttendee(auth()->user(), 'required');

        // Add visitors if provided
        if ($request->has('visitor_ids')) {
            foreach ($request->visitor_ids as $visitorId) {
                $visitor = \App\Models\Visitor::findOrFail($visitorId);
                $meeting->addAttendee($visitor, 'required');
            }
        }

        return $this->success(new MeetingResource($meeting->load(['meetingRoom', 'host', 'attendees'])), 'Meeting created successfully', 201);
    }

    public function show(Tenant $tenant, Meeting $meeting): JsonResponse
    {
        if ($meeting->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting');
        }

        return $this->success(new MeetingResource($meeting->load(['meetingRoom', 'host', 'attendees', 'visitorVisits'])));
    }

    public function update(MeetingRequest $request, Tenant $tenant, Meeting $meeting): JsonResponse
    {
        if ($meeting->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting');
        }

        $validated = $request->validated();

        // Check room availability if changing room or time
        if (!empty($validated['meeting_room_id']) || !empty($validated['start_at']) || !empty($validated['end_at'])) {
            $roomId = $validated['meeting_room_id'] ?? $meeting->meeting_room_id;
            $startAt = $validated['start_at'] ?? $meeting->start_at;
            $endAt = $validated['end_at'] ?? $meeting->end_at;
            $room = MeetingRoom::findOrFail($roomId);
            
            if (!$room->isAvailable($startAt, $endAt, $meeting->id)) {
                return $this->error('The selected meeting room is not available for the specified time', 422);
            }
        }

        $meeting->update($validated);

        return $this->success(new MeetingResource($meeting->fresh()->load(['meetingRoom', 'host', 'attendees'])), 'Meeting updated successfully');
    }

    public function destroy(Tenant $tenant, Meeting $meeting): JsonResponse
    {
        if ($meeting->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting');
        }

        // Check if user is the meeting host or has permission to delete meetings
        if (!auth()->user()->can('delete meetings') && $meeting->host_id !== auth()->id()) {
            return $this->error('You can only delete your own meetings', 403);
        }

        $meeting->delete();

        return $this->success(null, 'Meeting deleted successfully');
    }

    public function cancel(Request $request, Tenant $tenant, Meeting $meeting): JsonResponse
    {
        if ($meeting->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting');
        }

        $request->validate(['reason' => 'required|string|max:500']);

        $meeting->cancel($request->reason);

        return $this->success(new MeetingResource($meeting->fresh()), 'Meeting cancelled successfully');
    }

    public function attendees(Tenant $tenant, Meeting $meeting): JsonResponse
    {
        if ($meeting->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting');
        }

        return $this->success(\App\Http\Resources\MeetingAttendeeResource::collection($meeting->attendees));
    }

    public function addAttendee(Request $request, Tenant $tenant, Meeting $meeting): JsonResponse
    {
        if ($meeting->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting');
        }

        $request->validate([
            'attendee_type' => 'required|in:visitor,user',
            'attendee_id' => 'required|integer',
            'type' => 'in:required,optional',
        ]);

        $modelClass = $request->attendee_type === 'visitor' ? \App\Models\Visitor::class : \App\Models\User::class;
        $attendee = $modelClass::findOrFail($request->attendee_id);

        $meetingAttendee = $meeting->addAttendee($attendee, $request->type ?? 'required');

        return $this->success(new \App\Http\Resources\MeetingAttendeeResource($meetingAttendee), 'Attendee added successfully', 201);
    }

    public function removeAttendee(Tenant $tenant, Meeting $meeting, int $attendeeId): JsonResponse
    {
        if ($meeting->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting');
        }

        $meeting->attendees()->where('id', $attendeeId)->delete();

        return $this->success(null, 'Attendee removed successfully');
    }

    public function inviteVisitor(Request $request, Tenant $tenant, Meeting $meeting): JsonResponse
    {
        if ($meeting->tenant_id !== $tenant->id) {
            return $this->notFound('Meeting');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
        ]);

        // Find or create the visitor
        $visitor = \App\Models\Visitor::firstOrCreate(
            [
                'tenant_id' => $tenant->id,
                'email' => $request->email,
            ],
            [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'company' => $request->company,
            ]
        );

        // Add as attendee
        $meeting->addAttendee($visitor, 'required');

        return $this->success([
            'visitor' => new \App\Http\Resources\VisitorResource($visitor),
            'meeting' => new MeetingResource($meeting->fresh()->load(['meetingRoom', 'host', 'attendees'])),
        ], 'Visitor invited to meeting successfully');
    }

    public function today(Tenant $tenant): JsonResponse
    {
        $meetings = $tenant->meetings()
            ->with(['meetingRoom', 'host', 'attendees'])
            ->whereDate('start_at', today())
            ->orderBy('start_at')
            ->get();

        return $this->success(MeetingResource::collection($meetings));
    }

    public function upcoming(Request $request, Tenant $tenant): JsonResponse
    {
        $meetings = $tenant->meetings()
            ->with(['meetingRoom', 'host', 'attendees'])
            ->where('start_at', '>', now())
            ->where('status', 'scheduled')
            ->orderBy('start_at')
            ->limit($request->limit ?? 10)
            ->get();

        return $this->success(MeetingResource::collection($meetings));
    }
}