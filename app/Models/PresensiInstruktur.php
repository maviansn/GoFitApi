<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiInstruktur extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_instruktur',
        'tanggal_mengajar',
        'jam_mulai',
        'jam_selesai',
        'status'
    ];
}
