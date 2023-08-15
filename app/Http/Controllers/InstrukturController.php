<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Instruktur;
use App\Http\Resources\InstrukturResource;
use Illuminate\Support\Facades\Validator;

class InstrukturController extends Controller
{
    public function index()
    {
        //get Instruktur
        $instruktur = Instruktur::latest()->get();
        //render view with posts
        return new InstrukturResource(
            true,
            'List Data Instruktur',
            $instruktur
        );
    }
    

    /**
     * store
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        //Validasi Formulir
        $validator = Validator::make($request->all(), [
            'nama_instruktur' => 'required',
            'alamat_instruktur' => 'required',
            'telepon_instruktur' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //Fungsi Post ke Database
        $departemen = Instruktur::create([
            'nama_instruktur' => $request->nama_instruktur, 
            'alamat_instruktur' => $request->alamat_instruktur, 
            'telepon_instruktur' => $request->telepon_instruktur, 
            'email' => $request->email, 
            'password' => $request->password, 
        ]);
        return new InstrukturResource(true, 'Data Instruktur Berhasil Ditambahkan!', $departemen);
    }

    public function edit($id)
    {
        $instruktur = Instruktur::findOrFail($id);
        return view('instruktur.edit', compact('instruktur'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_instruktur' => 'required',
            'alamat_instruktur' => 'required',
            'telepon_instruktur' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $instruktur = Instruktur::findOrFail($id);
        $instruktur->update([
            'nama_instruktur' => $request->nama_instruktur, 
            'alamat_instruktur' => $request->alamat_instruktur, 
            'telepon_instruktur' => $request->telepon_instruktur, 
            'email' => $request->email, 
            'password' => $request->password,
        ]);
        return new InstrukturResource(true, 'Data Instruktur berhasil diubah!', $instruktur);
    }

    public function destroy($id)
    {
        $instruktur = Instruktur::findOrFail($id);
        $instruktur->delete();
        return new InstrukturResource(true, 'Data Instruktur berhasil dihapus!', $instruktur);
    }

    public function show($id)
    {
        $instruktur= Instruktur::find($id);

        if(!is_null($instruktur)){
            return response([
                'message' => 'Data Instruktur Ditemukan',
                'data' => $instruktur
            ], 200);
        }

        return response([
            'message' => 'Data Instruktur Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data instruktur tidak ditemukan
    }
}