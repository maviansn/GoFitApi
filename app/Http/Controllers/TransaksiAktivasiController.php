<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Pegawai;
use App\Models\TransaksiAktivasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Http\Resources\InstrukturResource;
use Illuminate\Support\Facades\DB;

class TransaksiAktivasiController extends Controller
{
    public function index()
    {
        //get Instruktur
        $transaksi_aktivasi = DB::table('transaksi_aktivasis')
        ->join('members', 'transaksi_aktivasis.id_member', '=', 'members.id')
        ->join('pegawais', 'transaksi_aktivasis.id_pegawai', '=', 'pegawais.id')
        ->select('transaksi_aktivasis.id as id', 'pegawais.nama_pegawai as pegawai', 'members.nama_member as member',
        'transaksi_aktivasis.jumlah_bayar as jumlahBayar', 'transaksi_aktivasis.tanggal_transaksi as tglTransaksi',
        'transaksi_aktivasis.tanggal_expired as tglExpired')
        ->get();
        //render view with posts
        return new InstrukturResource(
            true,
            'List Data Transaksi Aktivasi',
            $transaksi_aktivasi
        );
    }

    public function store(Request $request)
    {   
        $member = Member::where('id','=',$request->id_member)->first();
        if ($member->masa_aktivasi == null) {
            $tgl = date('Y-m-d H:i:s');
        } else {
            $tgl= $member->masa_aktivasi;
        }
        $expired = date('Y-m-d H:i:s', strtotime('+1 year', strtotime($tgl)));
        $member->masa_aktivasi = $expired;
        $member->save();
        $pegawai = Pegawai::where('role','like','Kasir')->first();

        $transaksi_aktivasi = TransaksiAktivasi::create([
            'tanggal_transaksi' => date('Y-m-d', strtotime('now')),
            'id_member' => $request->id_member,
            'id_pegawai' => $pegawai->id,
            'jumlah_bayar' => '3000000',
            'tanggal_expired' => date('Y-m-d H:i:s', strtotime('+1 year', strtotime($tgl)))
        ]);

        return response([
            'message'=> 'success tambah data transaksi aktivasi',
            'data' => ['transaksi_aktivasi' => $transaksi_aktivasi, 'member' => $member, 'no_struk' => TransaksiAktivasi::latest()->first()->no_struk_aktivasi],
        ]);
        
    }

    public function destroy($id)
    {
        $transaksiAktivasi = TransaksiAktivasi::findOrFail($id);
        $transaksiAktivasi->delete();
        return new InstrukturResource(true, 'Data Transaksi Aktivasi berhasil dihapus!', $transaksiAktivasi);
    }
}