<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\VisitorRequest;
use App\Http\Resources\VisitorResource;
use App\Models\Tenant;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitorController extends BaseApiController
{
    public function index(Request $request, Tenant $tenant): JsonResponse
    {
        $query = $tenant->visitors()->with(['visits' => fn ($q) => $q->latest('check_in_at')->limit(1)]);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            if ($request->status === 'blacklisted') {
                $query->where('is_blacklisted', true);
            } elseif ($request->status === 'active') {
                $query->where('is_blacklisted', false);
            }
        }

        if ($request->has('company')) {
            $query->where('company', $request->company);
        }

        $visitors = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? $this->perPage);

        return $this->paginatedResponse($visitors, VisitorResource::class);
    }

    public function store(VisitorRequest $request, Tenant $tenant): JsonResponse
    {
        $visitor = $tenant->visitors()->create($request->validated());

        return $this->success(new VisitorResource($visitor), 'Visitor created successfully', 201);
    }

    public function show(Tenant $tenant, Visitor $visitor): JsonResponse
    {
        if ($visitor->tenant_id !== $tenant->id) {
            return $this->notFound('Visitor');
        }

        return $this->success(new VisitorResource($visitor->load(['visits.host', 'visits.meeting', 'documents'])));
    }

    public function update(VisitorRequest $request, Tenant $tenant, Visitor $visitor): JsonResponse
    {
        if ($visitor->tenant_id !== $tenant->id) {
            return $this->notFound('Visitor');
        }

        $visitor->update($request->validated());

        return $this->success(new VisitorResource($visitor->fresh()), 'Visitor updated successfully');
    }

    public function destroy(Tenant $tenant, Visitor $visitor): JsonResponse
    {
        if ($visitor->tenant_id !== $tenant->id) {
            return $this->notFound('Visitor');
        }

        // Check if user has permission to delete visitors
        if (!auth()->user()->can('delete visitors')) {
            return $this->error('You do not have permission to delete visitors', 403);
        }

        $visitor->delete();

        return $this->success(null, 'Visitor deleted successfully');
    }

    public function blacklist(Request $request, Tenant $tenant, Visitor $visitor): JsonResponse
    {
        if ($visitor->tenant_id !== $tenant->id) {
            return $this->notFound('Visitor');
        }

        // Check if user has permission to blacklist visitors
        if (!auth()->user()->can('blacklist visitors')) {
            return $this->error('You do not have permission to blacklist visitors', 403);
        }

        $request->validate(['reason' => 'required|string|max:500']);

        $visitor->blacklist($request->reason);

        return $this->success(new VisitorResource($visitor), 'Visitor blacklisted successfully');
    }

    public function unblacklist(Tenant $tenant, Visitor $visitor): JsonResponse
    {
        if ($visitor->tenant_id !== $tenant->id) {
            return $this->notFound('Visitor');
        }

        // Check if user has permission to blacklist visitors
        if (!auth()->user()->can('blacklist visitors')) {
            return $this->error('You do not have permission to unblacklist visitors', 403);
        }

        $visitor->unblacklist();

        return $this->success(new VisitorResource($visitor), 'Visitor removed from blacklist successfully');
    }

    public function checkIn(Request $request, Tenant $tenant): JsonResponse
    {
        $request->validate([
            'visitor_id' => 'required_without:visitor_data|exists:visitors,id',
            'visitor_data' => 'required_without:visitor_id|array',
            'host_id' => 'nullable|exists:users,id',
            'meeting_id' => 'nullable|exists:meetings,id',
            'purpose' => 'nullable|string|max:255',
            'check_in_method' => 'in:reception,kiosk,qr,app',
            'notes' => 'nullable|string',
        ]);

        $visitor = $request->has('visitor_id')
            ? Visitor::findOrFail($request->visitor_id)
            : $tenant->visitors()->create($request->visitor_data);

        if ($visitor->is_blacklisted) {
            return $this->error('This visitor is blacklisted and cannot check in', 403);
        }

        $visit = $visitor->visits()->create([
            'tenant_id' => $tenant->id,
            'host_id' => $request->host_id,
            'meeting_id' => $request->meeting_id,
            'purpose' => $request->purpose,
            'check_in_method' => $request->check_in_method ?? 'reception',
            'check_in_at' => now(),
            'status' => 'checked_in',
            'notes' => $request->notes,
        ]);

        return $this->success([
            'visit' => new \App\Http\Resources\VisitorVisitResource($visit->load('visitor', 'host')),
            'badge_number' => $visit->badge_number,
        ], 'Visitor checked in successfully', 201);
    }

    public function checkOut(Tenant $tenant, VisitorVisit $visit): JsonResponse
    {
        if ($visit->tenant_id !== $tenant->id) {
            return $this->notFound('Visit');
        }

        if ($visit->check_out_at !== null) {
            return $this->error('Visitor has already been checked out', 400);
        }

        $visit->checkOut(auth()->id());

        return $this->success(new \App\Http\Resources\VisitorVisitResource($visit->fresh()), 'Visitor checked out successfully');
    }

    public function visits(Request $request, Tenant $tenant, Visitor $visitor): JsonResponse
    {
        if ($visitor->tenant_id !== $tenant->id) {
            return $this->notFound('Visitor');
        }

        $visits = $visitor->visits()
            ->with('host', 'meeting')
            ->orderBy('check_in_at', 'desc')
            ->paginate($request->per_page ?? $this->perPage);

        return $this->paginatedResponse($visits, \App\Http\Resources\VisitorVisitResource::class);
    }

    public function currentVisitors(Tenant $tenant): JsonResponse
    {
        $visits = $tenant->visitors()
            ->whereHas('visits', fn ($q) => $q->whereNull('check_out_at')->where('status', 'checked_in'))
            ->with(['visits' => fn ($q) => $q->whereNull('check_out_at')->where('status', 'checked_in')->with('host')])
            ->get();

        return $this->success(VisitorResource::collection($visits));
    }
}