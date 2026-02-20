<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MeetingRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeetingRoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', MeetingRoom::class);
        $rooms = MeetingRoom::query()
            ->with(['tenant', 'building'])
            ->when(!$request->user()->hasRole('super_admin'), fn($q) => $q->where('tenant_id', $request->user()->tenant_id))
            ->when($request->user()->hasRole('super_admin') && $request->tenant_id, fn($q) => $q->where('tenant_id', $request->tenant_id))
            ->when($request->building_id, fn($q, $id) => $q->where('building_id', $id))
            ->when($request->search, fn($q, $search) => 
                $q->where('name', 'like', "%{$search}%")
            )
            ->when($request->has('available'), fn($q) => 
                $q->where('is_active', true)
            )
            ->orderBy('name')
            ->paginate($request->per_page ?? 20);

        return response()->json($rooms);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', MeetingRoom::class);
        $validated = $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'building_id' => 'required|exists:buildings,id',
            'name' => 'required|string|max:255',
            'capacity' => 'nullable|integer|min:1|max:100',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:50',
            'floor' => 'nullable|string|max:20',
            'floor_plan_path' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $room = MeetingRoom::create($validated);

        return response()->json([
            'message' => 'Meeting room created successfully',
            'meeting_room' => $room,
        ], 201);
    }

    public function show(MeetingRoom $meetingRoom): JsonResponse
    {
        $this->authorize('view', $meetingRoom);
        return response()->json($meetingRoom->load(['tenant', 'building', 'visits']));
    }

    public function update(Request $request, MeetingRoom $meetingRoom): JsonResponse
    {
        $this->authorize('update', $meetingRoom);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'capacity' => 'nullable|integer|min:1|max:100',
            'amenities' => 'nullable|array',
            'amenities.*' => 'string|max:50',
            'floor' => 'nullable|string|max:20',
            'floor_plan_path' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $meetingRoom->update($validated);

        return response()->json([
            'message' => 'Meeting room updated successfully',
            'meeting_room' => $meetingRoom->fresh(),
        ]);
    }

    public function destroy(MeetingRoom $meetingRoom): JsonResponse
    {
        $this->authorize('delete', $meetingRoom);
        $meetingRoom->delete();

        return response()->json([
            'message' => 'Meeting room deleted successfully',
        ]);
    }

    /**
     * Get rooms available for a specific time slot
     */
    public function available(Request $request): JsonResponse
    {
        $request->validate([
            'tenant_id' => 'required|exists:tenants,id',
            'building_id' => 'nullable|exists:buildings,id',
            'start' => 'required|date',
            'end' => 'required|date|after:start',
        ]);

        $rooms = MeetingRoom::query()
            ->where('tenant_id', $request->tenant_id)
            ->where('is_active', true)
            ->when($request->building_id, fn($q, $id) => $q->where('building_id', $id))
            ->get()
            ->filter(function ($room) use ($request) {
                // Check if room has no conflicting visits
                return !$room->visits()
                    ->whereIn('status', ['pre_registered', 'checked_in'])
                    ->where(function ($query) use ($request) {
                        $query->whereBetween('scheduled_start', [$request->start, $request->end])
                            ->orWhereBetween('scheduled_end', [$request->start, $request->end])
                            ->orWhere(function ($q) use ($request) {
                                $q->where('scheduled_start', '<=', $request->start)
                                  ->where('scheduled_end', '>=', $request->end);
                            });
                    })
                    ->exists();
            })
            ->values();

        return response()->json($rooms);
    }
}
