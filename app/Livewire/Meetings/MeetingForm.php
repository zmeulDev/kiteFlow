<?php

namespace App\Livewire\Meetings;

use App\Models\Meeting;
use App\Models\MeetingRoom;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Component;

class MeetingForm extends Component
{
    public ?int $tenantId = null;
    public ?int $selectedCompanyId = null;
    public ?Meeting $meeting = null;
    
    public string $title = '';
    public string $description = '';
    public string $purpose = '';
    public ?int $meeting_room_id = null;
    public ?int $host_id = null;
    public string $start_at = '';
    public string $end_at = '';
    public string $meeting_type = 'in_person';
    public string $meeting_url = '';
    public array $visitor_ids = [];
    public array $user_ids = [];
    
    public bool $isEdit = false;
    public array $companies = [];
    public $hosts = [];

    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'selectedCompanyId' => ['required', 'integer', 'exists:tenants,id'],
            'meeting_room_id' => ['nullable', 'exists:meeting_rooms,id'],
            'host_id' => ['required', 'exists:users,id'],
            'start_at' => ['required', 'date', 'after:now'],
            'end_at' => ['required', 'date', 'after:start_at'],
            'meeting_type' => ['required', 'in:in_person,virtual,hybrid'],
            'meeting_url' => ['nullable', 'url', 'max:500'],
            'visitor_ids' => ['nullable', 'array'],
            'user_ids' => ['nullable', 'array'],
        ];
    }

    public function mount(?int $meetingId = null): void
    {
        $this->tenantId = auth()->user()?->getCurrentTenant()?->id;
        
        // Load companies (tenant + subtenants)
        $this->loadCompanies();
        
        if ($meetingId) {
            $this->meeting = Meeting::findOrFail($meetingId);
            $this->isEdit = true;
            $this->selectedCompanyId = $this->meeting->tenant_id;
            $this->fill([
                'title' => $this->meeting->title,
                'description' => $this->meeting->description,
                'purpose' => $this->meeting->purpose,
                'meeting_room_id' => $this->meeting->meeting_room_id,
                'host_id' => $this->meeting->host_id,
                'start_at' => $this->meeting->start_at->format('Y-m-d\TH:i'),
                'end_at' => $this->meeting->end_at->format('Y-m-d\TH:i'),
                'meeting_type' => $this->meeting->meeting_type,
                'meeting_url' => $this->meeting->meeting_url,
            ]);
            $this->loadHosts();
        } else {
            // Default to current tenant
            $this->selectedCompanyId = $this->tenantId;
            $this->host_id = auth()->id();
            $this->loadHosts();
        }
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

    public function loadHosts(): void
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
        $this->host_id = null;
    }

    public function getMeetingRoomsProperty()
    {
        if (!$this->selectedCompanyId || !$this->start_at || !$this->end_at) {
            return MeetingRoom::where('tenant_id', $this->selectedCompanyId ?? 0)
                ->where('is_active', true)
                ->get();
        }

        // Filter rooms by availability
        return MeetingRoom::where('tenant_id', $this->selectedCompanyId)
            ->where('is_active', true)
            ->get()
            ->filter(fn ($room) => $room->isAvailable(
                $this->start_at,
                $this->end_at,
                $this->meeting?->id
            ));
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'purpose' => $this->purpose,
            'meeting_room_id' => $this->meeting_room_id,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'meeting_type' => $this->meeting_type,
            'meeting_url' => $this->meeting_url,
            'tenant_id' => $this->selectedCompanyId,
            'host_id' => $this->host_id,
        ];

        if ($this->isEdit) {
            $this->meeting->update($data);
            session()->flash('message', 'Meeting updated successfully.');
        } else {
            $meeting = Meeting::create($data);
            session()->flash('message', 'Meeting created successfully.');
            $this->redirect(route('meetings.show', $meeting));
        }
    }

    public function render()
    {
        return view('livewire.meetings.meeting-form', [
            'meetingRooms' => $this->meetingRooms,
            'hosts' => $this->hosts,
        ])->layout('layouts.app');
    }
}