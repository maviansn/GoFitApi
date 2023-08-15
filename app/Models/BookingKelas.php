<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingKelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_member',
        'id_jadwal_harian',
        'jenis_pembayaran',
        'status',
        'tanggal_booking',
        'tanggal_melakukan_booking',
        'waktu_presensi',
    ];
}
