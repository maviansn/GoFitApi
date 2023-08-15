<?php

namespace App\Http\Controllers;
use App\Models\JadwalHarian;
use Illuminate\Http\Request;
use App\Http\Resources\JadwalUmumResource;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 


class JadwalHarianController extends Controller
{

    public function index()
    {
        $jadwalHarian = DB::table('jadwal_harians')
        ->join('instrukturs', 'jadwal_harians.id_instruktur', '=', 'instrukturs.id')
        ->join('jadwal_umums', 'jadwal_harians.id_jadwal_umum', '=', 'jadwal_umums.id')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->select('jadwal_harians.id as id', 'instrukturs.nama_instruktur as instruktur', 'jadwal_harians.tanggal_kelas as tgl' ,
        'jadwal_harians.keterangan as keterangan', 'kelas.nama_kelas as nama_kelas', 'jadwal_umums.hari as hari', 'jadwal_umums.waktu as waktu')
        ->get();
        //render view with posts
        return new JadwalUmumResource(
            true,
            'List Data Jadwal Harian',
            $jadwalHarian
        );
    }

    public function store(){
        // cek udah generate atau belum
        $cekJadwalHarian = JadwalHarian::where('tanggal_kelas', '>', Carbon::now()->startOfWeek(Carbon::SUNDAY)->format('Y-m-d'))->first();
        if(!is_null($cekJadwalHarian)){
            return response()->json([
                'success' => false,
                'message' => 'Jadwal Harian Telah di Generate  ',
                'data' => null
            ]);
        }
        
        //generate
        $start_date = Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDay();
        $end_date = Carbon::now()->startOfWeek(Carbon::SUNDAY)->addDays(7);
        
        //Mapping Hari
        $map = [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ];
        for($date = $start_date ; $date->lte($end_date);$date->addDay())
        {
            $hari = Carbon::parse($date)->format('l');
            $jadwal_umum = DB::table('jadwal_umums')
            ->where('jadwal_umums.hari','=',$map[strtolower($hari)])
            ->get();

            foreach($jadwal_umum as $jd){
                //Agar tidak double
                $jadwal_harian = DB::table('jadwal_harians')
                ->where('tanggal_kelas','=',$date->toDateString())
                ->where('id_jadwal_umum', '=', $jd->id)
                ->first();
                if(!$jadwal_harian){
                    DB::table('jadwal_harians')->insert([
                        'tanggal_kelas' =>$date->toDateString(),
                        'keterangan' => 'Sedang Berlangsung',
                        'id_jadwal_umum' =>$jd->id,
                        'id_instruktur' =>$jd->id_instruktur,   
                    ]);
                }
            }
        }
        return response([
            'message'=> 'Berhasil Melakukan Generate',
        ]);
    }
    public function update($id_jadwal_harian){
        $jadwal_harian = JadwalHarian::find($id_jadwal_harian);
        $jadwal_harian->keterangan = 'Diliburkan';
        $jadwal_harian->update();
        return response()->json(['message' => 'Jadwal Harian berhasil diliburkan'], 200);
    }
}