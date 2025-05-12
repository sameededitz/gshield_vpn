<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('ticket.{ticketId}', function ($user, $ticketId) {
    return $user->id === \App\Models\Ticket::find($ticketId)->user_id;
});
