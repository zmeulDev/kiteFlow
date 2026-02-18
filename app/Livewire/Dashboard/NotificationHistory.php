<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class NotificationHistory extends Component
{
    use WithPagination;

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        $this->dispatch('notify', type: 'success', message: 'Notification marked as read.');
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->dispatch('notify', type: 'success', message: 'All notifications marked as read.');
    }

    public function deleteNotification($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        $this->dispatch('notify', type: 'success', message: 'Notification removed.');
    }

    public function render()
    {
        $notifications = Auth::user()->notifications()->paginate(15);

        return view('livewire.dashboard.notification-history', [
            'notifications' => $notifications
        ])->layout('components.layouts.app', ['header' => 'Notification Center']);
    }
}
