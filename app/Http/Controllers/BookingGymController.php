<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingGym;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class BookingGymController extends Controller
{
    public function index(){
        $bookgym = DB::table('booking_gyms')
        ->join('members','booking_gyms.id_member','=','members.id')
        ->select('booking_gyms.id as id', 'members.nama_member as member','booking_gyms.sesi', 
        'booking_gyms.status','booking_gyms.tanggal_booking', 'booking_gyms.tanggal_melakukan_booking',
        'booking_gyms.waktu_presensi', 'members.id as id_member')
        ->get();
        return new MemberResource(true, 'List Data Booking Gym', $bookgym);
    }

    public function store(Request $request){
        try
        {
            $id_member = $request->id_member;
            $member = Member::find($id_member);
            $id_gym =  $request->id_gym;
            $gym = BookingGym::find($id_gym);
            $tgl_reserve = $request->tgl_reservasi_gym;

            if($member->status_membership == "Tidak Aktif"){
                return response(
                    ['message'=> 'Maaf Status Member Anda Tidak Aktif',] , 400);
            }else if($gym->kapasitas == "0"){
                return response(
                    ['message'=> 'Maaf Kapasitas Gym Sudah Penuh',] , 400);
            }else{
                $cek = BookingGym::where('id_member', $id_member)->value('booking_gyms.tgl_reservasi_gym');
                if($tgl_reserve == $cek ){
                    return response(
                        ['message'=> 'Anda Hanya Dapat Melakukan Booking Gym 1x Sehari',]
                    ,400);
                }else{
                    $bookingGym = BookingGym::firstOrCreate([
                        'id_member' => $request->id_member,
                        'id_gym' => $request->id_gym,
                        'tgl_reservasi_gym' => $request->tgl_reservasi_gym, //tgl pilihan memmber buat gym
                        'tgl_booking_gym' => date('Y-m-d H:i:s', strtotime('now')), //tgl_transaksi
                        'status_presensi'=>'Tidak Hadir',
                    ]);
                    $gym = Gym::find($request->id_gym);
                    $gym->kapasitas -= 1; 
                    $gym->save();
                    return response([
                        'message' => 'Booking Gym Berhasil',
                        'data' => ['Booking_Gym'=>$bookingGym, 'Gym'=>$gym, 'no_booking_gym'=>BookingGym::latest()->first()->no_booking_gym, 'nama_member'=>$member->nama_member, 'nomor_member' => $member->no_member, 'sesi_gym' =>$gym->id, 'sisa_kapasitas' =>$gym->kapasitas],
                    ]);
                }
                
            }

        } catch(Exception $e){
            dd($e);
        }
    }

    public function destroy($id)
    {
        $today = Carbon::today();
        $batalGym = BookingGym::findOrFail($id);
        if($batalGym->tgl_reservasi_gym > $today){
            $batalGym->delete();
            return new MemberResource(true, 'Data Jadwal Umum berhasil dihapus!', $batalGym);
        }else{
            return response(
                ['message'=> 'Pembatalan Hanya Dapat Dilakukan H-1 Tanggal Reservasi ',]
            ,400);
        }
       
    }

    public function update(Request $request, $id){
        $presensi = BookingGym::findOrFail($id);
        $presensi->update([
            'status' => $request->status,
            'waktu_presensi' => Carbon::now()
        ]);
        return new MemberResource(true, 'Data presensi berhasil diubah!', $presensi);
    }
}
