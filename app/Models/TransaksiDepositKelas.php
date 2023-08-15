<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransaksiDepositKelas extends Model
{
    public $incrementing = false;
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable =[
        'id',
        'id_promo',
        'id_member',
        'id_pegawai',
        'id_kelas',
        'jumlah_deposit',
        'tanggal_deposit',
        'bonus_deposit',
        'total_deposit',
        'jumlah_pembayaran',
        'tanggal_expired',
        'status'
    ];

    public function getCreatedAtAttribute(){
        if(!is_null($this->attributes['created_at'])){
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute(){
        if(!is_null($this->attributes['updated_at'])){
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }
}