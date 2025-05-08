<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image' => $this->image_url,
            'name' => $this->name,
            'platforms' => [
                'android' => (bool) $this->android,
                'ios' => (bool) $this->ios,
                'macos' => (bool) $this->macos,
                'windows' => (bool) $this->windows,
            ],
            'type' => $this->type,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'sub_servers' => $this->whenLoaded('subServers', fn() => SubServerResource::collection($this->subServers)),
        ];
    }
}
