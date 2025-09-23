<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Models\MenuGroup;
use Illuminate\Http\Request;

class MenuGroupController extends Controller
{
    public function index(Request $request)
    {
        // Menampilkan Data pegawai
        $menuGroups = MenuGroup::with('menuDetails.subMenuDetails')->get();
        // dd($pegawai);
        if ($request->ajax()) {
            return datatables()->of($menuGroups)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/menu_groups.index', compact(['menuGroups']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'jenis_menu' => 'required|integer',
            'icon'       => 'nullable|string',
            'order'      => 'required|integer'
        ]);

        $menu = MenuGroup::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan!',
            'menu'    => $menu
        ]);
    }

    public function edit($id)
    {
        $menuGroup = MenuGroup::findOrFail($id);

        return response()->json([
            'success' => true,
            'menu' => $menuGroup
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'jenis_menu' => 'required|integer',
            'icon'       => 'nullable|string',
            'order'      => 'required|integer'
        ]);

        $menuGroup = MenuGroup::findOrFail($id);
        $menuGroup->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diperbarui!'
        ]);
    }


    public function destroy($id)
    {
        $menuGroup = MenuGroup::where('id', $id)->first();

        if (!$menuGroup) {
            return response()->json([
                'status' => 404,
                'errors' => 'Data Menu Tidak Ditemukan'
            ]);
        }

        // Hapus data (Soft Delete)
        $menuGroup->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Data Menu Berhasil Dihapus'
        ]);
    }
}

