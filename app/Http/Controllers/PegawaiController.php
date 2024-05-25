<?php

namespace App\Http\Controllers;

use App\Http\Requests\PegawaiRequest;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $dataQuery = Pegawai::with('user')->orderBy('nama', 'asc');
        if ($request->has('search')) {
            $dataQuery->where('nama', 'like', '%' . $request->search . '%')
                ->orWhere('hp', 'like', '%' . $request->search . '%');
        }

        $paging = 25;
        if ($request->has('paging')) {
            $paging = $request->paging;
        }
        $dataQuery = $dataQuery->paginate($paging);

        $startingNumber = ($dataQuery->currentPage() - 1) * $dataQuery->perPage() + 1;

        $dataQuery->transform(function ($item) use (&$startingNumber) {
            $item->setAttribute('nomor', $startingNumber++);
            // $item->setAttribute('created_at_formatted', $item->created_at->toDateTimeString());
            $item->setAttribute('created_at_formatted', Carbon::parse($item->created_at)->timezone('Asia/Makassar')->toDateTimeString());

            return $item;
        });

        return response()->json($dataQuery);
    }

    public function store(PegawaiRequest $request)
    {
        $dataQuery = Pegawai::create($request->all());
        return response()->json($dataQuery, 201);
    }

    public function show($id)
    {
        $dataQuery = Pegawai::find($id);
        if (!$dataQuery) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        return response()->json($dataQuery);
    }

    public function update(PegawaiRequest $request, $id)
    {
        $dataQuery = Pegawai::find($id);
        if (!$dataQuery) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        $dataQuery->update($request->all());
        return response()->json($dataQuery, 200);
    }

    public function updateHp(Request $request, $id)
    {
        $dataQuery = Pegawai::find($id);
        if (!$dataQuery) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        $dataQuery->update($request->all());
        return response()->json($dataQuery, 200);
    }

    public function destroy($id)
    {
        $dataQuery = Pegawai::find($id);
        if (!$dataQuery) {
            return response()->json(['message' => 'data tidak ditemukan'], 404);
        }
        $dataQuery->delete();
        return response()->json(null, 204);
    }
}
