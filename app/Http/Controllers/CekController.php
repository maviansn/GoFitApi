<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Validator;

class CekController extends Controller
{
    public function update(){
        $member = Member::latest()->get();        
        //render view with posts
        for ($x = 0; $x < sizeof($member); $x++){
            if($member->masa_aktivasi < date('Y-m-d', strtotime('now'))){
                $member->update([
                    $member->status => "0"
                ]);
                return new MemberResource(true, 'Data Member berhasil diubah!', $member);
            }
        }
    }
}
