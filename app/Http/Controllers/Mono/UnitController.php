<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Models\UnitBerat;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query data hanya saat AJAX request
            $satuan = UnitBerat::select(['id', 'nama', 'simbol', 'order', 'created_at'])
                ->orderBy('order', 'asc');

            return datatables()->of($satuan)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/satuan.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:255',
            'simbol' => 'nullable|string',
            'order'  => 'nullable|integer'
        ]);

        $unit = UnitBerat::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Satuan berhasil ditambahkan!',
            'unit'    => $unit
        ]);
    }

    public function edit($id)
    {
        $unit = UnitBerat::findOrFail($id);

        return response()->json([
            'success' => true,
            'unit' => $unit
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255'
        ]);

        $unit = UnitBerat::findOrFail($id);
        $unit->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Satuan berhasil diperbarui!'
        ]);
    }

    public function updateOrder(Request $request)
    {
        $satuan = $request->input('satuan');

        foreach ($satuan as $index => $id) {
            UnitBerat::where('id', $id)->update(['order' => $index + 1]);
        }

        return response()->json(['message' => 'Urutan berhasil diperbarui']);
    }

    public function destroy($id)
    {
        $unit = UnitBerat::find($id);

        // \ActivityLog::addToLog('Menghapus data unit');

        if ($unit) {
            $unit->delete();
            return response()->json([
                'status'    => 200,
                'message'   => 'Sukses! Data unit berhasil dihapus'
            ]);
        } else {
            return response()->json([
                'status'    => 404,
                'errors'    => 'Error! Data unit tidak ditemukan'
            ]);
        }
    }

}

