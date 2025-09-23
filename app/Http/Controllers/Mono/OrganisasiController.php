<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Services\FileStorageService;
use App\Models\Organisasi;
use Illuminate\Http\Request;
use App\Http\Requests\OrganisasiRequest;
use Illuminate\Support\Facades\DB;

class OrganisasiController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request)
    {
        // Menampilkan Data about
        $organisasi = Organisasi::withoutTrashed();

        if ($request->ajax()) {
            return datatables()->of($organisasi)
                ->addColumn('deleted_by', function ($data) {
                    return optional($data->deleter)->name ?? '-';
                })
                ->editColumn('image', function ($data) {
                    // Data sudah berupa URL lengkap dari accessor model
                    return $data->image;
                })
                ->addColumn('aksi', function ($data) {
                    $button = '';
                    return $button;
                })
                ->rawColumns(['created_by', 'updated_by', 'deleted_by', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal/organisasi.index', compact(['organisasi']));
    }

    public function store(OrganisasiRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // Upload image ke object storage jika ada
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'organisasi/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload image: ' . $uploadResult['error']);
                }

                $validatedData['image'] = $uploadResult['path'];
            }

            // Set created_by berdasarkan user yang sedang login
            $validatedData['created_by'] = auth()->id();

            // Create About
            $organisasi = Organisasi::create($validatedData);

            DB::commit();

                        return response()->json([
                'status' => 200,
                'message' => 'Data organisasi berhasil disimpan!',
                'data' => $organisasi
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
            $organisasi = Organisasi::where('id', $id)->first();

            if (!$organisasi) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data organisasi tidak ditemukan'
                ], 404);
            }

            // Format data untuk frontend
            $organisasiData = $organisasi->toArray();

            return response()->json([
                'success' => true,
                'organisasi' => $organisasiData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update($id, OrganisasiRequest $request)
    {
        try {
            DB::beginTransaction();

            $organisasi = Organisasi::findOrFail($id);
            $validatedData = $request->validated();
            $oldImage = $organisasi->image;

            // Upload image baru ke object storage jika ada
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'organisasi/images'
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
            $organisasi->update($validatedData);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Data organisasi berhasil diubah'
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

            $organisasi = Organisasi::where('id', $id)->first();

            if (!$organisasi) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Data Organisasi Tidak Ditemukan'
                ]);
            }

            // Hapus image dari object storage jika ada
            if ($organisasi->image) {
                $this->fileStorageService->deleteFile($organisasi->image);
            }

            // Set deleted_by berdasarkan user yang sedang login
            $organisasi->deleted_by = auth()->id();
            $organisasi->save();

            // Hapus data (Soft Delete)
            $organisasi->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data Organisasi Berhasil Dihapus'
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
