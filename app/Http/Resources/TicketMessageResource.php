<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketMessageResource extends JsonResource
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
            'user_id'    => $this->user_id,
            'is_admin'   => $this->is_admin,
            'message'    => $this->message,
            'created_at' => $this->created_at->toDateTimeString(),
            'attachments' => $this->getMedia('attachments')->map(function ($media) {
                return [
                    'url'      => $media->getFullUrl(),
                    'size'     => $media->size,
                    'mime_type' => $media->mime_type,
                ];
            })->toArray(),
        ];
    }
}
