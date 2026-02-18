<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Visitor;
use App\Models\Visit;
use App\Models\Tenant;
use App\Models\Booking;
use App\Models\MeetingRoom;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PreRegisterGuest extends Component
{
    public $showModal = false;
    
    public $first_name, $last_name, $email, $phone, $purpose, $expected_at;
    public $meeting_room_id, $target_tenant_id;
    public $visitor_count = 1;
    public $booking_duration = 60;

    protected $rules = [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email',
        'purpose' => 'required|string',
        'expected_at' => 'required|date|after:now',
        'meeting_room_id' => 'nullable|exists:meeting_rooms,id',
        'target_tenant_id' => 'nullable|exists:tenants,id',
        'visitor_count' => 'required|integer|min:1',
    ];

    #[On('openInviteModal')]
    public function open()
    {
        $this->reset(['first_name', 'last_name', 'email', 'phone', 'purpose', 'expected_at', 'meeting_room_id', 'target_tenant_id', 'visitor_count']);
        $this->showModal = true;
    }

    public function getAvailableRoomsProperty()
    {
        if (!$this->expected_at) return collect();

        $start = \Carbon\Carbon::parse($this->expected_at);
        $end = $start->copy()->addMinutes($this->booking_duration);

        return MeetingRoom::with('location')
            ->where('tenant_id', auth()->user()->tenant_id)
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

    public function submit()
    {
        $this->validate();

        $tenant = auth()->user()->tenant;
        $targetTenantId = $this->target_tenant_id ?: $tenant->id;
        
        $visitor = Visitor::updateOrCreate(
            ['email' => $this->email],
            [
                'tenant_id' => $targetTenantId,
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'phone' => $this->phone,
            ]
        );

        $visit = Visit::create([
            'tenant_id' => $targetTenantId,
            'check_in_token' => Str::random(32),
            'visitor_id' => $visitor->id,
            'user_id' => Auth::id(),
            'purpose' => $this->purpose,
            'scheduled_at' => \Carbon\Carbon::parse($this->expected_at),
            'checked_in_at' => null,
        ]);

        if ($this->meeting_room_id) {
            $startsAt = \Carbon\Carbon::parse($this->expected_at);
            Booking::create([
                'tenant_id' => $tenant->id,
                'meeting_room_id' => $this->meeting_room_id,
                'visit_id' => $visit->id,
                'starts_at' => $startsAt,
                'ends_at' => $startsAt->copy()->addMinutes($this->booking_duration),
                'notes' => $this->purpose,
            ]);
        }

        $this->showModal = false;
        $this->dispatch('visitor-pre-registered');
        $this->dispatch('notify', type: 'success', message: 'Guest successfully invited!');
    }

    public function render()
    {
        $tenant = auth()->user()->tenant;
        
        return view('livewire.dashboard.pre-register-guest', [
            'subtenants' => $tenant ? $tenant->children : collect(),
            'availableRooms' => $this->availableRooms,
        ]);
    }
}
