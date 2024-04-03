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
        $proses_id = $request->proses_id;

        $dataQuery = kirimpesan::select('id', DB::raw('IF(is_berhasil=1,"sudah",IF(is_berhasil=0,"gagal","belum"))as is_berhasil'), 'pegawai_id', 'proses_id', DB::raw('CONVERT_TZ(updated_at, "+00:00", "+08:00") AS updated_at'))
            ->with([
                'pegawai' => function ($dataQuery) {
                    $dataQuery->select('id', 'nama', 'hp');
                }
            ])->where('proses_id', $proses_id);

        if ($request->has('search')) {
            $dataQuery->whereHas('pegawai', function ($dataQuery) use ($request) {
                $dataQuery->where('nama', 'like', '%' . $request->search . '%')
                    ->orWhere('hp', 'like', '%' . $request->search . '%');
            });
        }

        $paging = 25;
        if ($request->has('paging')) {
            $paging = $request->paging;
        }

        $dataQuery = $dataQuery->paginate($paging);
        $startingNumber = ($dataQuery->currentPage() - 1) * $dataQuery->perPage() + 1;

        $dataQuery->transform(function ($item) use (&$startingNumber) {
            $item->setAttribute('nomor', $startingNumber++);
            $item->setAttribute('created_at_formatted', Carbon::parse($item->created_at)->timezone('Asia/Makassar')->toDateTimeString());
            return $item;
        });

        return response()->json($dataQuery);
    }

    public function store(KirimPesanRequest $request)
    {
        DB::beginTransaction();
        try {
            $simpanProses = Proses::create($request->all());

            $dataPegawai = Pegawai::where('is_aktif', true)->get();
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
        $data = KirimPesan::find($id);
        if (!$data) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        $data->delete();
        return response()->json(['message' => 'berhasil dihapus'], 204);
    }
}
