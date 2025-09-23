<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProgramRequirement;
use App\Http\Requests\ProgramReqRequest;
use App\Models\Program;

class ProgramReqController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan Data pelanggan
        $programreq       = ProgramRequirement::withoutTrashed()->with('program');
        // Cache data program untuk dropdown
        $program = \Cache::remember('programs_list_requirements', 1800, function() {
            return Program::select(['id', 'name', 'status'])
                ->where('status', 'active')
                ->get();
        });
        // dd($pelanggan);
        if ($request->ajax()) {
            return datatables()->of($programreq)
                ->addColumn('program', function ($data) {
                    return $data->program ? $data->program->name : '-';
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['program', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/program_req.index', compact(['programreq', 'program']));
    }

    public function store(ProgramReqRequest $request)
    {
        $validatedData = $request->validated();

        try {
            $programreq = ProgramRequirement::create($validatedData);


            return response()->json([
                'status'  => 200,
                'message' => 'Data berhasil disimpan!',
                'data'    => $programreq
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
        $programreq = ProgramRequirement::with('program')->where('id', $id)->first();

        if (!$programreq) {
            return response()->json(['error' => 'Data tidak ditemukan'], 404);
        }

        return response()->json($programreq);
    }

    public function update($id, ProgramReqRequest $request)
    {
        try {
            $programreq = ProgramRequirement::findOrFail($id);

            $validatedData = $request->validated();
            $programreq->update($validatedData);

            return response()->json([
                'status'  => 200,
                'message' => 'Data pelanggan berhasil diperbarui',
                'data'    => $programreq
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
        $programreq = ProgramRequirement::where('id', $id)->first();

        if (!$programreq) {
            return response()->json([
                'status' => 404,
                'errors' => 'Data Program Tidak Ditemukan'
            ]);
        }

        // Hapus data (Soft Delete)
        $programreq->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Data Program Berhasil Dihapus'
        ]);
    }
}
