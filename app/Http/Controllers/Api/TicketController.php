<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\TicketResource;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $tickets = $user->tickets()
            ->with(['messages' => function ($query) {
                $query->oldest()->limit(1); // get only the first (oldest) message
            }])
            ->latest()
            ->get();

        return response()->json([
            'tickets' => TicketResource::collection($tickets),
        ], 200);
    }

    public function show($id)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $ticket = $user->tickets()->with('messages.media')->find($id);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        Gate::authorize('view', $ticket);

        return response()->json([
            'ticket' => new TicketResource($ticket->load('messages.media')),
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all()
            ], 422);
        }

        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $ticket = $user->tickets()->create([
            'subject' => $request->subject,
            'status' => 'open',
        ]);

        $ticket->messages()->create([
            'message' => $request->message,
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Ticket created successfully!',
            'ticket' => new TicketResource($ticket->load('messages.media')),
        ], 201);
    }

    public function reply(Request $request, $ticketId)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'image|mimes:jpg,jpeg,png|max:20420', // 20MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all()
            ], 422);
        }

        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $ticket = $user->tickets()->find($ticketId);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        $message = $ticket->messages()->create([
            'message' => $request->message,
            'user_id' => $user->id,
        ]);

        if ($request->has('attachments')) {
            $attachments = $request->attachments;
            $uploadErrors = [];

            foreach ($attachments as $attachment) {
                // Check if the file is valid
                if (!$attachment->isValid()) {
                    $uploadErrors[] = 'File ' . $attachment->getClientOriginalName() . ' is not valid.';
                    continue;
                }

                // Attempt to add the media to the collection
                try {
                    $message->addMedia($attachment->getRealPath())
                        ->usingFileName(time() . '_ticket-' . $ticket->id . '_message-' . $message->id . '_file-' . $attachment->getClientOriginalName())
                        ->toMediaCollection('attachments');
                } catch (\Exception $e) {
                    $uploadErrors[] = 'Error uploading file ' . $attachment->getClientOriginalName() . ': ' . $e->getMessage();
                }
            }

            // If there were errors, return them as part of the response
            if (count($uploadErrors) > 0) {
                return response()->json(['message' => 'Some files failed to upload', 'errors' => $uploadErrors], 400);
            }
        }

        return response()->json([
            'message' => 'Message sent successfully!',
            'ticket' => new TicketResource($ticket->load('messages.media')),
        ], 201);
    }

    public function close($ticketId)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $ticket = $user->tickets()->find($ticketId);
        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        Gate::authorize('close', $ticket);

        $ticket->update(['status' => 'closed']);

        return response()->json(['message' => 'Ticket closed successfully'], 200);
    }

    public function destroy($ticketId)
    {
        /** @var \App\Models\User $user **/
        $user = Auth::user();
        $ticket = $user->tickets()
            ->with('messages.media')
            ->find($ticketId);

        if (!$ticket) {
            return response()->json(['message' => 'Ticket not found'], 404);
        }

        Gate::authorize('delete', $ticket);

        // Delete all messages and their media
        foreach ($ticket->messages as $message) {
            $message->clearMediaCollection('attachments');
        }

        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully'], 200);
    }
}
