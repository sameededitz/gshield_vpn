<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'subject'    => $this->subject,
            'status'     => $this->status,
            'priority'   => $this->priority,
            'created_at' => $this->created_at->toDateTimeString(),
            'messages'   => $this->whenLoaded('messages', function () {
                return TicketMessageResource::collection($this->messages);
            }),
        ];
    }
}
