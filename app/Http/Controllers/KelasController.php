<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Kelas;
use Exception;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\KelasResource;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::latest()->get();
        //render view with posts
        return new KelasResource(
            true,
            'List Data Kelas',
            $kelas
        );
    }
}