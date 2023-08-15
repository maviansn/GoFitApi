<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TransaksiDepositUang extends Model
{
    public $incrementing = false;
    use HasFactory;
    protected $primaryKey = 'id';
    protected $fillable =[
        'id',
        'id_promo',
        'id_member',
        'id_pegawai',
        'tanggal_deposit',
        'jumlah_deposit',
        'bonus_deposit',
        'total_deposit',
    ];

    protected $casts = [
        'id' => 'string'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'id_member', 'id');

    }
    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id');

    }
    public function promo()
    {
        return $this->belongsTo(Promo::class, 'id_promo', 'id');

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