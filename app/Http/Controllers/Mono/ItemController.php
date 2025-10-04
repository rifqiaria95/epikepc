<?php

namespace App\Http\Controllers\Mono;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Models\UnitBerat;
use App\Models\Kategori;
use App\Services\FileStorageService;


class ItemController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }
    public function index(Request $request)
    {
        // Optimasi: Cache data dropdown dan gunakan select untuk field yang diperlukan
        $unit_berat = Cache::remember('unit_berat_list', 1800, function() {
            return UnitBerat::select(['id', 'nama'])->get();
        });
        $kategori = Cache::remember('kategori_list', 1800, function() {
            return Kategori::select(['id', 'nama_kategori'])->get();
        });

        if ($request->ajax()) {
            // Optimasi: Select hanya field yang diperlukan dan eager load dengan field spesifik
            $items = Item::select(['id', 'nama_item', 'kd_item', 'harga_item', 'stok_item', 'id_unit_berat', 'id_kategori', 'foto_item'])
                ->with([
                    'unit_berat:id,nama',
                    'kategori:id,nama_kategori'
                ]);

            return datatables()->of($items)
                ->addColumn('stok_satuan', function ($item) {
                    return $item->unit_berat ? $item->unit_berat->nama : '-';
                })
                ->addColumn('kategori', function ($item) {
                    return $item->kategori ? $item->kategori->nama_kategori : '-';
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['aksi', 'stok_satuan', 'kategori'])
                ->addIndexColumn()
                ->make(true);
        }

        // Kirim data ke view
        return view('internal/item.index', compact('unit_berat', 'kategori'));
    }

    public function store(StoreItemRequest $request)
    {
        $validatedData = $request->validated();

        try {
            // Ambil kode terakhir dari database
            $lastItem = Item::latest('kd_item')->first();
            $lastNumber = $lastItem ? intval(substr($lastItem->kd_item, 4)) : 0;

            // Buat kode baru dengan format ITM-000001
            $newCode = 'ITM-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);

            // Tambahkan kode ke request
            $request->merge([
                'kd_item'  => $newCode,
            ]);

            // Simpan data item baru
            $item = Item::create($request->all());

            // Upload foto item ke object storage jika ada
            if ($request->hasFile('foto_item')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('foto_item'),
                    'items/photos'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload foto item: ' . $uploadResult['error']);
                }

                $item->foto_item = $uploadResult['path'];
                $item->save();
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Data berhasil disimpan!',
                'data'    => $item
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
        // Optimasi: Gabungkan with dan select field yang diperlukan
        $item = Item::with([
                'kategori:id,nama_kategori',
                'unit_berat:id,nama'
            ])
            ->select(['id', 'nama_item', 'kd_item', 'harga_item', 'stok_item', 'id_unit_berat', 'id_kategori', 'foto_item'])
            ->where('id', $id)
            ->first();

        return response()->json($item);
    }

    public function update($id, UpdateItemRequest $request)
    {
        try {
            $item = Item::findOrFail($id);

            $validatedData             = $request->validated();

            $item->update($validatedData);

            // Upload foto item baru ke object storage jika ada
            if ($request->hasFile('foto_item')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('foto_item'),
                    'items/photos'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload foto item: ' . $uploadResult['error']);
                }

                // Hapus foto lama jika ada
                if ($item->foto_item) {
                    $this->fileStorageService->deleteFile($item->foto_item);
                }

                $item->foto_item = $uploadResult['path'];
                $item->save();
            }

            return response()->json([
                'status'  => 200,
                'message' => 'Data item berhasil diperbarui',
                'data'    => $item
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
        $item = Item::where('id', $id)->first();

        if (!$item) {
            return response()->json([
                'status' => 404,
                'errors' => 'Data Item Tidak Ditemukan'
            ]);
        }

        // Hapus foto jika ada
        if ($item->foto_item) {
            $path = 'images/' . $item->foto_item;
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        // Hapus data (Soft Delete)
        $item->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Data Pegawai Berhasil Dihapus'
        ]);
    }

}
