<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Promo;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\MemberResource;

class PromoUangController extends Controller
{
    public function index()
    {
        $promo = Promo::where('jenis_promo','like','uang')->latest()->get();
        //render view with posts
        return new MemberResource(
            true,
            'List Data Promo',
            $promo
        );
    }
}