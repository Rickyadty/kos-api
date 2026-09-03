<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Hitung penghuni aktif - gunakan loaded relation jika ada, otherwise query
        $penghuniAktif = $this->relationLoaded('activeRentals')
            ? $this->activeRentals->count()
            : $this->activeRentals()->count();

        $statusKamar = match (true) {
            $penghuniAktif === 0 => 'kosong',
            $penghuniAktif === 1 => 'terisi',
            default => 'penuh',
        };

        return [
            'id' => $this->id,
            'nomor_kamar' => $this->nomor_kamar,
            'lantai' => $this->lantai,
            'harga_bulanan' => (float) $this->harga_bulanan,
            'kapasitas' => $this->kapasitas,
            'keterangan' => $this->keterangan,
            'gambar' => $this->gambar,
            'gambar_url' => $this->gambar ? asset('storage/' . $this->gambar) : null,
            'jumlah_penghuni_aktif' => $penghuniAktif,
            'status_kamar' => $statusKamar,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
