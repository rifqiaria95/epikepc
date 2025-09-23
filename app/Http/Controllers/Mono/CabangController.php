<?php

namespace App\Http\Controllers\Mono;

use App\Models\Cabang;
use App\Models\Perusahaan;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CabangRequest;

class CabangController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan Data cabang
        $cabang = Cabang::with('perusahaan');

        // Cache data perusahaan untuk dropdown
        $perusahaan = \Cache::remember('perusahaan_list_cabang', 1800, function() {
            return Perusahaan::select(['id', 'nama_perusahaan'])->get();
        });

        if ($request->ajax()) {
            return datatables()->of($cabang)
                ->addColumn('perusahaan', function ($data) {
                    return $data->perusahaan->nama_perusahaan ?? '-';
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['perusahaan', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/cabang.index', compact(['cabang', 'perusahaan']));
    }

    public function store(CabangRequest $request)
    {
        $validatedData = $request->validated();

        $cabang = Cabang::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Cabang berhasil ditambahkan!',
            'cabang' => $cabang
        ]);
    }

    public function edit($id)
    {
        $cabang = Cabang::find($id);

        if (!$cabang) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'cabang' => $cabang
        ]);
    }

    public function update(CabangRequest $request, $id)
    {
        $validatedData = $request->validated();

        $cabang = Cabang::findOrFail($id);
        $cabang->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Cabang berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $cabang = Cabang::find($id);

        if ($cabang) {
            $cabang->delete();
            return response()->json([
                'status' => 200,
                'message' => 'Sukses! Data cabang berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'errors' => 'Error! Data cabang tidak ditemukan'
            ]);
        }
    }

    public function getPerusahaan()
    {
        // Cache API response untuk perusahaan
        $perusahaan = \Cache::remember('api_perusahaan_list', 1800, function() {
            return Perusahaan::select(['id', 'nama_perusahaan'])->get();
        });
        return response()->json($perusahaan);
    }

    public function getByPerusahaan($id_perusahaan)
    {
        $cabang = Cabang::where('id_perusahaan', $id_perusahaan)->get();
        return response()->json($cabang);
    }
}
