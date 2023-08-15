<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinInstruktur extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_instruktur',
        'tanggal_izin',
        'tanggal_melakukan_izin',
        'tanggal_konfirmasi',
        'keterangan',
        'status',
    ];
}
