<?php

namespace App\Http\Controllers\Mono;

use Illuminate\Http\Request;
use App\Models\About;
use App\Http\Requests\AboutRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Services\FileStorageService;

class AboutController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request)
    {
        // Menampilkan Data about
        $about = About::withoutTrashed()->with(['creator', 'updater', 'deleter']);

        // Jika request adalah ajax, maka return data dalam bentuk json
        if ($request->ajax()) {
            return datatables()->of($about)
                ->addColumn('created_by', function ($data) {
                    return optional($data->creator)->name ?? '-';
                })
                ->addColumn('updated_by', function ($data) {
                    return optional($data->updater)->name ?? '-';
                })
                ->addColumn('deleted_by', function ($data) {
                    return optional($data->deleter)->name ?? '-';
                })
                ->editColumn('image', function ($data) {
                    // Kembalikan URL gambar saja; rendering <img> ditangani di frontend about.js
                    if ($data->image) {
                        return $this->fileStorageService->getFileUrl($data->image);
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

        // Jika request bukan ajax, maka return view
        return view('internal/about.index', compact(['about']));
    }

    // Menambahkan data about
    public function store(AboutRequest $request)
    {
        $validatedData = $request->validated();

        try {
            DB::beginTransaction();

            // Upload image ke object storage jika ada, jika tidak ada maka throw error
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'about/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload image: ' . $uploadResult['error']);
                }

                $validatedData['image'] = $uploadResult['path'];
            }

            // Set created_by berdasarkan user yang sedang login, jika tidak ada maka throw error
            $validatedData['created_by'] = auth()->id();

            // Create About
            $about = About::create($validatedData);

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data about berhasil disimpan!',
                'data' => $about
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file yang sudah diupload jika ada error, jika tidak ada maka throw error
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

    // Mengedit data about
    public function edit($id)
    {
        try {
            $about = About::with(['creator', 'updater', 'deleter'])->where('id', $id)->first();

            if (!$about) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Data about tidak ditemukan'
                ], 404);
            }

            // Format data untuk frontend, jika tidak ada maka throw error
            $aboutData = $about->toArray();

            return response()->json([
                'success' => true,
                'about' => $aboutData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Mengubah data about
    public function update($id, AboutRequest $request)
    {
        try {
            DB::beginTransaction();

            $about = About::findOrFail($id);
            $validatedData = $request->validated();
            $oldImage = $about->image;

            // Upload image baru ke object storage jika ada, jika tidak ada maka throw error
            if ($request->hasFile('image')) {
                $uploadResult = $this->fileStorageService->uploadImage(
                    $request->file('image'),
                    'about/images'
                );

                if (!$uploadResult['success']) {
                    throw new \Exception('Gagal upload image: ' . $uploadResult['error']);
                }

                $validatedData['image'] = $uploadResult['path'];

                // Hapus image lama jika ada, jika tidak ada maka throw error
                if ($oldImage) {
                    $this->fileStorageService->deleteFile($oldImage);
                }
            }

            // Set updated_by berdasarkan user yang sedang login, jika tidak ada maka throw error
            $validatedData['updated_by'] = auth()->id();

            // Update about
            $about->update($validatedData);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Data about berhasil diubah'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file yang sudah diupload jika ada error, jika tidak ada maka throw error
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

    // Menghapus data about
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $about = About::where('id', $id)->first();

            if (!$about) {
                return response()->json([
                    'status' => 404,
                    'errors' => 'Data About Tidak Ditemukan'
                ]);
            }

            // Hapus image dari object storage jika ada, jika tidak ada maka throw error
            if ($about->image) {
                $this->fileStorageService->deleteFile($about->image);
            }

            // Set deleted_by berdasarkan user yang sedang login, jika tidak ada maka throw error
            $about->deleted_by = auth()->id();
            $about->save();

            // Hapus data (Soft Delete), jika tidak ada maka throw error
            $about->delete();

            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Data About Berhasil Dihapus'
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
