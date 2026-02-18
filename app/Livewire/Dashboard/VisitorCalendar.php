<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Visit;
use App\Models\Visitor;
use App\Models\Tenant;
use App\Models\Booking;
use App\Models\MeetingRoom;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class VisitorCalendar extends Component
{
    public $currentMonth;
    public $currentYear;
    public $daysInMonth;
    public $firstDayOfMonth;

    // Add Modal state
    public $showAddModal = false;
    public $first_name, $last_name, $email, $purpose, $scheduled_at, $meeting_room_id, $target_tenant_id;
    public $visitor_count = 1;
    public $booking_duration = 60;

    // Details Modal state
    public $showDetailsModal = false;
    public $selectedVisit = null;

    public function mount()
    {
        $this->currentMonth = now()->month;
        $this->currentYear = now()->year;
        $this->calculateCalendar();
    }

    public function calculateCalendar()
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1);
        $this->daysInMonth = $date->daysInMonth;
        $this->firstDayOfMonth = $date->dayOfWeek; 
    }

    public function previousMonth()
    {
        if ($this->currentMonth == 1) {
            $this->currentMonth = 12;
            $this->currentYear--;
        } else {
            $this->currentMonth--;
        }
        $this->calculateCalendar();
    }

    public function nextMonth()
    {
        if ($this->currentMonth == 12) {
            $this->currentMonth = 1;
            $this->currentYear++;
        } else {
            $this->currentMonth++;
        }
        $this->calculateCalendar();
    }

    public function openAddModal($day = null)
    {
        $this->reset(['first_name', 'last_name', 'email', 'purpose', 'meeting_room_id', 'target_tenant_id', 'visitor_count']);
        if ($day) {
            $this->scheduled_at = Carbon::create($this->currentYear, $this->currentMonth, $day)->setTime(9, 0)->format('Y-m-d\TH:i');
        } else {
            $this->scheduled_at = now()->addHour()->format('Y-m-d\TH:i');
        }
        $this->showDetailsModal = false;
        $this->showAddModal = true;
    }

    public function getAvailableRoomsProperty()
    {
        if (!$this->scheduled_at) return collect();

        $start = Carbon::parse($this->scheduled_at);
        $end = $start->copy()->addMinutes($this->booking_duration);

        return MeetingRoom::with('location')
            ->where('capacity', '>=', $this->visitor_count)
            ->where('is_active', true)
            ->whereDoesntHave('bookings', function ($query) use ($start, $end) {
                $query->where(function ($q) use ($start, $end) {
                    $q->whereBetween('starts_at', [$start, $end])
                      ->orWhereBetween('ends_at', [$start, $end])
                      ->orWhere(function ($sq) use ($start, $end) {
                          $sq->where('starts_at', '<=', $start)
                             ->where('ends_at', '>=', $end);
                      });
                });
            })
            ->get();
    }

    public function deleteVisit()
    {
        if ($this->selectedVisit && $this->selectedVisit->tenant_id === auth()->user()->tenant_id) {
            if ($this->selectedVisit->booking) {
                $this->selectedVisit->booking->delete();
            }
            $this->selectedVisit->delete();
            $this->showDetailsModal = false;
            $this->selectedVisit = null;
            $this->dispatch('notify', type: 'success', message: 'Visit deleted successfully.');
        }
    }

    public function updateVisit()
    {
        $this->validate([
            'scheduled_at' => 'required|date|after:now',
        ]);

        if ($this->selectedVisit && $this->selectedVisit->tenant_id === auth()->user()->tenant_id) {
            $newDate = Carbon::parse($this->scheduled_at);
            
            $this->selectedVisit->update([
                'scheduled_at' => $newDate,
            ]);

            if ($this->selectedVisit->booking) {
                $this->selectedVisit->booking->update([
                    'starts_at' => $newDate,
                    'ends_at' => $newDate->copy()->addMinutes($this->booking_duration),
                ]);
            }
            
            $this->showDetailsModal = false;
            $this->dispatch('notify', type: 'success', message: 'Visit rescheduled successfully.');
        }
    }

    public function showVisit($visitId)
    {
        // Scope handles filtering
        $this->selectedVisit = Visit::with(['visitor', 'host', 'location', 'booking.room', 'tenant'])
            ->findOrFail($visitId);
            
        $this->scheduled_at = $this->selectedVisit->scheduled_at->format('Y-m-d\TH:i'); // For rescheduling
        $this->showAddModal = false;
        $this->showDetailsModal = true;
    }

    public function submit()
    {
        $this->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'purpose' => 'required|string',
            'scheduled_at' => 'required',
            'target_tenant_id' => 'nullable|exists:tenants,id',
            'visitor_count' => 'required|integer|min:1',
        ]);

        $tenant = auth()->user()->tenant;
        $targetTenantId = $this->target_tenant_id ?: $tenant->id;

        $visitor = Visitor::updateOrCreate(
            ['email' => $this->email],
            [
                'tenant_id' => $targetTenantId,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
            ]
        );

        $visit = Visit::create([
            'tenant_id' => $targetTenantId,
            'visitor_id' => $visitor->id,
            'user_id' => Auth::id(),
            'purpose' => $this->purpose,
            'scheduled_at' => Carbon::parse($this->scheduled_at),
            'check_in_token' => Str::random(32),
        ]);

        if ($this->meeting_room_id) {
            Booking::create([
                'tenant_id' => $tenant->id,
                'meeting_room_id' => $this->meeting_room_id,
                'visit_id' => $visit->id,
                'starts_at' => $visit->scheduled_at,
                'ends_at' => $visit->scheduled_at->copy()->addHour(),
                'notes' => $this->purpose,
            ]);
        }

        $this->reset(['first_name', 'last_name', 'email', 'purpose', 'scheduled_at', 'meeting_room_id', 'target_tenant_id', 'visitor_count', 'showAddModal']);
        $this->dispatch('notify', type: 'success', message: 'Visit scheduled successfully.');
    }

    public function render()
    {
        // Scope handles filtering
        $visits = Visit::with(['visitor', 'tenant'])
            ->whereYear('scheduled_at', $this->currentYear)
            ->whereMonth('scheduled_at', $this->currentMonth)
            ->get()
            ->groupBy(function($visit) {
                return $visit->scheduled_at->day;
            });

        return view('livewire.dashboard.visitor-calendar', [
            'monthName' => Carbon::create($this->currentYear, $this->currentMonth, 1)->format('F'),
            'visits' => $visits,
            'availableRooms' => $this->availableRooms,
            'subtenants' => auth()->user()->tenant->is_hub ? auth()->user()->tenant->children : collect()
        ]);
    }
}
