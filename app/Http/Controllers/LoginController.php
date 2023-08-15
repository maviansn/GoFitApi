<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Instruktur;
use App\Models\Member;
use App\Models\Pegawai;
use App\Models\Login;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\InstrukturResource;


class LoginController extends Controller
{
    public function loginPegawai(Request $request)
    {
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
        $user = Login::create([
            'email' => $request->email, 
            'password' => $request->password, 
        ]);
        return new InstrukturResource(true, 'Data Instruktur Berhasil Ditambahkan!', $user);
    }

    public function loginInstruktur(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $instruktur = Instruktur::
        where('email', $request->email)
        ->where('password', $request->password)
        ->first();

        if(!$instruktur){
            return response(200);
        }

        //Fungsi Post ke Database
        $user = Login::create([
            'email' => $request->email, 
            'password' => $request->password, 
        ]);
        return new InstrukturResource(true, 'Data Instruktur Berhasil Ditambahkan!', $user);
    }

    public function loginMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $member = Member::
        where('email', $request->email)
        ->where('password', $request->password)
        ->first();

        if(!$member){
            return response(200);
        }

        //Fungsi Post ke Database
        $user = Login::create([
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