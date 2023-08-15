<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalUmum extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_kelas',
        'id_instruktur',
        'hari',
        'waktu'
    ];
}
