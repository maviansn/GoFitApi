<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingGym;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class BookingKelasController extends Controller
{
    public function index(){
        $bookKelas = DB::table('booking_kelas')
        ->join('members','booking_kelas.id_member','=','members.id')
        ->join('jadwal_harians','booking_kelas.id_jadwal_harian', '=','jadwal_harians.id')
        ->join('instrukturs', 'jadwal_harians.id_instruktur', '=', 'instrukturs.id')
        ->join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->select('booking_kelas.id as id', 'members.nama_member as member','booking_kelas.jenis_pembayaran', 
        'booking_kelas.status','booking_kelas.tanggal_booking', 'booking_kelas.tanggal_melakukan_booking',
        'booking_kelas.waktu_presensi', 'members.id as id_member', 'kelas.nama_kelas as kelas',
        'instrukturs.nama_instruktur as instruktur', 'members.sisa_deposit', 'kelas.tarif')
        ->where('booking_kelas.jenis_pembayaran', 'like', 'uang')
        ->get();
        return new MemberResource(true, 'List Data Booking Kelas', $bookKelas);
    }

    public function indexPaket(){
        $bookKelas = DB::table('booking_kelas')
        ->join('members','booking_kelas.id_member','=','members.id')
        ->join('jadwal_harians','booking_kelas.id_jadwal_harian', '=','jadwal_harians.id')
        ->join('instrukturs', 'jadwal_harians.id_instruktur', '=', 'instrukturs.id')
        ->join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->join('member_deposit_kelas', 'member_deposit_kelas.id_member', '=', 'members.id')
        ->select('booking_kelas.*', 'members.nama_member as member', 'kelas.nama_kelas as kelas',
        'instrukturs.nama_instruktur as instruktur', 'member_deposit_kelas.sisa_deposit as sisa', 
        'member_deposit_kelas.masa_berlaku', 'jadwal_harians.tanggal_kelas')
        ->where('booking_kelas.jenis_pembayaran', 'like', 'paket')
        ->whereRaw('member_deposit_kelas.id_kelas = kelas.id')
        ->get();
        return new MemberResource(true, 'List Data Booking Kelas', $bookKelas);
    }
}
