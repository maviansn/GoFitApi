<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\InstrukturResource;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function index()
    {
        //get Instruktur
        $user = User::latest()->get();
        //render view with posts
        return new InstrukturResource(
            true,
            'List Data user',
            $user
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
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $pegawai = Pegawai::
        where('email', $request->email)
        ->where('password', $request->password)
        ->first();

        if(!$pegawai){
            return response(200);
        }

        //Fungsi Post ke Database
        $user = User::create([
            'email' => $request->email, 
            'password' => $request->password, 
        ]);
        return new InstrukturResource(true, 'Data Instruktur Berhasil Ditambahkan!', $user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return new InstrukturResource(true, 'Data user berhasil dihapus!', $user);
    }
}
