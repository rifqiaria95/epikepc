<?php

namespace App\Http\Controllers\Mono;

use Illuminate\Http\Request;
use App\Models\Divisi;
use App\Http\Controllers\Controller;
use App\Http\Requests\DivisiRequest;

class DivisiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $divisi = Divisi::select(['id', 'nama_divisi', 'deskripsi', 'created_at']);

            return datatables()->of($divisi)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/divisi.index');
    }

    public function store(DivisiRequest $request)
    {
        $validatedData = $request->validated();

        $divisi = Divisi::create($validatedData);

        return response()->json([
            'success'  => true,
            'message'  => 'Divisi berhasil ditambahkan!',
            'divisi' => $divisi
        ]);
    }

    public function edit($id)
    {
        $divisi = Divisi::findOrFail($id);

        return response()->json([
            'success' => true,
            'divisi' => $divisi
        ]);
    }

    public function update(DivisiRequest $request, $id)
    {
        $validatedData = $request->validated();

        $divisi = Divisi::findOrFail($id);
        $divisi->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Divisi berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $divisi = Divisi::find($id);

        // \ActivityLog::addToLog('Menghapus data kategori');

        if ($divisi) {
            $divisi->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data divisi berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data divisi tidak ditemukan'
            ]);
        }
    }
}
