<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingGym extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'id_member',
        'sesi',
        'status',
        'tanggal_booking',
        'tanggal_melakukan_booking',
        'waktu_presensi',
    ];
}
