<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function index()
    {
        //get member
        $member = Member::latest()->get();
        //render view with posts
        return new MemberResource(
            true,
            'List Data Member',
            $member
        );
    }

    public function store(Request $request)
    {
        //Validasi Formulir
        $validator = Validator::make($request->all(), [
            'nama_member' => 'required',
            'alamat_member' => 'required',
            'telepon_member' => 'required',
            'tanggal_lahir' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //Fungsi Post ke Database
        $member = Member::create([
            'nama_member' => $request->nama_member, 
            'alamat_member' => $request->alamat_member, 
            'telepon_member' => $request->telepon_member, 
            'tanggal_lahir' => $request->tanggal_lahir, 
            'email' => $request->email, 
            'password' => $request->password
        ]);
        return new MemberResource(true, 'Data Member Berhasil Ditambahkan!', $member);
    }

    public function edit($id)
    {
        $member = Member::findOrFail($id);
        return view('member.edit', compact('member'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama_member' => 'required',
            'alamat_member' => 'required',
            'telepon_member' => 'required',
            'tanggal_lahir' => 'required',
            'email' => 'required',
            'password' => 'required'

        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $member = Member::findOrFail($id);
        $member->update([
            'nama_member' => $request->nama_member, 
            'alamat_member' => $request->alamat_member, 
            'telepon_member' => $request->telepon_member, 
            'tanggal_lahir' => $request->tanggal_lahir, 
            'email' => $request->email, 
            'password' => $request->password
        ]);
        return new MemberResource(true, 'Data Member berhasil diubah!', $member);
    }

    public function destroy($id)
    {
        $member = Member::findOrFail($id);
        $member->delete();
        return new MemberResource(true, 'Data Member berhasil dihapus!', $member);
    }

    public function show($id)
    {
        $member= Member::find($id);

        if(!is_null($member)){
            return response([
                'message' => 'Data member Ditemukan',
                'data' => $member
            ], 200);
        }

        return response([
            'message' => 'Data member Tidak Ditemukan',
            'data' => null
        ], 404); // return message saat data member tidak ditemukan
    }

    public function cek(){
        $member = Member::latest()->get();        
        //render view with posts
        $temp = date('Y-m-d', strtotime('now'));

        for ($x = 0; $x < sizeof($member); $x++){
            if($member[$x]->masa_aktivasi < $temp){
                $member[$x]->update([
                    'status' => 0
                ]);
            }
        }
        return new MemberResource(true, 'Data Member berhasil diubah!', $member);
    }
}