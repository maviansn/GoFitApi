<?php

namespace App\Http\Controllers;

use App\Models\Promo;
use App\Models\Member;
use App\Models\Kelas;
use App\Models\TransaksiDepositKelas;
use Exception;
use Illuminate\Http\Request;
use App\Http\Resources\InstrukturResource;
use App\Models\Pegawai;

class TransaksiDepositKelasController extends Controller
{
    public function index()
    {
        //get Instruktur
        $transaksi_deposit_kelas = TransaksiDepositKelas::
        join('members', 'transaksi_deposit_kelas.id_member', '=', 'members.id')
        ->join('pegawais', 'transaksi_deposit_kelas.id_pegawai', '=', 'pegawais.id')
        ->join('promos', 'transaksi_deposit_kelas.id_promo', '=', 'promos.id')
        ->join('kelas', 'transaksi_deposit_kelas.id_kelas', '=', 'kelas.id')
        ->select('transaksi_deposit_kelas.id as id', 'pegawais.nama_pegawai as pegawai', 'members.nama_member as member',
        'promos.nama_promo as promo', 'kelas.nama_kelas as kelas', 'transaksi_deposit_kelas.jumlah_deposit',
        'transaksi_deposit_kelas.bonus_deposit', 'transaksi_deposit_kelas.total_deposit', 'transaksi_deposit_kelas.status',
        'transaksi_deposit_kelas.jumlah_pembayaran','transaksi_deposit_kelas.tanggal_expired')
        ->get();
        //render view with posts
        return new InstrukturResource(
            true,
            'List Data Transaksi Deposit Kelas',
            $transaksi_deposit_kelas
        );
    }

    public function store(Request $request)
    {

        if($request->jumlah_deposit <= 0 ){
            return response(
                ['message'=> 'Minimal Deposit kelas 1',] , 400);
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

            $kelas = Kelas::findorfail($request->id_kelas);
            $jumlahBayar = $jumlah_deposit * $kelas->tarif;
            $pegawai = Pegawai::where('role','like','Kasir')->first();
            $depositKelas = TransaksiDepositKelas::firstOrCreate  ([
                'tanggal_deposit' => date('Y-m-d H:i:s', strtotime('now')),
                'jumlah_deposit' => $jumlah_deposit,
                'bonus_deposit' => $bonus_deposit,
                'total_deposit' => $total_deposit,
                'id_pegawai' => $pegawai->id,
                'id_member'=> $request->id_member,
                'id_promo' => $id_promo,
                'id_kelas' => $request->id_kelas,
                'jumlah_pembayaran' => $jumlahBayar,
                'tanggal_expired' => date('Y-m-d H:i:s', strtotime('+1 month', strtotime('now'))),
                'status' => 1
            ]);
            
            $member = Member::find($request->id_member);
            $sebelum_transaksi = $member->sisa_deposit;
            $member->sisa_deposit =  $sebelum_transaksi + $total_deposit;
            $member->save();
            return response([
                'message'=> 'Berhasil Melakukan Transaksi',
                'data' => [
                    'transaksi_deposit_uang' => $depositKelas, 
                    'sisa_deposit' => $sebelum_transaksi, 
                    'id' => TransaksiDepositKelas::latest()->first()->id, 
                    'Member' => $member->nama_member],
                'total' => $total_deposit,
            ]);

        } catch(Exception $e){
            echo $e;
        }    
    }

    public function updateStatus(){
        $depositKelas = TransaksiDepositKelas::latest()->get();        
        //render view with posts
        $temp = date('Y-m-d', strtotime('now'));
        for ($x = 0; $x < sizeof($depositKelas); $x++){
            if($depositKelas[$x]->tanggal_expired < $temp){
                $depositKelas[$x]->update([
                    'status' => 0
                    
                ]);
            }
        }
        return new InstrukturResource(true, 'Data berhasil diubah!', $depositKelas);
    }
}