<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Notification;
use Livewire\WithPagination;

class AllNotifications extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;

    public $notificationId;
    public $title;
    public $message;
    public $isEdit = false;

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ];
    }

    public function resetForm()
    {
        $this->reset([
            'notificationId',
            'title',
            'message',
        ]);
        $this->isEdit = false;
        $this->resetValidation();
    }

    public function editNotification($notificationId)
    {
        $this->resetForm();
        $this->isEdit = true;

        $notification = Notification::findOrFail($notificationId);
        $this->notificationId = $notification->id;
        $this->title = $notification->title;
        $this->message = $notification->message;
    }

    public function saveNotification()
    {
        $this->validate();

        if ($this->isEdit) {
            $notification = Notification::findOrFail($this->notificationId);
            $notification->update([
                'title' => $this->title,
                'message' => $this->message,
            ]);
            $message = 'Notification updated successfully.';
        } else {
            Notification::create([
                'title' => $this->title,
                'message' => $this->message,
            ]);
            $message = 'Notification created successfully.';
        }

        $this->dispatch('closeModel');
        $this->dispatch('sweetAlert', title: 'Success!', message: $message, type: 'success');
        $this->resetPage();
        $this->resetForm();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteNotification($notificationId)
    {
        $notification = Notification::findOrFail($notificationId);
        $notification->delete();

        $this->dispatch('sweetAlert', title: 'Success!', message: 'Notification deleted successfully.', type: 'success');
        $this->resetPage();
    }

    public function render()
    {
        $notifications = Notification::query()
            ->when($this->search, fn($query) => $query->where('title', 'like', '%' . $this->search . '%'))
            ->latest()
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-notifications', ['notifications' => $notifications])
            ->extends('layouts.app')
            ->section('content');
    }
}
