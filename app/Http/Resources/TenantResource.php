<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'no_hp' => $this->no_hp,
            'alamat' => $this->alamat,
            'no_identitas' => $this->no_identitas,
            'pekerjaan' => $this->pekerjaan,
            'kontak_darurat' => $this->kontak_darurat,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
