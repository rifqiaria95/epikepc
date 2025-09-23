<?php

namespace App\Http\Controllers\Mono;

use App\Models\Pelanggan;
use App\Models\Kendaraan;
use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePelangganRequest;
use App\Http\Requests\UpdatePelangganRequest;

class PelangganController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query data pelanggan dengan select field spesifik
            $pelanggan = Pelanggan::withoutTrashed()
                ->select(['id', 'nama_pelanggan', 'alamat_pelanggan', 'no_telp_pelanggan', 'email_pelanggan', 'status', 'created_at']);

            return datatables()->of($pelanggan)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['provinsi', 'aksi', 'kota'])
                ->addIndexColumn()
                ->toJson();
        }

        // Cache data dropdown yang jarang berubah
        $provinsi = \Cache::remember('indonesia_provinces_list', 3600, function() {
            return Province::select(['id', 'name'])->get();
        });
        $kendaraan = \Cache::remember('kendaraan_list', 1800, function() {
            return Kendaraan::select(['id', 'nama_kendaraan', 'type'])->get();
        });

        return view('internal/pelanggan.index', compact(['provinsi', 'kendaraan']));
    }

    public function store(StorePelangganRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $pelanggan = Pelanggan::create($request->all());

            return response()->json([
                'status'  => 200,
                'message' => 'Data berhasil disimpan!',
                'data'    => $pelanggan
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
        $pelanggan = Pelanggan::with('kendaraan')->where('id', $id)->first();

        if (!$pelanggan) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($pelanggan);
    }

    public function update($id, UpdatePelangganRequest $request)
    {
        try {
            $pelanggan = Pelanggan::findOrFail($id);

            $validatedData = $request->validated();
            $pelanggan->update($validatedData);

            return response()->json([
                'status'  => 200,
                'message' => 'Data pelanggan berhasil diperbarui',
                'data'    => $pelanggan
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
        $pelanggan = Pelanggan::where('id', $id)->first();

        if (!$pelanggan) {
            return response()->json([
                'status' => 404,
                'errors' => 'Data Pelanggan Tidak Ditemukan'
            ]);
        }

        // Hapus data (Soft Delete)
        $pelanggan->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Data Pelanggan Berhasil Dihapus'
        ]);
    }

    public function getKotaByProvinsi($id_provinsi)
    {
        // Cari province_code berdasarkan id_provinsi
        $provinsi = Province::where('id_provinsi', $id_provinsi)->first();

        if (!$provinsi) {
            return response()->json(['error' => 'Provinsi tidak ditemukan'], 404);
        }

        // Cari kota berdasarkan province_code
        $kota = City::where('province_code', $provinsi->code)->get();

        return response()->json($kota);
    }
}
