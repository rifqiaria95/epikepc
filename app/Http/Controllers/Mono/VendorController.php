<?php

namespace App\Http\Controllers\Mono;

use App\Models\Vendor;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravolt\Indonesia\Models\City;
use Laravolt\Indonesia\Models\Province;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Optimasi: Query data vendor dengan select field spesifik
            $vendor = Vendor::withoutTrashed()
                ->select(['id', 'nama_vendor', 'alamat_vendor', 'no_telp_vendor', 'email_vendor', 'status', 'created_at']);

            return datatables()->of($vendor)
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['provinsi', 'aksi', 'kota'])
                ->addIndexColumn()
                ->toJson();
        }

        // Cache data provinsi untuk dropdown - data yang jarang berubah
        $provinsi = \Cache::remember('indonesia_provinces_list', 3600, function() {
            return Province::select(['id', 'name'])->get();
        });

        return view('internal/vendor.index', compact(['provinsi']));
    }
}
