<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PresensiInstruktur;
use App\Models\Instruktur;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Validator;


class PresensiController extends Controller
{
    public function index()
    {
        //get member
        $presensi = PresensiInstruktur::
        join('instrukturs', 'presensi_instrukturs.id_instruktur', '=', 'instrukturs.id')
        ->select('presensi_instrukturs.id as id', 'instrukturs.nama_instruktur as instruktur', 
        'presensi_instrukturs.tanggal_mengajar', 'presensi_instrukturs.jam_mulai', 
        'presensi_instrukturs.jam_selesai', 'presensi_instrukturs.status')
        ->get();
        //render view with posts
        return new MemberResource(
            true,
            'List Data Presensi',
            $presensi
        );
    }

    public function destroy()
    {
        $presensi = PresensiInstruktur::latest()->get();
        for($x = 0; $x < sizeof($presensi);$x++){
            $presensi[$x]->delete();
        }

        $instruktur = Instruktur::latest()->get();
        for($x = 0; $x < sizeof($instruktur);$x++){
            $instruktur[$x]->update([
                'total_keterlambatan' => 0
            ]);
        }
        return new MemberResource(true, 'Total Keterlambatan berhasil direset!', $presensi);
    }

    public function tambahKeterlambatan(){
        $presensi = PresensiInstruktur::latest()->get();
        for($x = 0; $x < sizeof($presensi);$x++){
            if($presensi[$x]->status == 1){
                $instruktur = Instruktur::findOrFail($presensi[$x]->id_instruktur);
                $temp = $instruktur->total_keterlambatan;
                $plus = $temp + 1;
                $instruktur->update([
                    'total_keterlambatan' => $plus
                ]);
            }
        }
        return new MemberResource(true, 'Total Keterlambatan berhasil direset!', $presensi);
    }
}
