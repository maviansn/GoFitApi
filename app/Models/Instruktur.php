<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instruktur extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nama_instruktur',
        'alamat_instruktur',
        'telepon_instruktur',
        'email',
        'password',
        'total_keterlambatan',
    ];
}
