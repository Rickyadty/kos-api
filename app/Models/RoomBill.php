<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomBill extends Model
{
    use HasFactory;

    protected $table = 'room_bills';

    protected $fillable = [
        'room_id',
        'periode',
        'jumlah_tagihan',
        'jatuh_tempo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_tagihan' => 'decimal:2',
            'jatuh_tempo' => 'date',
        ];
    }

    /**
     * Room bill milik room.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Room bill memiliki satu payment.
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
