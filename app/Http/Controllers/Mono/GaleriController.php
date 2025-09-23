<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\GaleriRequest;
use App\Models\Galeri;
use App\Models\KategoriGaleri;
use App\Services\FileStorageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request)
    {
        // Menampilkan Data galeri
        $galeri = Galeri::withoutTrashed()->with(['createdBy', 'updatedBy', 'deletedBy', 'kategoriGaleri']);
        $kategoriGaleri = KategoriGaleri::select('id', 'name')->get();

        if ($request->ajax()) {
            return datatables()->of($galeri)
                ->addColumn('created_by', function ($data) {
                    return optional($data->createdBy)->name ?? '-';
                })
                ->addColumn('updated_by', function ($data) {
                    return optional($data->updatedBy)->name ?? '-';
                })
                ->addColumn('deleted_by', function ($data) {
                    return optional($data->deletedBy)->name ?? '-';
                })
                ->addColumn('kategori_galeri', function ($data) {
                    return optional($data->kategoriGaleri)->name ?? '-';
                })
                ->editColumn('image', function ($data) {
                    // Return full storage URL untuk image
                    if ($data->image) {
                        return \Storage::disk('public')->url($data->image);
                    }
                    return null;
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['created_by', 'updated_by', 'deleted_by', 'aksi', 'kategori_galeri'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/galeri.index', compact(['galeri', 'kategoriGaleri']));
    }

    public function store(GaleriRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // Upload image ke object storage jika ada
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'galeri/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload image: ' . $uploadResult['error']);
                }

                $validatedData['image'] = $uploadResult['path'];
            }

            // Set created_by berdasarkan user yang sedang login
            $validatedData['created_by'] = auth()->id();

            // Create Galeri
            $galeri = Galeri::create($validatedData);

            DB::commit();

                        return response()->json([
                'status' => 200,
                'message' => 'Data galeri berhasil disimpan!',
                'data' => $galeri
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file yang sudah diupload jika ada error
            if (isset($uploadResult) && $uploadResult['success']) {
                $this->fileStorageService->deleteFile($uploadResult['path']);
            }

            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $galeri = Galeri::with(['createdBy', 'updatedBy', 'deletedBy'])->where('id', $id)->first();

            if (!$galeri) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data galeri tidak ditemukan'
                ], 404);
            }

            // Format data untuk frontend
            $galeriData = $galeri->toArray();

            return response()->json([
                'success' => true,
                'galeri' => $galeriData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id, GaleriRequest $request)
    {
        try {
            DB::beginTransaction();

            $galeri = Galeri::findOrFail($id);
            $validatedData = $request->validated();
            $oldImage = $galeri->image;

            // Upload image baru ke object storage jika ada
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'galeri/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload image: ' . $uploadResult['error']);
                }

                $validatedData['image'] = $uploadResult['path'];

                // Hapus image lama jika ada
                if ($oldImage) {
                    $this->fileStorageService->deleteFile($oldImage);
                }
            }

            // Set updated_by berdasarkan user yang sedang login
            $validatedData['updated_by'] = auth()->id();

            // Update galeri
            $galeri->update($validatedData);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Data galeri berhasil diubah'
            ]);
                } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file yang sudah diupload jika ada error
            if (isset($uploadResult) && $uploadResult['success']) {
                $this->fileStorageService->deleteFile($uploadResult['path']);
            }

            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $galeri = Galeri::where('id', $id)->first();

            if (!$galeri) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Data Galeri Tidak Ditemukan'
                ]);
            }

            // Hapus image dari object storage jika ada
            if ($galeri->image) {
                $this->fileStorageService->deleteFile($galeri->image);
            }

            // Set deleted_by berdasarkan user yang sedang login
            $galeri->deleted_by = auth()->id();
            $galeri->save();

            // Hapus data (Soft Delete)
            $galeri->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data Galeri Berhasil Dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
