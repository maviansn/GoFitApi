<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    public $incrementing = false;
    
    protected $fillable = [
        'nama_member', 
        'alamat_member', 
        'telepon_member', 
        'tanggal_lahir',
        'masa_aktivasi', 
        'sisa_deposit', 
        'email', 
        'password',
        'status',
    ];
}
