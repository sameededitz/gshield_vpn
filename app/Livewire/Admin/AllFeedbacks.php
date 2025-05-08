<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\UserFeedback;
use Livewire\WithPagination;

class AllFeedbacks extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;

    public $feedbackId;
    public $subject;
    public $email;
    public $message;

    public function viewFeedback($feedbackId)
    {
        $this->feedbackId = $feedbackId;

        $feedback = UserFeedback::findOrFail($feedbackId);
        $this->subject = $feedback->subject;
        $this->email = $feedback->email;
        $this->message = $feedback->message;
    }

    public function closeModel()
    {
        $this->reset([
            'feedbackId',
            'subject',
            'email',
            'message',
        ]);

        $this->dispatch('closeModel');
    }

    public function deleteFeedback($feedbackId)
    {
        $feedback = UserFeedback::findOrFail($feedbackId);
        $feedback->delete();

        $this->dispatch('sweetAlert', title: 'Success!', message: 'Feedback deleted successfully.', type: 'success');
        $this->resetPage();
    }

    public function render()
    {
        $feedbacks = UserFeedback::query()
            ->when($this->search, fn($query) => $query->where(function ($query) {
            $query->where('subject', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            }))
            ->latest()
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-feedbacks', ['feedbacks' => $feedbacks])
            ->extends('layouts.app')
            ->section('content');
    }
}
