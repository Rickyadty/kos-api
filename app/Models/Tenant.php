<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'no_hp',
        'alamat',
        'no_identitas',
        'pekerjaan',
        'kontak_darurat',
        'status',
    ];

    /**
     * Tenant memiliki banyak rental.
     */
    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    /**
     * Tenant memiliki banyak payment.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class, 'payer_tenant_id');
    }

    /**
     * Rental aktif tenant.
     */
    public function activeRental()
    {
        return $this->hasOne(Rental::class)->where('status', 'aktif');
    }
}
