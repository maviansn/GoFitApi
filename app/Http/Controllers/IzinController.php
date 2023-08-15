<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IzinInstruktur;
use App\Http\Resources\InstrukturResource;
use Illuminate\Support\Facades\Validator;

class IzinController extends Controller
{
    public function index()
    {
        //get Instruktur
        $izin = IzinInstruktur::
        join('instrukturs', 'izin_instrukturs.id_instruktur', '=', 'instrukturs.id')
        ->select('instrukturs.nama_instruktur as instruktur', 'izin_instrukturs.tanggal_izin', 'izin_instrukturs.keterangan',
        'izin_instrukturs.tanggal_melakukan_izin', 'izin_instrukturs.status', 'izin_instrukturs.tanggal_konfirmasi',
        'izin_instrukturs.id as id')
        ->get();
        //render view with posts
        return new InstrukturResource(
            true,
            'List Data Izin',
            $izin
        );
    }

    public function store(Request $request)
    {
        //Validasi Formulir
        $validator = Validator::make($request->all(), [
            'id_instruktur' => 'required',
            'keterangan' => 'required',
            'tanggal_izin' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //Fungsi Post ke Database
        $izin = IzinInstruktur::create([
            'id_instruktur' => $request->id_instruktur, 
            'tanggal_izin' => $request->tanggal_izin, 
            'keterangan' => $request->keterangan, 
            'tanggal_melakukan_izin' => date('Y-m-d', strtotime('now')),
            'status' => 'pending', 
        ]);
        return new InstrukturResource(true, 'Data Izin Instruktur Berhasil Ditambahkan!', $izin);
    }

    public function edit($id)
    {
        $izin = IzinInstruktur::findOrFail($id);
        return view('izin.edit', compact('izin'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $izin = IzinInstruktur::findOrFail($id);
        $izin->update([
            'status' => $request->status,
            'tanggal_konfirmasi' => date('Y-m-d', strtotime('now')),
        ]);
        return new InstrukturResource(true, 'Data Izin Instruktur berhasil diubah!', $izin);
    }
}
