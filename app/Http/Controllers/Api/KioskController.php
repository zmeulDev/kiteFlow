<?php

namespace App\Http\Controllers\Api;

use App\Models\AccessLog;
use App\Models\AccessPoint;
use App\Models\Tenant;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class KioskController extends BaseApiController
{
    public function checkIn(Request $request, Tenant $tenant, AccessPoint $accessPoint): JsonResponse
    {
        // Check tenant status
        if ($tenant->status !== 'active') {
            return $this->error('This tenant is currently suspended and cannot accept visitors.', 403);
        }

        if (!$accessPoint->isKiosk() || !$accessPoint->is_active) {
            return $this->error('Invalid kiosk access point', 403);
        }

        // Validate based on check-in method
        $method = $request->input('method', 'manual');

        if ($method === 'code') {
            $request->validate([
                'code' => 'required|string',
                'accepted_terms' => 'required|accepted',
                'accepted_gdpr' => 'required|accepted',
                'accepted_nda' => 'nullable|accepted',
            ]);

            // Find meeting by check-in code
            $meeting = \App\Models\Meeting::where('tenant_id', $tenant->id)
                ->where('check_in_code', $request->code)
                ->where('status', 'scheduled')
                ->first();

            if (!$meeting) {
                return $this->error('Invalid check-in code', 404);
            }

            // Find or create visitor from meeting
            if ($meeting->visitor) {
                $visitor = $meeting->visitor;
            } else {
                $visitor = \App\Models\Visitor::firstOrCreate([
                    'tenant_id' => $tenant->id,
                    'email' => $meeting->visitor_email ?? null,
                ], [
                    'first_name' => $meeting->visitor_name ?? 'Guest',
                    'last_name' => '',
                    'phone' => $meeting->visitor_phone ?? null,
                ]);
            }

            $hostId = $meeting->host_id;
            $meetingId = $meeting->id;
            $purpose = $meeting->purpose ?? 'Meeting';
        } else {
            // Manual check-in
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'company' => 'nullable|string|max:255',
                'purpose' => 'nullable|string|max:255',
                'host_name' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'accepted_terms' => 'required|accepted',
                'accepted_gdpr' => 'required|accepted',
                'accepted_nda' => 'nullable|accepted',
            ]);

            // Check if visitor exists
            $visitor = null;
            if ($request->email) {
                $visitor = $tenant->visitors()->where('email', $request->email)->first();
            } elseif ($request->phone) {
                $visitor = $tenant->visitors()->where('phone', $request->phone)->first();
            }

            // Create or update visitor
            if (!$visitor) {
                $visitor = $tenant->visitors()->create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'company' => $request->company,
                ]);
            } else {
                // Update returning visitor
                $visitor->update([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone ?: $visitor->phone,
                    'company' => $request->company ?: $visitor->company,
                ]);
            }

            // Find host by name
            $hostId = null;
            if ($request->host_name) {
                $host = $tenant->users()->where('name', 'like', "%{$request->host_name}%")->first();
                $hostId = $host ? $host->id : null;
            }

            $meetingId = null;
            $purpose = $request->purpose;
        }

        // Check blacklist
        if ($visitor->is_blacklisted) {
            $accessPoint->logAccess($visitor, 'entry', 'denied', ['reason' => 'blacklisted']);
            return $this->error('Visitor is blacklisted', 403);
        }

        // Create visit
        $visit = $visitor->visits()->create([
            'tenant_id' => $tenant->id,
            'host_id' => $hostId,
            'meeting_id' => $meetingId,
            'purpose' => $purpose,
            'check_in_method' => 'kiosk',
            'check_in_at' => now(),
            'status' => 'checked_in',
            'notes' => $request->notes,
        ]);

        // Log access
        $accessPoint->logAccess($visitor, 'entry', 'granted', [
            'visit_id' => $visit->id,
            'visitor_name' => $visitor->full_name,
        ]);

        // Build response data
        $responseData = [
            'visitor' => new \App\Http\Resources\VisitorResource($visitor),
            'visit' => new \App\Http\Resources\VisitorVisitResource($visit),
            'badge_number' => $visit->badge_number,
            'message' => 'Check-in successful. Please proceed to your destination.',
        ];

        // Add host info if available
        if ($visit->host) {
            $responseData['host'] = [
                'name' => $visit->host->name,
                'department' => $visit->host->department,
                'phone' => $visit->host->phone,
            ];
        }

        // Add meeting info if available
        if ($visit->meeting) {
            $responseData['meeting'] = [
                'title' => $visit->meeting->title,
                'start_at' => $visit->meeting->start_at,
                'end_at' => $visit->meeting->end_at,
                'location' => $visit->meeting->location,
            ];
        }

        // Add instructions
        $responseData['instructions'] = 'Please wear your badge at all times. Return to the kiosk when leaving.';

        return $this->success($responseData, 'Check-in successful', 201);
    }

    public function checkOut(Request $request, Tenant $tenant, AccessPoint $accessPoint): JsonResponse
    {
        if (!$accessPoint->isKiosk() || !$accessPoint->is_active) {
            return $this->error('Invalid kiosk access point', 400);
        }

        $request->validate([
            'badge_number' => 'required_without:email|string',
            'email' => 'required_without:badge_number|email',
        ]);

        // Find active visit
        $visit = null;
        if ($request->badge_number) {
            $visit = VisitorVisit::where('badge_number', $request->badge_number)
                ->where('tenant_id', $tenant->id)
                ->whereNull('check_out_at')
                ->where('status', 'checked_in')
                ->first();
        } elseif ($request->email) {
            $visitor = Visitor::where('email', $request->email)
                ->where('tenant_id', $tenant->id)
                ->first();
            if ($visitor) {
                $visit = $visitor->visits()
                    ->whereNull('check_out_at')
                    ->where('status', 'checked_in')
                    ->first();
            }
        }

        if (!$visit) {
            return $this->error('No active visit found for the provided credentials', 404);
        }

        $visit->checkOut();

        // Log access
        $accessPoint->logAccess($visit->visitor, 'exit', 'granted', [
            'visit_id' => $visit->id,
            'duration' => $visit->getDurationInMinutes(),
        ]);

        return $this->success([
            'visit' => new \App\Http\Resources\VisitorVisitResource($visit->fresh()),
            'duration' => $visit->getDurationFormatted(),
            'message' => 'Thank you for visiting. Goodbye!',
        ], 'Check-out successful');
    }

    public function lookup(Request $request, Tenant $tenant): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $visitors = $tenant->visitors()
            ->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->query}%")
                    ->orWhere('last_name', 'like', "%{$request->query}%")
                    ->orWhere('email', 'like', "%{$request->query}%")
                    ->orWhere('phone', 'like', "%{$request->query}%")
                    ->orWhere('company', 'like', "%{$request->query}%");
            })
            ->where('is_blacklisted', false)
            ->limit(10)
            ->get();

        return $this->success(\App\Http\Resources\VisitorResource::collection($visitors));
    }

    public function preRegisteredVisitors(Tenant $tenant): JsonResponse
    {
        $todayVisitors = $tenant->visitors()
            ->whereHas('visits', function ($q) {
                $q->where('status', 'pre_registered')
                    ->whereDate('check_in_at', today());
            })
            ->with(['visits' => fn ($q) => $q->where('status', 'pre_registered')->whereDate('check_in_at', today())->with('host')])
            ->get();

        return $this->success(\App\Http\Resources\VisitorResource::collection($todayVisitors));
    }

    public function activeVisitors(Tenant $tenant): JsonResponse
    {
        $visits = VisitorVisit::where('tenant_id', $tenant->id)
            ->whereNull('check_out_at')
            ->where('status', 'checked_in')
            ->with(['visitor', 'host'])
            ->orderBy('check_in_at', 'desc')
            ->get();

        return $this->success(\App\Http\Resources\VisitorVisitResource::collection($visits));
    }

    public function hostDirectory(Tenant $tenant): JsonResponse
    {
        $hosts = $tenant->users()
            ->where('is_active', true)
            ->get(['id', 'name', 'email', 'department', 'job_title']);

        return $this->success(\App\Http\Resources\UserResource::collection($hosts));
    }
}