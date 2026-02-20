<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\MeetingRoom;
use Illuminate\Http\Request;

class MeetingRoomController extends Controller
{
    public function index(Request $request, Tenant $tenant)
    {
        $rooms = $tenant->meetingRooms()->with('location')->get();
        return response()->json(['data' => $rooms]);
    }

    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'location_id' => 'required|exists:locations,id',
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'amenities' => 'nullable|array',
        ]);

        $room = $tenant->meetingRooms()->create($validated);
        
        return response()->json(['data' => $room], 201);
    }

    public function show(Tenant $tenant, MeetingRoom $room)
    {
        if ($room->tenant_id !== $tenant->id) {
            abort(403);
        }

        return response()->json(['data' => $room]);
    }

    public function update(Request $request, Tenant $tenant, MeetingRoom $room)
    {
        if ($room->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'location_id' => 'sometimes|exists:locations,id',
            'name' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer|min:1',
            'amenities' => 'nullable|array',
        ]);

        $room->update($validated);
        
        return response()->json(['data' => $room]);
    }

    public function destroy(Tenant $tenant, MeetingRoom $room)
    {
        if ($room->tenant_id !== $tenant->id) {
            abort(403);
        }

        $room->delete();
        
        return response()->noContent();
    }
}
