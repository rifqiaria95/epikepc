<?php

namespace App\Http\Controllers\Mono;

use App\Models\Perusahaan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PerusahaanRequest;
use Illuminate\Support\Facades\File;

class PerusahaanController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan Data pegawai
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $perusahaan = Perusahaan::select(['id', 'nama_perusahaan', 'alamat_perusahaan', 'no_telp_perusahaan', 'email_perusahaan', 'created_at']);

            return datatables()->of($perusahaan)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/perusahaan.index');
        // dd($pegawai);
        if ($request->ajax()) {
            return datatables()->of($perusahaan)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/perusahaan.index', compact(['perusahaan']));
    }

    public function store(PerusahaanRequest $request)
    {
        $validatedData = $request->validated();

        try {
            if ($request->hasFile('logo_perusahaan')) {
                $file = $request->file('logo_perusahaan');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/'), $filename);
                $validatedData['logo_perusahaan'] = $filename;
            }

            // Simpan data perusahaan baru
            $perusahaan = Perusahaan::create($validatedData);

            return response()->json([
                'status'  => 200,
                'message' => 'Data berhasil disimpan!',
                'data'    => $perusahaan
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
        $perusahaan = Perusahaan::findOrFail($id);

        return response()->json([
            'success' => true,
            'perusahaan' => $perusahaan
        ]);
    }

    public function update(PerusahaanRequest $request, $id)
    {
        try {
            $perusahaan = Perusahaan::findOrFail($id);

            $validatedData = $request->validated();

            if ($request->hasFile('logo_perusahaan')) {
                if ($perusahaan->logo_perusahaan) {
                    $oldPath = public_path('images/' . $perusahaan->logo_perusahaan);
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }

                $file = $request->file('logo_perusahaan');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/'), $filename);
                $validatedData['logo_perusahaan'] = $filename;
            }

            $perusahaan->update($validatedData);

            return response()->json([
                'status'  => 200,
                'message' => 'Data perusahaan berhasil diperbarui',
                'data'    => $perusahaan
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
        $perusahaan = Perusahaan::find($id);

        // \ActivityLog::addToLog('Menghapus data perusahaan');

        if ($perusahaan) {
            if ($perusahaan->logo_perusahaan) {
                $logoPath = public_path('images/' . $perusahaan->logo_perusahaan);
                if (File::exists($logoPath)) {
                    File::delete($logoPath);
                }
            }
            $perusahaan->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data perusahaan berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data perusahaan tidak ditemukan'
            ]);
        }
    }
}
