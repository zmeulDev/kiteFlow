<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Visit;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Jobs\SendVisitInviteJob;
use App\Jobs\NotifyHostArrivalJob;
use App\Jobs\SendPostVisitSummaryJob;

class VisitController extends Controller
{
    public function store(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string',
            'visitor_email' => 'required|email',
            'meeting_room_id' => 'required|exists:meeting_rooms,id',
            'host_user_id' => 'required|exists:users,id',
            'scheduled_at' => 'required|date',
            'purpose' => 'nullable|string',
        ]);

        $visitor = Visitor::firstOrCreate(
            ['email' => $validated['visitor_email'], 'tenant_id' => $tenant->id],
            ['name' => $validated['visitor_name']]
        );

        $inviteCode = strtoupper(Str::random(6));

        $visit = Visit::create([
            'tenant_id' => $tenant->id,
            'visitor_id' => $visitor->id,
            'meeting_room_id' => $validated['meeting_room_id'],
            'host_user_id' => $validated['host_user_id'],
            'scheduled_at' => $validated['scheduled_at'],
            'purpose' => $validated['purpose'] ?? 'Meeting',
            'status' => 'pending',
            'invite_code' => $inviteCode,
        ]);

        SendVisitInviteJob::dispatch($visit);

        return response()->json(['data' => $visit], 201);
    }

    public function showByCode(Tenant $tenant, $code)
    {
        $visit = Visit::with(['visitor', 'meetingRoom', 'host'])
             ->where('tenant_id', $tenant->id)
             ->where('invite_code', $code)
             ->whereIn('status', ['pending', 'checked_in'])
             ->firstOrFail();

        return response()->json(['data' => $visit]);
    }

    public function checkIn(Request $request, Tenant $tenant, Visit $visit)
    {
        if ($visit->tenant_id !== $tenant->id) {
            abort(403);
        }

        $validated = $request->validate([
            'nda_signature' => 'required|string'
        ]);

        $visit->update([
            'status' => 'checked_in',
            'check_in_time' => now(),
            'nda_signature' => $validated['nda_signature']
        ]);

        NotifyHostArrivalJob::dispatch($visit);

        return response()->json(['data' => $visit]);
    }

    public function checkOut(Request $request, Tenant $tenant, Visit $visit)
    {
        if ($visit->tenant_id !== $tenant->id) {
            abort(403);
        }

        $visit->update([
            'status' => 'completed',
            'check_out_time' => now(),
        ]);

        SendPostVisitSummaryJob::dispatch($visit);

        return response()->json(['data' => $visit]);
    }
}
