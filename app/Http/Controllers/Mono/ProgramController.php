<?php

namespace App\Http\Controllers\Mono;

use Illuminate\Http\Request;
use App\Http\Requests\ProgramRequest;
use App\Models\Program;
use App\Models\JenisProgram;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan Data pelanggan
        $program       = Program::withoutTrashed()->with('jenisProgram', 'user');
        $jenis_program = JenisProgram::where('status', 'true')->get();
        // Cache data user untuk dropdown
        $user = \Cache::remember('users_list_programs', 900, function() {
            return User::select(['id', 'name', 'email'])
                ->where('active', true)
                ->get();
        });
        // dd($pelanggan);
        if ($request->ajax()) {
            return datatables()->of($program)
                ->addColumn('jenis_program', function ($data) {
                    return $data->jenisProgram->nama_jenis_program;
                })
                ->addColumn('created_by', function ($data) {
                    return $data->user->name;
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['jenis_program', 'aksi', 'created_by'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/daftar_program.index', compact(['program', 'jenis_program', 'user']));
    }

    public function store(ProgramRequest $request)
    {
        $validatedData = $request->validated();
        // Set created_by berdasarkan user yang sedang login
        $validatedData['created_by'] = auth()->id();

        try {
            $program = Program::create($validatedData);


            return response()->json([
                'status'  => 200,
                'message' => 'Data berhasil disimpan!',
                'data'    => $program
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
        $program = Program::with('jenisProgram', 'user')->where('id', $id)->first();

        if (!$program) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($program);
    }

    public function update($id, ProgramRequest $request)
    {
        try {
            $program = Program::findOrFail($id);

            $validatedData = $request->validated();
            $program->update($validatedData);

            return response()->json([
                'status'  => 200,
                'message' => 'Data pelanggan berhasil diperbarui',
                'data'    => $program
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
        $program = Program::where('id', $id)->first();

        if (!$program) {
            return response()->json([
                'status' => 404,
                'errors' => 'Data Program Tidak Ditemukan'
            ]);
        }

        // Hapus data (Soft Delete)
        $program->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Data Program Berhasil Dihapus'
        ]);
    }

    public function getProgram($id)
    {
        try {
            $program = Program::select(['id', 'name', 'status'])
                ->where('id', $id)
                ->first();

            if (!$program) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Program tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'data' => $program
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
