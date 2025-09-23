<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Models\VisiMisi;
use Illuminate\Http\Request;
use App\Services\FileStorageService;
use App\Http\Requests\VisiMisiRequest;
use Illuminate\Support\Facades\DB;

class VisiMisiController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request)
    {
        // Menampilkan Data about
        $visiMisi = VisiMisi::withoutTrashed()->with(['createdBy', 'updatedBy', 'deletedBy']);

        if ($request->ajax()) {
            return datatables()->of($visiMisi)
                ->addColumn('created_by', function ($data) {
                    return optional($data->createdBy)->name ?? '-';
                })
                ->addColumn('updated_by', function ($data) {
                    return optional($data->updatedBy)->name ?? '-';
                })
                ->addColumn('deleted_by', function ($data) {
                    return optional($data->deletedBy)->name ?? '-';
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
                ->rawColumns(['created_by', 'updated_by', 'deleted_by', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/visi-misi.index', compact(['visiMisi']));
    }

    public function store(VisiMisiRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // Upload image ke object storage jika ada
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'visi-misi/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload image: ' . $uploadResult['error']);
                }

                $validatedData['image'] = $uploadResult['path'];
            }

            // Set created_by berdasarkan user yang sedang login
            $validatedData['created_by'] = auth()->id();

            // Create About
            $visiMisi = VisiMisi::create($validatedData);

            DB::commit();

                        return response()->json([
                'status' => 200,
                'message' => 'Data visi misi berhasil disimpan!',
                'data' => $visiMisi
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
            $visiMisi = VisiMisi::with(['createdBy', 'updatedBy', 'deletedBy'])->where('id', $id)->first();

            if (!$visiMisi) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data visi misi tidak ditemukan'
                ], 404);
            }

            // Format data untuk frontend
            $visiMisiData = $visiMisi->toArray();

            return response()->json([
                'success' => true,
                'visiMisi' => $visiMisiData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id, VisiMisiRequest $request)
    {
        try {
            DB::beginTransaction();

            $visiMisi = VisiMisi::findOrFail($id);
            $validatedData = $request->validated();
            $oldImage = $visiMisi->image;

            // Upload image baru ke object storage jika ada
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'visi-misi/images'
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

            // Update about
            $visiMisi->update($validatedData);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Data visi misi berhasil diubah'
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

            $visiMisi = VisiMisi::where('id', $id)->first();

            if (!$visiMisi) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Data Visi Misi Tidak Ditemukan'
                ]);
            }

            // Hapus image dari object storage jika ada
            if ($visiMisi->image) {
                $this->fileStorageService->deleteFile($visiMisi->image);
            }

            // Set deleted_by berdasarkan user yang sedang login
            $visiMisi->deleted_by = auth()->id();
            $visiMisi->save();

            // Hapus data (Soft Delete)
            $visiMisi->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data Visi Misi Berhasil Dihapus'
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
