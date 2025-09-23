<?php

namespace App\Http\Controllers\Mono;

use Illuminate\Http\Request;
use App\Models\Jabatan;
use App\Http\Requests\JabatanRequest;
use App\Http\Controllers\Controller;

class JabatanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $jabatan = Jabatan::select(['id', 'nama_jabatan', 'deskripsi', 'created_at']);

            return datatables()->of($jabatan)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/jabatan.index');
    }

    public function store(JabatanRequest $request)
    {
        $validatedData = $request->validated();

        $jabatan = Jabatan::create($validatedData);

        return response()->json([
            'success'  => true,
            'message'  => 'Jabatan berhasil ditambahkan!',
            'jabatan' => $jabatan
        ]);
    }

    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);

        return response()->json([
            'success' => true,
            'jabatan' => $jabatan
        ]);
    }

    public function update(JabatanRequest $request, $id)
    {
        $validatedData = $request->validated();

        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Jabatan berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $jabatan = Jabatan::find($id);

        // \ActivityLog::addToLog('Menghapus data kategori');

        if ($jabatan) {
            $jabatan->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data jabatan berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data jabatan tidak ditemukan'
            ]);
        }
    }
}
