<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Knowledge;
use App\Http\Requests\KnowledgeRequest;

class KnowledgeController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan Data pegawai
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $knowledge = Knowledge::select(['id', 'title', 'content', 'category', 'created_at']);

            return datatables()->of($knowledge)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/knowledge.index');
        // dd($pegawai);
        if ($request->ajax()) {
            return datatables()->of($knowledge)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/knowledge.index', compact(['knowledge']));
    }

    public function store(KnowledgeRequest $request)
    {
        $validatedData = $request->validated();

        $knowledge = Knowledge::create($validatedData);

        return response()->json([
            'success'  => true,
            'message'  => 'Knowledge berhasil ditambahkan!',
            'knowledge' => $knowledge
        ]);
    }

    public function edit($id)
    {
        $knowledge = Knowledge::findOrFail($id);

        return response()->json([
            'success' => true,
            'knowledge' => $knowledge
        ]);
    }

    public function update(KnowledgeRequest $request, $id)
    {
        $validatedData = $request->validated();

        $knowledge = Knowledge::findOrFail($id);
        $knowledge->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Knowledge berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $knowledge = Knowledge::find($id);

        // \ActivityLog::addToLog('Menghapus data kategori');

        if ($knowledge) {
            $knowledge->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data knowledge berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data knowledge tidak ditemukan'
            ]);
        }
    }
}
