<?php

namespace App\Livewire\Meetings;

use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class MeetingList extends Component
{
    use WithPagination;

    public ?int $tenantId = null;
    public string $search = '';
    public string $statusFilter = 'all';
    public string $dateFilter = '';

    // Calendar view state
    public string $currentMonth = '';
    public string $currentYear = '';
    public bool $showCalendar = true; // Toggle between list and calendar view

    // Modal state
    public bool $showModal = false;

    // Form fields
    public string $title = '';
    public string $description = '';
    public string $purpose = '';
    public ?int $meeting_room_id = null;
    public ?int $host_id = null;
    public string $meeting_date = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $meeting_type = 'in_person';
    public string $meeting_url = '';

    // Dropdowns data
    public array $companies = [];
    public ?int $selectedCompanyId = null;
    public $hosts = [];
    public $availableMeetingRooms = [];

    protected $queryString = ['search', 'statusFilter', 'dateFilter'];

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'selectedCompanyId' => ['required', 'integer', 'exists:tenants,id'],
            'host_id' => ['required', 'exists:users,id'],
            'meeting_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'meeting_type' => ['required', 'in:in_person,virtual,hybrid'],
            'meeting_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function mount(?int $tenantId = null): void
    {
        $tenantId = $tenantId
            ?? request()->attributes->get('tenant_id')
            ?? auth()->user()?->getCurrentTenant()?->id;

        // Verify user has access to this tenant
        if ($tenantId && auth()->check()) {
            $user = auth()->user();
            if (!$user->belongsToOneOfTenants([$tenantId])) {
                abort(403, 'You do not have access to this tenant data.');
            }
        }

        $this->tenantId = $tenantId;
        $this->selectedCompanyId = $tenantId;
        $this->host_id = auth()->id();
        $this->loadCompanies();
        $this->loadHosts();

        // Initialize calendar to current month
        $now = now();
        $this->currentMonth = $now->format('m');
        $this->currentYear = $now->format('Y');
    }

    // Calendar methods
    public function getCalendarDataProperty(): array
    {
        $year = (int) $this->currentYear;
        $month = (int) $this->currentMonth;
        $firstDay = Carbon::create($year, $month, 1);
        $lastDay = $firstDay->copy()->endOfMonth();
        $daysInMonth = $lastDay->day;
        $startingDayOfWeek = $firstDay->dayOfWeekIso - 1; // 0 = Monday, 6 = Sunday

        // Get meetings for this month
        $meetingsQuery = Meeting::where('tenant_id', $this->tenantId)
            ->whereYear('start_at', $year)
            ->whereMonth('start_at', $month);

        if ($this->statusFilter !== 'all') {
            $meetingsQuery->where('status', $this->statusFilter);
        }

        $meetings = $meetingsQuery
            ->with(['meetingRoom', 'host'])
            ->orderBy('start_at')
            ->get();

        // Group meetings by day
        $meetingsByDay = [];
        foreach ($meetings as $meeting) {
            $day = $meeting->start_at->day;
            $meetingsByDay[$day][] = $meeting;
        }

        return [
            'year' => $year,
            'month' => $month,
            'monthName' => $firstDay->format('F'),
            'daysInMonth' => $daysInMonth,
            'startingDayOfWeek' => $startingDayOfWeek,
            'firstDay' => $firstDay,
            'lastDay' => $lastDay,
            'meetingsByDay' => $meetingsByDay,
        ];
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->subMonth();
        $this->currentYear = $date->format('Y');
        $this->currentMonth = $date->format('m');
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->currentYear, $this->currentMonth, 1)->addMonth();
        $this->currentYear = $date->format('Y');
        $this->currentMonth = $date->format('m');
    }

    public function goToToday(): void
    {
        $now = now();
        $this->currentYear = $now->format('Y');
        $this->currentMonth = $now->format('m');
    }

    public function getCalendarMeetingsProperty(): array
    {
        $year = (int) $this->currentYear;
        $month = (int) $this->currentMonth;

        $query = Meeting::where('tenant_id', $this->tenantId)
            ->whereYear('start_at', $year)
            ->whereMonth('start_at', $month)
            ->with(['meetingRoom', 'host'])
            ->orderBy('start_at');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->get()->toArray();
    }

    public function getMeetingsProperty()
    {
        if (!$this->tenantId) {
            return collect();
        }

        return Meeting::with(['meetingRoom', 'host', 'attendees'])
            ->where('tenant_id', $this->tenantId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== 'all', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->dateFilter, fn ($q) => $q->whereDate('start_at', $this->dateFilter))
            ->orderBy('start_at', 'asc')
            ->paginate(15);
    }

    public function getMeetingRoomsProperty()
    {
        if (!$this->tenantId) {
            return collect();
        }

        return MeetingRoom::where('tenant_id', $this->tenantId)
            ->where('is_active', true)
            ->get();
    }

    public function openModal(): void
    {
        $this->resetForm();
        // Set default meeting date to tomorrow
        $tomorrow = now()->addDay()->format('Y-m-d');
        $this->meeting_date = $tomorrow;
        $this->start_time = '09:00';
        $this->end_time = '10:00';
        $this->showModal = true;
        $this->updateAvailableRooms();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    protected function loadCompanies(): void
    {
        if (!$this->tenantId) {
            return;
        }

        $tenant = Tenant::find($this->tenantId);
        if ($tenant) {
            $this->companies = collect([$tenant])
                ->merge($tenant->children)
                ->map(fn ($t) => ['id' => $t->id, 'name' => $t->name])
                ->toArray();
        }
    }

    protected function loadHosts(): void
    {
        if (!$this->selectedCompanyId) {
            $this->hosts = [];
            return;
        }

        $this->hosts = User::whereHas('tenants', fn ($q) => $q->where('tenant_id', $this->selectedCompanyId))
            ->where('is_active', true)
            ->get(['id', 'name', 'email']);
    }

    public function updatedSelectedCompanyId(): void
    {
        $this->loadHosts();
        $this->meeting_room_id = null;
        $this->host_id = auth()->id();
        $this->updateAvailableRooms();
    }

    public function updateAvailableRooms(): void
    {
        if (!$this->selectedCompanyId || !$this->meeting_date || !$this->start_time || !$this->end_time) {
            // Show all rooms if dates not set yet
            $this->availableMeetingRooms = MeetingRoom::where('tenant_id', $this->selectedCompanyId)
                ->where('is_active', true)
                ->get();
            return;
        }

        // Combine date and time for availability check
        $startAt = $this->meeting_date . ' ' . $this->start_time;
        $endAt = $this->meeting_date . ' ' . $this->end_time;

        $this->availableMeetingRooms = MeetingRoom::where('tenant_id', $this->selectedCompanyId)
            ->where('is_active', true)
            ->get()
            ->filter(fn ($room) => $room->isAvailable($startAt, $endAt));
    }

    public function updatedMeetingDate(): void
    {
        $this->updateAvailableRooms();
    }

    public function updatedStartTime(): void
    {
        $this->updateAvailableRooms();
    }

    public function updatedEndTime(): void
    {
        $this->updateAvailableRooms();
    }

    public function save(): void
    {
        $this->validate();

        $startAt = $this->meeting_date . ' ' . $this->start_time;
        $endAt = $this->meeting_date . ' ' . $this->end_time;

        Meeting::create([
            'title' => $this->title,
            'description' => $this->description,
            'purpose' => $this->purpose,
            'meeting_room_id' => $this->meeting_room_id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'meeting_type' => $this->meeting_type,
            'meeting_url' => $this->meeting_url,
            'tenant_id' => $this->selectedCompanyId,
            'host_id' => $this->host_id,
            'status' => 'scheduled',
        ]);

        $this->closeModal();
        session()->flash('message', 'Meeting scheduled successfully.');
    }

    protected function resetForm(): void
    {
        $this->reset([
            'title', 'description', 'purpose', 'meeting_room_id', 'host_id',
            'meeting_date', 'start_time', 'end_time', 'meeting_type', 'meeting_url'
        ]);
        $this->title = '';
        $this->description = '';
        $this->purpose = '';
        $this->meeting_room_id = null;
        $this->host_id = auth()->id();
        $this->meeting_date = '';
        $this->start_time = '';
        $this->end_time = '';
        $this->meeting_type = 'in_person';
        $this->meeting_url = '';
        $this->selectedCompanyId = $this->tenantId;
        $this->loadHosts();
    }

    public function cancelMeeting(int $meetingId): void
    {
        $meeting = Meeting::findOrFail($meetingId);
        $meeting->cancel('Cancelled by user');
        session()->flash('message', 'Meeting cancelled successfully.');
    }

    public function render()
    {
        return view('livewire.meetings.meeting-list', [
            'meetings' => $this->meetings,
            'meetingRooms' => $this->meetingRooms,
            'companies' => $this->companies,
            'hosts' => $this->hosts,
            'availableMeetingRooms' => $this->availableMeetingRooms,
            'calendarData' => $this->calendarData,
            'calendarMeetings' => $this->calendarMeetings,
        ]);
    }
}