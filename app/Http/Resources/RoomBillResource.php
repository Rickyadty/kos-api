<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomBillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_id' => $this->room_id,
            'periode' => $this->periode,
            'jumlah_tagihan' => (float) $this->jumlah_tagihan,
            'jatuh_tempo' => $this->jatuh_tempo?->format('Y-m-d'),
            'status' => $this->status,
            'room' => $this->whenLoaded('room', fn () => new RoomResource($this->room)),
            'payment' => $this->whenLoaded('payment', fn () => $this->payment ? new PaymentResource($this->payment) : null),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
