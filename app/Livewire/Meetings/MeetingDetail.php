<?php

namespace App\Livewire\Meetings;

use App\Models\Meeting;
use Livewire\Component;

class MeetingDetail extends Component
{
    public Meeting $meeting;
    public bool $showCancelModal = false;

    public function mount(Meeting $meeting): void
    {
        $this->meeting = $meeting->load(['meetingRoom', 'host', 'attendees.attendee', 'visitorVisits.visitor']);
    }

    public function openCancelModal(): void
    {
        $this->showCancelModal = true;
    }

    public function confirmCancel(): void
    {
        $this->meeting->cancel('Cancelled by user');
        $this->showCancelModal = false;
        session()->flash('message', 'Meeting cancelled successfully.');
        $this->meeting->refresh();
    }

    public function render()
    {
        return view('livewire.meetings.meeting-detail', [
            'meeting' => $this->meeting,
        ])->layout('layouts.app');
    }
}