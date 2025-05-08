<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\VpsServerResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SubServerResource extends JsonResource
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
            'server_id' => $this->server_id,
            'name' => $this->name,
            'status' => $this->status,
            'vps_server' => $this->whenLoaded('vpsServer', fn() => new VpsServerResource($this->vpsServer)),
        ];
    }
}
