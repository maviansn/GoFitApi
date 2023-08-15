<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class JadwalHarian extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_instruktur',
        'id_jadwal_umum',
        'tanggal_kelas',
        'keterangan'
    ]; 
}