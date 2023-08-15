<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\MemberResource;

class PegawaiController extends Controller
{
    public function index()
    {
        $pegawai = Pegawai::latest()->get();
        //render view with posts
        return new MemberResource(
            true,
            'List Data Pegawai',
            $pegawai
        );
    }

    public function show($id)
    {
        $pegawai= Pegawai::where('email','like',$id)->latest()->get();

        if(!is_null($pegawai)){
            return response([
                'message' => 'Data Pegawai Ditemukan',
                'data' => $pegawai
            ], 200);
        }

        return response([
            'message' => 'Data Pegawai Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data instruktur tidak ditemukan
    }
}