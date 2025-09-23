<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\JenisProgramRequest;
use App\Models\JenisProgram;

class JenisProgramController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan Data pegawai
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $jenis_program = JenisProgram::select(['id', 'nama_jenis_program', 'deskripsi_jenis_program', 'gambar_jenis_program', 'created_at']);

            return datatables()->of($jenis_program)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/jenis_program.index');
        // dd($pegawai);
        if ($request->ajax()) {
            return datatables()->of($jenis_program)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/jenis_program.index', compact(['jenis_program']));
    }

    public function store(JenisProgramRequest $request)
    {
        $validatedData = $request->validated();

        try {
            if ($request->hasFile('gambar_jenis_program')) {
                $file = $request->file('gambar_jenis_program');
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('images/'), $filename);
                $validatedData['gambar_jenis_program'] = $filename;
            }

            // Simpan data perusahaan baru
            $jenis_program = JenisProgram::create($validatedData);

            return response()->json([
                'status'  => 200,
                'message' => 'Data berhasil disimpan!',
                'data'    => $jenis_program
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
        $jenis_program = JenisProgram::findOrFail($id);

        return response()->json([
            'success' => true,
            'jenis_program' => $jenis_program
        ]);
    }

    public function update(JenisProgramRequest $request, $id)
    {
        $validatedData = $request->validated();

        $jenis_program = JenisProgram::findOrFail($id);
        $jenis_program->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Jenis Program berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $jenis_program = JenisProgram::find($id);

        // \ActivityLog::addToLog('Menghapus data kategori');

        if ($jenis_program) {
            $jenis_program->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data jenis program berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data jenis program tidak ditemukan'
            ]);
        }
    }
}
