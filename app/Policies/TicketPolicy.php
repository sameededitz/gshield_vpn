<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ticket $ticket): Response
    {
        return $user->id === $ticket->user_id ? Response::allow() : Response::deny('You do not own this ticket.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ticket $ticket): Response
    {
        return $user->id === $ticket->user_id ? Response::allow() : Response::deny('You do not own this ticket.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ticket $ticket): Response
    {
        return $user->id === $ticket->user_id ? Response::allow() : Response::deny('You do not own this ticket.');
    }

    /**
     * Determine whether the user can close the ticket
     */
    public function close(User $user, Ticket $ticket): Response
    {
        return $user->id === $ticket->user_id && $ticket->status === 'open' ? Response::allow() : Response::deny('You do not own this ticket or the ticket is not open.');
    }
}
