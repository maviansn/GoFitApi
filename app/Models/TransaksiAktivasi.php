<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransaksiAktivasi extends Model
{
    public $incrementing = false;
    
    use HasFactory;
    protected $primaryKey= 'id';
    protected $fillable =[
        'id',
        'tanggal_transaksi',
        'jumlah_bayar',
        'id_member',
        'id_pegawai',
        'tanggal_expired'
    ];

    public function member(){
        return $this->belongsTo(member::class, 'id_member', 'id');
    }

    public function pegawai(){
      return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id');
    }

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