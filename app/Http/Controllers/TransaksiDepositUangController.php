<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\Member;
use App\Models\Pegawai;
use App\Models\TransaksiDepositUang;
use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\InstrukturResource;

class TransaksiDepositUangController extends Controller
{
    public function index()
    {
        //get Instruktur
        $transaksi_deposit_uangs = TransaksiDepositUang::
        join('members', 'transaksi_deposit_uangs.id_member', '=', 'members.id')
        ->join('pegawais', 'transaksi_deposit_uangs.id_pegawai', '=', 'pegawais.id')
        ->join('promos', 'transaksi_deposit_uangs.id_promo', '=', 'promos.id')
        ->select('transaksi_deposit_uangs.id as id', 'pegawais.nama_pegawai as pegawai', 'members.nama_member as member',
        'promos.nama_promo as promo', 'transaksi_deposit_uangs.jumlah_deposit', 'transaksi_deposit_uangs.tanggal_deposit',
        'transaksi_deposit_uangs.bonus_deposit', 'transaksi_deposit_uangs.total_deposit')
        ->get();
        //render view with posts
        return new InstrukturResource(
            true,
            'List Data Transaksi Deposit Uang',
            $transaksi_deposit_uangs
        );
    }

    public function store(Request $request)
    {

        if($request->jumlah_deposit < 500000 ){
            return response(
                ['message'=> 'Minimal Deposit Rp 500.000',] , 400);
        }
        try
        {
            $id_promo = 3;
            if($request->id_promo != null){
                $promo = Promo::findorfail($request->id_promo);
                $minimal_deposit = $promo->minimal_deposit;
                $jumlah_deposit = $request->jumlah_deposit;
                $total_deposit = $request->jumlah_deposit;
                if($minimal_deposit <= $jumlah_deposit){
                    $id_promo = $request->id_promo;
                    $bonus_deposit = $promo->bonus_promo;
                    $total_deposit += $promo->bonus_promo;
                }else{
                    $id_promo = 3;
                    $bonus_deposit = 0;
                    $total_deposit = $jumlah_deposit;
                }
                
            }else{
                $jumlah_deposit = $request->jumlah_deposit;
                $bonus_deposit = 0;
                $total_deposit = $jumlah_deposit;
            }
            $pegawai = Pegawai::where('role','like','Kasir')->first();
            $depositUang = TransaksiDepositUang::firstOrCreate  ([
                'tanggal_deposit' => date('Y-m-d H:i:s', strtotime('now')),
                'jumlah_deposit' => $jumlah_deposit,
                'bonus_deposit' => $bonus_deposit,
                'total_deposit' => $total_deposit,
                'id_pegawai' => $pegawai->id,
                'id_member'=> $request->id_member,
                'id_promo' => $id_promo
            ]);
            
            $member = Member::find($request->id_member);
            $sebelum_transaksi = $member->sisa_deposit;
            $member->sisa_deposit =  $sebelum_transaksi + $total_deposit;
            $member->save();
            return response([
                'message'=> 'Berhasil Melakukan Transaksi',
                'data' => ['transaksi_deposit_uangs' => $depositUang, 'sisa_deposit' => $sebelum_transaksi, 'id' => TransaksiDepositUang::latest()->first()->id, 'Member' => $member->nama_member],
                'total' => $total_deposit,
            ]);

        } catch(Exception $e){
            echo $e;
        }
        
    }
}