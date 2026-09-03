<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'room_id' => $this->room_id,
            'tanggal_masuk' => $this->tanggal_masuk?->format('Y-m-d'),
            'tanggal_keluar' => $this->tanggal_keluar?->format('Y-m-d'),
            'status' => $this->status,
            'tenant' => $this->whenLoaded('tenant', fn () => new TenantResource($this->tenant)),
            'room' => $this->whenLoaded('room', fn () => new RoomResource($this->room)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
