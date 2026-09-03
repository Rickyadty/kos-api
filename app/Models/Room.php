<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_kamar',
        'lantai',
        'harga_bulanan',
        'kapasitas',
        'keterangan',
        'gambar',
    ];

    protected function casts(): array
    {
        return [
            'harga_bulanan' => 'decimal:2',
        ];
    }

    /**
     * Room memiliki banyak rental.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Rental aktif di kamar ini.
     */
    public function activeRentals()
    {
        return $this->hasMany(Rental::class)->where('status', 'aktif');
    }

    /**
     * Room memiliki banyak room bill.
     */
    public function roomBills()
    {
        return $this->hasMany(RoomBill::class);
    }

    /**
     * Hitung jumlah penghuni aktif.
     */
    public function getJumlahPenghuniAktifAttribute(): int
    {
        return $this->activeRentals()->count();
    }

    /**
     * Status kamar dihitung secara dinamis.
     * 0 penghuni = kosong, 1 = terisi, 2 = penuh
     */
    public function getStatusKamarAttribute(): string
    {
        $penghuni = $this->getJumlahPenghuniAktifAttribute();

        return match (true) {
            $penghuni === 0 => 'kosong',
            $penghuni === 1 => 'terisi',
            default => 'penuh',
        };
    }
}
