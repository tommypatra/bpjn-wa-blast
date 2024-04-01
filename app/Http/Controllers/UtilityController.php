<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaPesan;
use App\Models\Pegawai;


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
}
