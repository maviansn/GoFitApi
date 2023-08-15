<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalUmum;
use App\Models\Kelas;
use App\Http\Resources\JadwalUmumResource;
use App\Models\Instruktur;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class JadwalUmumController extends Controller
{
    public function index()
    {
        //get jadwalUmum
        $jadwalUmum = DB::table('jadwal_umums')
        ->join('kelas', 'jadwal_umums.id_kelas', '=', 'kelas.id')
        ->join('instrukturs', 'jadwal_umums.id_instruktur', '=', 'instrukturs.id')
        ->select('jadwal_umums.hari as hari', 'jadwal_umums.waktu as waktu', 'jadwal_umums.id as id',
        'kelas.nama_kelas as kelas', 'instrukturs.nama_instruktur as instruktur')
        ->get();
        //render view with posts    
        return new JadwalUmumResource(
            true,
            'List Data Jadwal Umum',
            $jadwalUmum
        );
    }

    public function store(Request $request)
    {
        //Validasi Formulir
        $validator = Validator::make($request->all(), [
            'id_kelas' => 'required',
            'id_instruktur' => 'required',
            'hari' => 'required',
            'waktu' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $kelas = Kelas::where('nama_kelas',$request->id_kelas)->first();
        $instruktur = Instruktur::where('nama_instruktur',$request->id_instruktur)->first();
        //Fungsi Post ke Database
        $jadwalUmum = JadwalUmum::create([
            'id_kelas' => $kelas->id,
            'id_instruktur' => $instruktur->id,
            'hari' => $request->hari,
            'waktu' => $request->waktu
        ]);
        return new JadwalUmumResource(true, 'Data Jadwal Umum Berhasil Ditambahkan!', $jadwalUmum);
    }

    public function edit($id)
    {
        $jadwal = JadwalUmum::findOrFail($id);
        return view('jadwalUmum.edit', compact('jadwalUmum'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id_kelas' => 'required',
            'id_instruktur' => 'required',
            'hari' => 'required',
            'waktu' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $jadwal = JadwalUmum::findOrFail($id);
        $kelas = Kelas::where('nama_kelas',$request->id_kelas)->first();
        $instruktur = Instruktur::where('nama_instruktur',$request->id_instruktur)->first();
        $jadwal->update([
            'id_kelas' => $kelas->id,
            'id_instruktur' => $instruktur->id,
            'hari' => $request->hari,
            'waktu' => $request->waktu
        ]);
        return new JadwalUmumResource(true, 'Data Jadwal Umum berhasil diubah!', $jadwal);
    }

    public function destroy($id)
    {
        $jadwalUmum = JadwalUmum::findOrFail($id);
        $jadwalUmum->delete();
        return new JadwalUmumResource(true, 'Data Jadwal Umum berhasil dihapus!', $jadwalUmum);
    }

    public function show($id)
    {
        $jadwalumum= JadwalUmum::find($id);

        if(!is_null($jadwalumum)){
            return response([
                'message' => 'Data Jadwal Umum Ditemukan',
                'data' => $jadwalumum
            ], 200);
        }

        return response([
            'message' => 'Data Jadwal Umum Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data instruktur tidak ditemukan
    }
}