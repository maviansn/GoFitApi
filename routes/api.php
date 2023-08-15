<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/member', 
App\Http\Controllers\MemberController::class);

Route::apiResource('/instruktur', 
App\Http\Controllers\InstrukturController::class);

Route::apiResource('/jadwalUmum', 
App\Http\Controllers\JadwalUmumController::class);

Route::apiResource('/kelas', 
App\Http\Controllers\KelasController::class);

Route::apiResource('/jadwalHarian', 
App\Http\Controllers\JadwalHarianController::class);

Route::apiResource('/promoKelas', 
App\Http\Controllers\PromoKelasController::class);

Route::apiResource('/promoUang', 
App\Http\Controllers\PromoUangController::class);

Route::apiResource('/pegawai', 
App\Http\Controllers\PegawaiController::class);

Route::apiResource('/transaksiDepositUang', 
App\Http\Controllers\TransaksiDepositUangController::class);

Route::apiResource('/transaksiAktivasi', 
App\Http\Controllers\TransaksiAktivasiController::class);

Route::apiResource('/transaksiDepositKelas', 
App\Http\Controllers\TransaksiDepositKelasController::class);

Route::apiResource('/login', 
App\Http\Controllers\AuthController::class);

Route::apiResource('/izin', 
App\Http\Controllers\IzinController::class);

Route::apiResource('/bookinggym', 
App\Http\Controllers\BookingGymController::class);

Route::put('cek','App\Http\Controllers\MemberController@cek');
Route::put('updateStatus','App\Http\Controllers\TransaksiDepositKelasController@updateStatus');
Route::delete('presensiInstruktur','App\Http\Controllers\PresensiController@destroy');
Route::get('presensiInstruktur','App\Http\Controllers\PresensiController@index');
Route::put('presensiInstruktur','App\Http\Controllers\PresensiController@tambahKeterlambatan');
Route::get('bookingkelaspaket','App\Http\Controllers\BookingKelasController@indexPaket');
Route::get('bookingkelas','App\Http\Controllers\BookingKelasController@index');
Route::post("loginPegawai", "App\Http\Controllers\LoginController@loginPegawai");
Route::post("loginMember", "App\Http\Controllers\LoginController@loginMember");
Route::post("loginInstruktur", "App\Http\Controllers\LoginController@loginInstruktur");
Route::get("laporanKelas", "App\Http\Controllers\LaporanController@aktivitasKelasBulanan");
Route::get("laporanGym", "App\Http\Controllers\LaporanController@aktivitasGymBulanan");
Route::get("laporanInstruktur", "App\Http\Controllers\LaporanController@laporanKinerjaInstruktur");
Route::get("laporanPendapatan", "App\Http\Controllers\LaporanController@laporanPendapatan");