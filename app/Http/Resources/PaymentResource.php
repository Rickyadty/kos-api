<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'room_bill_id' => $this->room_bill_id,
            'payer_tenant_id' => $this->payer_tenant_id,
            'jumlah_bayar' => (float) $this->jumlah_bayar,
            'tanggal_bayar' => $this->tanggal_bayar?->format('Y-m-d'),
            'diterima_oleh' => $this->diterima_oleh,
            'keterangan' => $this->keterangan,
            'room_bill' => $this->whenLoaded('roomBill', fn () => new RoomBillResource($this->roomBill)),
            'payer' => $this->whenLoaded('payer', fn () => new TenantResource($this->payer)),
            'receiver' => $this->whenLoaded('receiver', fn () => new UserResource($this->receiver)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
