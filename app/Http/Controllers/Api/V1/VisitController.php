<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\User;
use App\Models\MeetingRoom;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VisitController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Visit::class);
        $visits = Visit::query()
            ->with(['visitor', 'tenant', 'subTenant', 'hostUser', 'meetingRoom', 'building'])
            ->when(!$request->user()->hasRole('super_admin'), fn($q) => $q->where('tenant_id', $request->user()->tenant_id))
            ->when($request->user()->sub_tenant_id, fn($q) => $q->where('sub_tenant_id', $request->user()->sub_tenant_id))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->when($request->date, fn($q, $d) => $q->whereDate('scheduled_start', $d))
            ->orderBy('scheduled_start', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json($visits);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Visit::class);
        $validated = $request->validate([
            'visitor.first_name' => 'required|string|max:100',
            'visitor.last_name' => 'required|string|max:100',
            'visitor.email' => 'nullable|email',
            'visitor.phone' => 'nullable|string',
            'visitor.company' => 'nullable|string',
            'host_user_id' => 'nullable|exists:users,id',
            'meeting_room_id' => 'nullable|exists:meeting_rooms,id',
            'building_id' => 'nullable|exists:buildings,id',
            'scheduled_start' => 'required|date|after:now',
            'scheduled_end' => 'required|date|after:scheduled_start',
            'purpose' => 'nullable|string|max:500',
        ]);

        $tenantId = $request->user()->tenant_id;

        // Find or create visitor
        $visitor = Visitor::query()
            ->when($validated['visitor']['email'] ?? null, fn($q, $e) => $q->orWhere('email', $e))
            ->when($validated['visitor']['phone'] ?? null, fn($q, $p) => $q->orWhere('phone', $p))
            ->first();

        if (!$visitor) {
            $visitor = Visitor::create(array_merge(
                $validated['visitor'],
                ['tenant_id' => $tenantId]
            ));
        }

        $visit = Visit::create([
            'visitor_id' => $visitor->id,
            'tenant_id' => $tenantId,
            'host_user_id' => $validated['host_user_id'] ?? null,
            'meeting_room_id' => $validated['meeting_room_id'] ?? null,
            'building_id' => $validated['building_id'] ?? null,
            'visit_code' => Visit::generateVisitCode(),
            'scheduled_start' => $validated['scheduled_start'],
            'scheduled_end' => $validated['scheduled_end'],
            'purpose' => $validated['purpose'] ?? null,
            'status' => 'pre_registered',
        ]);

        // Dispatch notification job
        \App\Jobs\SendPreRegistrationNotification::dispatch($visit);

        return response()->json([
            'message' => 'Visit scheduled successfully',
            'visit' => $visit->load(['visitor', 'hostUser', 'meetingRoom']),
        ], 201);
    }

    public function show(Visit $visit): JsonResponse
    {
        $this->authorize('view', $visit);
        return response()->json($visit->load(['visitor', 'tenant', 'hostUser', 'meetingRoom', 'building', 'checkIns']));
    }

    public function update(Request $request, Visit $visit): JsonResponse
    {
        $this->authorize('update', $visit);
        $validated = $request->validate([
            'scheduled_start' => 'sometimes|date',
            'scheduled_end' => 'sometimes|date|after:scheduled_start',
            'meeting_room_id' => 'nullable|exists:meeting_rooms,id',
            'purpose' => 'nullable|string|max:500',
            'status' => 'sometimes|in:pre_registered,checked_in,checked_out,cancelled,no_show',
        ]);

        $visit->update($validated);

        return response()->json(['message' => 'Visit updated', 'visit' => $visit->fresh()]);
    }

    public function destroy(Visit $visit): JsonResponse
    {
        $this->authorize('delete', $visit);
        $visit->delete();
        return response()->json(['message' => 'Visit deleted']);
    }

    public function checkIn(Request $request, Visit $visit): JsonResponse
    {
        $this->authorize('update', $visit);
        if ($visit->status !== 'pre_registered') {
            return response()->json(['message' => 'Visit cannot be checked in'], 400);
        }

        $validated = $request->validate([
            'checked_in_by' => 'required|exists:users,id',
            'meeting_room_id' => 'nullable|exists:meeting_rooms,id',
        ]);

        $visit->update(['status' => 'checked_in', 'checked_in_at' => now()]);

        \App\Models\CheckIn::create([
            'visit_id' => $visit->id,
            'visitor_id' => $visit->visitor_id,
            'meeting_room_id' => $validated['meeting_room_id'] ?? $visit->meeting_room_id,
            'checked_in_by' => $validated['checked_in_by'],
            'check_in_time' => now(),
            'check_in_method' => 'manual',
        ]);

        \App\Jobs\SendHostArrivalNotification::dispatch($visit);
        \App\Jobs\SendPostCheckInEmail::dispatch($visit)->delay(now()->addSeconds(30));

        return response()->json(['message' => 'Checked in', 'visit' => $visit->fresh()]);
    }

    public function checkOut(Request $request, Visit $visit): JsonResponse
    {
        $this->authorize('update', $visit);
        if ($visit->status !== 'checked_in') {
            return response()->json(['message' => 'Visitor not checked in'], 400);
        }

        $validated = $request->validate([
            'checked_out_by' => 'required|exists:users,id',
        ]);

        $visit->update(['status' => 'checked_out', 'checked_out_at' => now()]);

        $visit->checkIns()->whereNull('check_out_time')->update([
            'checked_out_by' => $validated['checked_out_by'],
            'check_out_time' => now(),
            'check_out_method' => 'manual',
        ]);

        return response()->json(['message' => 'Checked out', 'visit' => $visit->fresh()]);
    }

    public function lookup(string $code): JsonResponse
    {
        $visit = Visit::where('visit_code', strtoupper($code))
            ->with(['visitor', 'tenant', 'hostUser', 'meetingRoom', 'building'])
            ->first();

        if (!$visit) {
            return response()->json(['message' => 'Visit not found'], 404);
        }

        return response()->json($visit);
    }

    public static function generateVisitCode(): string
    {
        do { $code = strtoupper(Str::random(8)); } while (self::where('visit_code', $code)->exists());
        return $code;
    }
}
