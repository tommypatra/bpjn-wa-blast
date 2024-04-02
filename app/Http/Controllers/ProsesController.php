<?php

namespace App\Http\Controllers;

use App\Models\Proses;
use App\Models\WaPesan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProsesController extends Controller
{
    public function index(Request $request)
    {
        $wa_pesan_id = $request->wa_pesan_id;
        $dataProses = WaPesan::with([
            'proses' => function ($query) {
                $query->select('id', 'wa_pesan_id', 'updated_at', DB::raw('CONVERT_TZ(created_at, "+00:00", "+08:00") AS created_at'));
            },
        ])->find($wa_pesan_id);

        if (!$dataProses) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        return response()->json($dataProses);
    }

    public function show($id)
    {
        $dataQuery = Proses::find($id);
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
}
