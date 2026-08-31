<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\KategoriRequest;
use App\Models\Kategori;
use Illuminate\Support\Facades\Cache;

class KategoriController extends Controller
{
    protected function forgetNewsCategoryCache(): void
    {
        Cache::forget('kategori_news_list');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $kategori = Kategori::select(['id', 'name', 'slug', 'created_at']);

            return datatables()->of($kategori)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/kategori.index');
    }

    public function store(KategoriRequest $request)
    {
        $validatedData = $request->validated();

        $kategori = Kategori::create($validatedData);
        $this->forgetNewsCategoryCache();

        return response()->json([
            'success'  => true,
            'message'  => 'Category added successfully!',
            'kategori' => $kategori
        ]);
    }

    public function edit($id)
    {
        $kategori = Kategori::findOrFail($id);

        return response()->json([
            'success' => true,
            'kategori' => $kategori
        ]);
    }

    public function update(KategoriRequest $request, $id)
    {
        $validatedData = $request->validated();

        $kategori = Kategori::findOrFail($id);
        $kategori->update($validatedData);
        $this->forgetNewsCategoryCache();

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully!'
        ]);
    }

    public function destroy($id)
    {
        $kategori = Kategori::find($id);

        // \ActivityLog::addToLog('Menghapus data kategori');

        if ($kategori) {
            $kategori->delete();
            $this->forgetNewsCategoryCache();
            return response()->json([
                'status'    => 200,
                'message'   => 'Category deleted successfully'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Category data not found'
            ]);
        }
    }
}
