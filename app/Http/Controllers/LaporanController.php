<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BookingGym;
use App\Http\Resources\MemberResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon; 

class LaporanController extends Controller
{
    public function aktivitasGymBulanan(Request $request)
    {
        $bulan = Carbon::now()->month;
        if ($request->has('month') && !empty($request->month)) {
            $bulan = $request->month;
        }
        $tanggalCetak = Carbon::now();

        if (!empty($request->month)) {
            $bulan = $request->month;
        }
        if (!empty($request->year)) {
            $tahun = $request->year;
        }

        $aktivitasGym = BookingGym::where('tanggal_booking', '<', $tanggalCetak)
            ->where('status', "hadir")
            ->whereMonth('tanggal_booking', $bulan)
            ->get()
            ->groupBy(function ($item) {
                // group by tanggal
                $carbonDate = Carbon::createFromFormat('Y-m-d', $item->tanggal_booking);
                return $carbonDate->toDateString();
            });
    
        // Count
        $responseData = [];
        $temp = 0;
    
        foreach ($aktivitasGym as $tanggal => $grup) {
            $count = $grup->Count();
            $temp += $count;
            $responseData[] = [
                'tanggal' => $tanggal,
                'count' => $count,
            ];
        }
    
        return response([
            'data' => $responseData,
            'tanggal' => $tanggalCetak,
        ]);
    }

    public function aktivitasKelasBulanan(Request $request)
    {
        $bulan = Carbon::now()->month;
        $namaBulan = Carbon::now()->format('F');
        $tahun = Carbon::now()->year;
        $tanggal = Carbon::now()->format('d-F-Y');

        if (!empty($request->month)) {
            $bulan = $request->month;
        }
        if (!empty($request->year)) {
            $tahun = $request->year;
        }

        $tanggalCetak = Carbon::now();
        $aktivitasKelas = DB::select('
            SELECT k.nama_kelas AS kelas, i.nama_instruktur AS instruktur, COUNT(bk.id) AS jumlah_peserta_kelas, 
                COUNT(CASE WHEN jh.keterangan = "diliburkan" THEN 1 ELSE NULL END) AS jumlah_libur
            FROM booking_kelas AS bk    
            JOIN jadwal_harians AS jh ON bk.id_jadwal_harian = jh.id
            JOIN jadwal_umums AS ju ON jh.id_jadwal_umum = ju.id
            JOIN instrukturs AS i ON ju.id_instruktur = i.id
            JOIN kelas AS k ON ju.id_kelas = k.id
            WHERE MONTH(jh.tanggal_kelas) = ?
            GROUP BY k.nama_kelas, i.nama_instruktur
        ', [$bulan]);
    
        //akumulasi terlambat direset tiap bulan jam mulai tiap bulan - jam selesai bulan
        return response([
            'data' => $aktivitasKelas,
            'tanggal' => $tanggal,
            'bulan' => $namaBulan,
            'tahun' => $tahun
        ]);
        
    }

    public function laporanKinerjaInstruktur(Request $request){
        $bulan = Carbon::now()->format('F');
        $tahun = Carbon::now()->year;
        $tanggalCetak = Carbon::now()->format('d');
        if (!empty($request->month)) {
            $bulan = $request->month;
        }
        if (!empty($request->year)) {
            $tahun = $request->year;
        }

        $laporan = DB::select('
            SELECT i.nama_instruktur AS instruktur,
            SUM(CASE WHEN pi.status = 1 THEN 1 ELSE 0 END) as jumlah_hadir, 
            SUM(CASE WHEN STRCMP(izin.status,"diterima") THEN 1 ELSE 0 END) as jumlah_izin, 
            SUM(CASE WHEN pi.waktu_terlambat iS NOT NULL THEN pi.waktu_terlambat ELSE 0 END) AS waktu_terlambat
            FROM instrukturs as i    
            LEFT JOIN izin_instrukturs as izin ON izin.id_instruktur = i.id
            LEFT JOIN presensi_instrukturs as pi ON pi.id_instruktur = i.id
            GROUP BY i.nama_instruktur
            ORDER BY SUM(CASE WHEN pi.WAKTU_TERLAMBAT iS NOT NULL THEN pi.WAKTU_TERLAMBAT ELSE 0 END)
            ');

        return response([
            'data' => $laporan,
            'tanggal_cetak' => $tanggalCetak,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);
    }

    public function laporanPendapatan(Request $request)
    {    
        for($x = 0; $x < 12 ; $x++){
            $report_income_deposit[] = DB::select(
                'SELECT MONTHNAME(t.tanggal_deposit) as bulan, SUM(t.jumlah_pembayaran) AS total_income_deposit 
                FROM 
                (SELECT jumlah_pembayaran, tanggal_deposit FROM transaksi_deposit_kelas 
                UNION ALL 
                SELECT total_deposit, tanggal_deposit FROM transaksi_deposit_uangs) t 
                WHERE MONTH(t.tanggal_deposit) ='.$x.' +1 
                GROUP BY bulan');

            $report_income_activaton[] = DB::select(
                'SELECT MONTHNAME(tanggal_transaksi) as bulan, SUM(jumlah_bayar) as total_income_activation 
                FROM transaksi_aktivasis 
                WHERE MONTH(tanggal_transaksi) ='.$x.' + 1
                GROUP BY bulan');
                    
            $report_total[] = DB::select(
                'SELECT MONTHNAME(t.tanggal_deposit) as bulan, SUM(t.jumlah_pembayaran) AS total_income FROM 
                (SELECT jumlah_pembayaran, tanggal_deposit FROM transaksi_deposit_kelas 
                UNION ALL 
                SELECT total_deposit, tanggal_deposit FROM transaksi_deposit_uangs
                UNION ALL
                SELECT jumlah_bayar, tanggal_transaksi FROM transaksi_aktivasis ) t 
                WHERE MONTH(t.tanggal_deposit) ='.$x.' +1 
                GROUP BY bulan'
            );
        }

        $collection = collect([
            $report_total
        ]);
    
        $collapsed = $collection->collapse();
        $collapsed2 = $collapsed->collapse();

        $temp_keys =['January','February','March','April','May','June','July','August','September','October','November','December'];
        $temp_value = [0,0,0,0,0,0,0,0,0,0,0,0];
        $keys = [];
        $value = [];

        for($i = 0; $i < 12; $i++){
            if($collapsed[$i]){
                $keys[] = $collapsed[$i][0]->bulan;
                $value[] = $collapsed[$i][0]->total_income;
            }else{
                $keys[] = $temp_keys[$i];
                $value[] = $temp_value[$i];
            }
        }

        return response([
            'data_depo_class' => $report_income_deposit,
            'data_activation' => $report_income_activaton,
            'data_total_income' => $report_total,
            'report_keys'=> $keys,
            'report_value' => $value
        ]);
    }
}
