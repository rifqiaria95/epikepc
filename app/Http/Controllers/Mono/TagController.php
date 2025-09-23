<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $tag = Tag::select(['id', 'name', 'slug', 'created_at']);

            return datatables()->of($tag)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/tag.index');
    }

    public function store(TagRequest $request)
    {
        $validatedData = $request->validated();

        $tag = Tag::create($validatedData);

        return response()->json([
            'success'  => true,
            'message'  => 'Tags berhasil ditambahkan!',
            'tag' => $tag
        ]);
    }

    public function edit($id)
    {
        $tag = Tag::findOrFail($id);

        return response()->json([
            'success' => true,
            'tag' => $tag
        ]);
    }

    public function update(TagRequest $request, $id)
    {
        $validatedData = $request->validated();

        $tag = Tag::findOrFail($id);
        $tag->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Tags berhasil diperbarui!'
        ]);
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);

        // \ActivityLog::addToLog('Menghapus data tag');

        if ($tag) {
            $tag->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data tag berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data tag tidak ditemukan'
            ]);
        }
    }
}
