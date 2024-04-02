<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaPesan;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;


class UtilityController extends Controller
{
    public function jumlahPesanBulanan($thn)
    {
        $data = WaPesan::selectRaw('MONTH(created_at) as bln, COUNT(*) as jumlah')
            ->whereYear('created_at', $thn)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')->get();
        $dataPerbulan = array_fill(1, 12, 0);
        foreach ($data as $bln => $dp) {
            $dataPerbulan[$dp->bln] = $dp->jumlah;
        }
        return response()->json($dataPerbulan);
    }

    public function jumlahPegawai()
    {
        $data = Pegawai::selectRaw('COUNT(*) as jumlah, is_aktif')
            ->groupByRaw('is_aktif')
            ->orderByRaw('is_aktif DESC')->get();

        // dd($data);
        $ret = [0, 0];
        foreach ($data as $i => $dp) {
            $ret[$i] = $dp->jumlah;
        }
        return response()->json($ret);
    }

    public function jumlahWaBlast($thn)
    {
        $data = DB::table('wa_pesans as w')
            ->join('proses as p', 'w.id', '=', 'p.wa_pesan_id')
            ->join('kirim_pesans as k', 'k.proses_id', '=', 'p.id')
            ->selectRaw(
                'COUNT(*) as total,
                SUM(CASE WHEN k.is_berhasil = true THEN 1 ELSE 0 END) as berhasil,
                SUM(CASE WHEN k.is_berhasil = false THEN 1 ELSE 0 END) as gagal,
                SUM(CASE WHEN k.is_berhasil IS NULL THEN 1 ELSE 0 END) as belum'
            )
            ->whereYear('w.created_at', $thn)
            ->groupByRaw('YEAR(w.created_at)')
            ->first();

        if ($data) {
            $ret = [
                'total' => intval($data->total),
                'gagal' => intval($data->gagal),
                'berhasil' => intval($data->berhasil),
                'belum' => intval($data->belum)
            ];
        } else {
            $ret = [
                'total' => 0,
                'gagal' => 0,
                'berhasil' => 0,
                'belum' => 0
            ];
        }
        return response()->json($data);
    }
}
