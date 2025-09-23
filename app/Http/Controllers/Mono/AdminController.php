<?php

namespace App\Http\Controllers\Mono;

use DB;
use App\Models\User;
use App\Models\Gudang;
use App\Models\Pegawai;
use App\Models\MenuDetail;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function getDeletedRecords(Request $request)
    {
        $pegawai = Pegawai::onlyTrashed()->select(DB::raw('CAST(id AS VARCHAR) as id'), 'nm_pegawai as nama', 'deleted_at', DB::raw("'Pegawai' as kategori"));
        $menu    = MenuDetail::onlyTrashed()->select(DB::raw('CAST(id AS VARCHAR) as id'), 'name as nama', 'deleted_at', DB::raw("'Menu' as kategori"));
        $users   = User::onlyTrashed()->select(DB::raw('CAST(id AS VARCHAR) as id'), 'name as nama', 'deleted_at', DB::raw("'User' as kategori"));
        $gudang  = Gudang::onlyTrashed()->select(DB::raw('CAST(id AS VARCHAR) as id'), 'nama_gudang as nama', 'deleted_at', DB::raw("'Gudang' as kategori"));
        $item    = Item::onlyTrashed()->select(DB::raw('CAST(id AS VARCHAR) as id'), 'nm_item as nama', 'deleted_at', DB::raw("'Item' as kategori"));

        // Gabungkan semua query
        $deletedData = 
        $pegawai
        ->union($menu)
        ->union($users)
        ->union($gudang)
        ->union($item);

        if ($request->ajax()) {
            return datatables()->of($deletedData)
                ->addColumn('aksi', function ($row) {
                    return '<button class="btn btn-success btn-sm restore-record" data-id="' . $row->id . '" data-kategori="' . $row->kategori . '">Restore</button>
                    <button class="btn btn-danger btn-sm delete-record" data-id="' . $row->id . '" data-kategori="' . $row->kategori . '">Delete</button>';
                })
                ->rawColumns(['aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('trash.index', compact(['deletedData']));
    }

    public function restoreRecord(Request $request)
    {
        $kategori = $request->kategori;
        $id = $request->id;

        switch ($kategori) {
            case 'Pegawai':
                Pegawai::onlyTrashed()->where('id', $id)->restore();
                break;
            case 'Menu':
                MenuDetail::onlyTrashed()->where('id', $id)->restore();
                break;
            case 'User':
                User::onlyTrashed()->where('id', $id)->restore();
                break;
            case 'Gudang':
                Gudang::onlyTrashed()->where('id', $id)->restore();
                break;
            case 'Item':
                Item::onlyTrashed()->where('id', $id)->restore();
                break;
        }

        return response()->json([
            'status'  => 200,
            'message' => 'Data berhasil dikembalikan!'
        ]);
    }

    public function deleteRecord(Request $request)
    {
        $kategori = $request->kategori;
        $id = $request->id;

        try {
            switch ($kategori) {
                case 'Pegawai':
                    $deleted = Pegawai::onlyTrashed()->where('id', $id)->forceDelete();
                    break;
                case 'Menu':
                    $deleted = MenuDetail::onlyTrashed()->where('id', $id)->forceDelete();
                    break;
                case 'User':
                    $deleted = User::onlyTrashed()->where('id', $id)->forceDelete();
                    break;
                case 'Gudang':
                    $deleted = Gudang::onlyTrashed()->where('id', $id)->forceDelete();
                    break;
                case 'Item':
                    $deleted = Item::onlyTrashed()->where('id', $id)->forceDelete();
                    break;
                default:
                    return response()->json([
                        'status' => 400,
                        'message' => 'Kategori tidak valid.'
                    ], 400);
            }

            if ($deleted) {
                return response()->json([
                    'status' => 200,
                    'message' => 'Data berhasil dihapus permanen!'
                ]);
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data tidak ditemukan atau sudah dihapus permanen.'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
