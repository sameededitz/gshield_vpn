<?php

namespace App\Livewire\Admin;

use App\Models\Ticket;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AllTickets extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 5;

    #[Url]
    public $status = '';

    #[Url]
    public $priority = '';

    public function resetFilters()
    {
       $this->reset('search', 'status', 'priority');
    }

    public function updatePriority($ticketId, $priority)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $ticket->priority = $priority;
        $ticket->save();

        $this->dispatch('sweetAlert', title: 'Updated!', message: 'Ticket priority has been updated.', type: 'success');
    }

    public function updateStatus($ticketId, $status)
    {
        $ticket = Ticket::findOrFail($ticketId);
        $ticket->status = $status;
        $ticket->save();

        $this->dispatch('sweetAlert', title: 'Updated!', message: 'Ticket status has been updated.', type: 'success');
    }

    public function render()
    {
        $tickets = Ticket::query()
            ->with('user:id,name')
            ->when($this->search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('subject', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->priority, function ($query) {
                $query->where('priority', $this->priority);
            })
            ->latest()
            ->paginate($this->perPage);

        /** @disregard @phpstan-ignore-line */
        return view('livewire.admin.all-tickets', compact('tickets'))
            ->extends('layouts.app')
            ->section('content');
    }
}
