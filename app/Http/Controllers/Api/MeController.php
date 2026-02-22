<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use App\Models\Visitor;
use App\Models\VisitorVisit;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeController extends Controller
{
    /**
     * Get current user's meetings
     */
    public function myMeetings(Request $request): JsonResponse
    {
        $query = Meeting::where('host_id', Auth::id())
            ->with(['meetingRoom', 'attendees', 'notifications']);

        if ($request->has('filter')) {
            $filter = $request->filter;

            if ($filter === 'upcoming') {
                $query->where('start_at', '>', now())->where('status', 'scheduled');
            } elseif ($filter === 'today') {
                $query->whereDate('start_at', today());
            } elseif ($filter === 'past') {
                $query->where('end_at', '<', now());
            }
        }

        $meetings = $query->orderBy('start_at', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $meetings,
            'count' => $meetings->count(),
        ]);
    }

    /**
     * Get current user's visitors (those they are hosting)
     */
    public function myVisitors(): JsonResponse
    {
        $visits = VisitorVisit::where('host_id', Auth::id())
            ->where('status', 'checked_in')
            ->with(['visitor', 'meeting'])
            ->get();

        $visitorIds = $visits->pluck('visitor_id')->unique();
        $visitors = Visitor::whereIn('id', $visitorIds)->get();

        return response()->json([
            'success' => true,
            'data' => $visitors,
            'count' => $visitors->count(),
        ]);
    }

    /**
     * Get current user's analytics
     */
    public function analytics(): JsonResponse
    {
        $userId = Auth::id();

        $totalMeetings = Meeting::where('host_id', $userId)->count();
        $totalVisitors = VisitorVisit::where('host_id', $userId)->count();
        $meetingsThisMonth = Meeting::where('host_id', $userId)
            ->whereMonth('start_at', now()->month)
            ->whereYear('start_at', now()->year)
            ->count();
        $visitorsThisMonth = VisitorVisit::where('host_id', $userId)
            ->whereMonth('check_in_at', now()->month)
            ->whereYear('check_in_at', now()->year)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_meetings' => $totalMeetings,
                'total_visitors' => $totalVisitors,
                'meetings_this_month' => $meetingsThisMonth,
                'visitors_this_month' => $visitorsThisMonth,
            ],
        ]);
    }

    /**
     * Update user's notification preferences
     */
    public function updateNotificationPreferences(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'meeting_reminders' => 'boolean',
            'visitor_check_in_alerts' => 'boolean',
        ]);

        $preferences = $user->preferences ?? [];
        foreach ($validated as $key => $value) {
            $preferences['notifications'][$key] = $value;
        }

        $user->preferences = $preferences;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Preferences updated',
            'data' => $preferences,
        ]);
    }

    /**
     * Get user's profile
     */
    public function profile(): JsonResponse
    {
        $user = Auth::user()->load(['roles', 'tenants']);

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Update user's profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'job_title' => 'nullable|string|max:100',
            'timezone' => 'nullable|string|max:50',
            'avatar' => 'nullable|string',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated',
            'data' => $user->fresh(),
        ]);
    }
}