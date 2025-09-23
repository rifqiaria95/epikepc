<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Models\KategoriGaleri;
use App\Http\Requests\KategoriGaleriRequest;
use Illuminate\Http\Request;

class KategoriGaleriController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $kategori = KategoriGaleri::select(['id', 'name', 'slug', 'created_at']);

            return datatables()->of($kategori)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/kategori_galeri.index');
    }

    public function store(KategoriGaleriRequest $request)
    {
        $validatedData = $request->validated();

        $kategori = KategoriGaleri::create($validatedData);

        return response()->json([
            'success'  => true,
            'message'  => 'Kategori berhasil ditambahkan!',
            'kategori' => $kategori
        ]);
    }

    public function edit($id)
    {
        $kategori = KategoriGaleri::findOrFail($id);

        return response()->json([
            'success' => true,
            'kategori' => $kategori
        ]);
    }

    public function update(KategoriGaleriRequest $request, $id)
    {
        $validatedData = $request->validated();

        $kategori = KategoriGaleri::findOrFail($id);
        $kategori->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Kategori berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $kategori = KategoriGaleri::find($id);

        // \ActivityLog::addToLog('Menghapus data kategori');

        if ($kategori) {
            $kategori->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data kategori berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data kategori tidak ditemukan'
            ]);
        }
    }
}
