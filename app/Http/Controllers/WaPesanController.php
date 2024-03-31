<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Proses;
use App\Models\Pegawai;
use App\Models\KirimPesan;
use App\Models\WaPesan;
use Illuminate\Http\Request;
use App\Http\Requests\WaPesanRequest;
use Illuminate\Support\Facades\DB;

class WaPesanController extends Controller
{
    public function index(Request $request)
    {
        $query = WaPesan::with('user')->orderByDesc('id');
        if ($request->has('search')) {
            $query->where('pesan', 'like', '%' . $request->search . '%')
                ->orWhere('judul', 'like', '%' . $request->search . '%');
        }
        $dataQuery = $query->paginate(10);
        $startingNumber = ($dataQuery->currentPage() - 1) * $dataQuery->perPage() + 1;

        $dataQuery->transform(function ($item) use (&$startingNumber) {
            $item->setAttribute('nomor', $startingNumber++);
            // $item->setAttribute('created_at_formatted', $item->created_at->toDateTimeString());
            $item->setAttribute('created_at_formatted', Carbon::parse($item->created_at)->timezone('Asia/Makassar')->toDateTimeString());

            return $item;
        });

        return response()->json($dataQuery);
    }

    public function store(WaPesanRequest $request)
    {

        DB::beginTransaction();
        try {
            $simpanPesan = WaPesan::create($request->all());
            $wa_pesan_id = $simpanPesan->id;

            $dataProses = [
                'user_id' => 1,
                'wa_pesan_id' => $wa_pesan_id,
            ];
            $simpanProses = Proses::create($dataProses);

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
            return response()->json($simpanPesan, 201);
        } catch (QueryException $exception) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menyimpan data'], 500);
        }

        return response()->json($dataQuery, 201);
    }

    public function show($id)
    {
        $dataQuery = WaPesan::find($id);
        if (!$dataQuery) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        return response()->json($dataQuery);
    }

    public function update(WaPesanRequest $request, $id)
    {
        $dataQuery = WaPesan::find($id);
        if (!$dataQuery) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        $dataQuery->update($request->all());
        return response()->json($dataQuery, 200);
    }

    public function destroy($id)
    {
        $dataQuery = WaPesan::find($id);
        if (!$dataQuery) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        $dataQuery->delete();
        return response()->json(null, 204);
    }
}
