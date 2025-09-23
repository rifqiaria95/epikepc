<?php

namespace App\Http\Controllers\Mono;

use App\Models\Divisi;
use App\Models\Departemen;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\DepartemenRequest;

class DepartemenController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query data dengan eager loading yang efisien
            $departemen = Departemen::select(['id', 'nama_departemen', 'deskripsi', 'id_divisi', 'created_at'])
                ->with(['divisi:id,nama_divisi']);

            return datatables()->of($departemen)
                ->addColumn('divisi', function ($data) {
                    return $data->divisi->nama_divisi ?? '-';
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['divisi', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        // Cache data divisi untuk dropdown
        $divisi = \Cache::remember('divisions_list', 1800, function() {
            return Divisi::select(['id', 'nama_divisi'])->get();
        });

        return view('internal/departemen.index', compact(['divisi']));
    }

    public function store(DepartemenRequest $request)
    {
        $validatedData = $request->validated();

        $departemen = Departemen::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Departemen berhasil ditambahkan!',
            'departemen'    => $departemen
        ]);
    }

    public function edit($id)
    {
        $departemen = Departemen::find($id); // Gunakan find() dulu, bukan findOrFail()

        if (!$departemen) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'departemen' => $departemen
        ]);
    }


    public function update(DepartemenRequest $request, $id)
    {
        $validatedData = $request->validated();

        $departemen = Departemen::findOrFail($id);
        $departemen->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Departemen berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $departemen = Departemen::find($id);

        // \ActivityLog::addToLog('Menghapus data menu_detail');

        if ($departemen) {
            $departemen->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data departemen berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data departemen tidak ditemukan'
            ]);
        }
    }

    public function getByDivisi($id_divisi)
    {
        $departemen = Departemen::where('id_divisi', $id_divisi)->get();
        return response()->json($departemen);
    }
}
