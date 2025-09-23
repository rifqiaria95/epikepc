<?php

namespace App\Http\Controllers\Mono;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\GudangRequest;
use App\Models\Gudang;
use Illuminate\Support\Facades\File;

class GudangController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan Data pegawai
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $gudang = Gudang::select(['id', 'nama_gudang', 'alamat_gudang', 'kapasitas', 'status', 'created_at']);

            return datatables()->of($gudang)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/gudang.index');
        // dd($pegawai);
        if ($request->ajax()) {
            return datatables()->of($gudang)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/gudang.index', compact(['gudang']));
    }

    public function store(GudangRequest $request)
    {
        $validatedData = $request->validated();

        try {
            if ($request->hasFile('foto_gudang')) {
                $file = $request->file('foto_gudang');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/'), $filename);
                $validatedData['foto_gudang'] = $filename;
            }

            // Simpan data gudang baru
            $gudang = Gudang::create($validatedData);

            return response()->json([
                'status'  => 200,
                'message' => 'Data berhasil disimpan!',
                'data'    => $gudang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        $gudang = Gudang::findOrFail($id);

        return response()->json([
            'success' => true,
            'gudang' => $gudang
        ]);
    }

    public function update(GudangRequest $request, $id)
    {
        try {
            $gudang = Gudang::findOrFail($id);

            $validatedData = $request->validated();

            if ($request->hasFile('foto_gudang')) {
                if ($gudang->foto_gudang) {
                    $oldPath = public_path('images/' . $gudang->foto_gudang);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                $file = $request->file('foto_gudang');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/'), $filename);
                $validatedData['foto_gudang'] = $filename;
            }

            $gudang->update($validatedData);

            return response()->json([
                'status'  => 200,
                'message' => 'Data gudang berhasil diperbarui',
                'data'    => $gudang
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $gudang = Gudang::find($id);

        // \ActivityLog::addToLog('Menghapus data gudang');

        if ($gudang) {
            $gudang->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data gudang berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data gudang tidak ditemukan'
            ]);
        }
    }
}
