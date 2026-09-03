<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_bill_id',
        'payer_tenant_id',
        'jumlah_bayar',
        'tanggal_bayar',
        'diterima_oleh',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_bayar' => 'decimal:2',
            'tanggal_bayar' => 'date',
        ];
    }

    /**
     * Payment milik room bill.
     */
    public function roomBill()
    {
        return $this->belongsTo(RoomBill::class);
    }

    /**
     * Tenant yang membayar.
     */
    public function payer()
    {
        return $this->belongsTo(Tenant::class, 'payer_tenant_id');
    }

    /**
     * User yang menerima pembayaran.
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }
}
