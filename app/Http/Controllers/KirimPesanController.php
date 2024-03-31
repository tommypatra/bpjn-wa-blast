<?php

namespace App\Http\Controllers;

use App\Http\Requests\KirimPesanRequest;
use App\Models\KirimPesan;
use App\Models\WaPesan;
use App\Models\Pegawai;
use App\Models\Proses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KirimPesanController extends Controller
{
    public function index(Request $request)
    {
        $wa_pesan_id = $request->wa_pesan_id;
        $dataProses = WaPesan::with([
            'proses' => function ($query) {
                $query->select('id', 'wa_pesan_id', 'updated_at', DB::raw('CONVERT_TZ(created_at, "+00:00", "+08:00") AS created_at'));
            },
            'proses.kirimpesan' => function ($query) {
                $query->select('id', DB::raw('IF(is_berhasil,"sudah","belum")as is_berhasil'), 'pegawai_id', 'proses_id', DB::raw('CONVERT_TZ(created_at, "+00:00", "+08:00") AS updated_at'));
            },
            'proses.kirimpesan.pegawai' => function ($query) {
                $query->select('id', 'nama', 'hp');
            }
        ])->find($wa_pesan_id);

        if (!$dataProses) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        return response()->json($dataProses);
    }

    public function store(KirimPesanRequest $request)
    {
        DB::beginTransaction();
        try {
            $simpanProses = Proses::create($request->all());

            $dataPegawai = Pegawai::get();
            if ($dataPegawai->isEmpty()) {
                DB::rollBack();
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            foreach ($dataPegawai as $i => $dp) {
                $dataKirimPesan = [
                    'user_id' => $request['user_id'],
                    'proses_id' => $simpanProses->id,
                    'pegawai_id' => $dp->id,
                ];
                $dataQuery = KirimPesan::create($dataKirimPesan);
            }
            DB::commit();
            return response()->json($simpanProses, 201);
        } catch (QueryException $exception) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menyimpan data'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $dataQuery = KirimPesan::find($id);
        if (!$dataQuery) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        $dataQuery->update($request->all());
        return response()->json($dataQuery, 200);
    }

    public function show($id)
    {
        $dataQuery = KirimPesan::find($id);
        if (!$dataQuery) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        return response()->json($dataQuery);
    }

    public function destroy($id)
    {
        $dataQuery = Proses::find($id);
        if (!$dataQuery) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        $dataQuery->delete();
        return response()->json(null, 204);
    }

    public function destroyTujuan($id)
    {
        $data = KirimPesan::find($id);
        if (!$data) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        $data->delete();
        return response()->json(['message' => 'berhasil dihapus'], 204);
    }
}
