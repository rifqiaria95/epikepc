<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class FileStorageService
{
    protected $disk;
    protected $basePath;

    public function __construct()
    {
        // Gunakan disk 'public' untuk file yang perlu diakses via web
        $this->disk = 'public';
        $this->basePath = 'uploads/' . date('Y/m');
    }

    /**
     * Upload file ke object storage
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param array $options
     * @return array
     */
    public function uploadFile(UploadedFile $file, string $directory = 'general', array $options = [])
    {
        try {
            // Generate unique filename
            $filename = $this->generateUniqueFilename($file);

            // Set path
            $path = $this->basePath . '/' . $directory . '/' . $filename;

            // Upload file
            $uploaded = Storage::disk($this->disk)->putFileAs(
                dirname($path),
                $file,
                basename($path),
                $options
            );

            if (!$uploaded) {
                throw new \Exception('Gagal mengupload file');
            }

            // Get URL
            $url = $this->getFileUrl($path);

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $path,
                'url' => $url,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'disk' => $this->disk
            ];

        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Upload image dengan optimasi
     *
     * @param UploadedFile $file
     * @param string $directory
     * @param array $options
     * @return array
     */
    public function uploadImage(UploadedFile $file, string $directory = 'images', array $options = [])
    {
        // Validasi file image
        if (!$file->isValid() || !in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])) {
            return [
                'success' => false,
                'error' => 'File bukan gambar yang valid'
            ];
        }

        // Set visibility public untuk image
        $options['visibility'] = 'public';

        return $this->uploadFile($file, $directory, $options);
    }

    /**
     * Hapus file dari storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        try {
            if (Storage::disk($this->disk)->exists($path)) {
                return Storage::disk($this->disk)->delete($path);
            }
            return true;
        } catch (\Exception $e) {
            Log::error('File delete error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cek apakah file exists
     *
     * @param string $path
     * @return bool
     */
    public function fileExists(string $path): bool
    {
        return Storage::disk($this->disk)->exists($path);
    }

    /**
     * Get file URL
     *
     * @param string $path
     * @return string
     */
    public function getFileUrl(string $path): string
    {
        if ($this->disk === 'local' || $this->disk === 'public') {
            return Storage::disk($this->disk)->url($path);
        }

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Generate unique filename
     *
     * @param UploadedFile $file
     * @return string
     */
    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40);

        return $filename . '.' . $extension;
    }

    /**
     * Upload multiple files
     *
     * @param array $files
     * @param string $directory
     * @param array $options
     * @return array
     */
    public function uploadMultipleFiles(array $files, string $directory = 'general', array $options = [])
    {
        $results = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $results[] = $this->uploadFile($file, $directory, $options);
            }
        }

        return $results;
    }

    /**
     * Get disk info
     *
     * @return array
     */
    public function getDiskInfo(): array
    {
        return [
            'disk' => $this->disk,
            'driver' => config("filesystems.disks.{$this->disk}.driver"),
            'base_path' => $this->basePath
        ];
    }

    /**
     * Migrate file dari local ke cloud storage
     *
     * @param string $localPath
     * @param string $cloudPath
     * @return array
     */
    public function migrateToCloud(string $localPath, string $cloudPath): array
    {
        try {
            if (!Storage::disk('local')->exists($localPath)) {
                return [
                    'success' => false,
                    'error' => 'File local tidak ditemukan'
                ];
            }

            $content = Storage::disk('local')->get($localPath);
            $uploaded = Storage::disk($this->disk)->put($cloudPath, $content, 'public');

            if ($uploaded) {
                // Hapus file local setelah berhasil upload ke cloud
                Storage::disk('local')->delete($localPath);

                return [
                    'success' => true,
                    'path' => $cloudPath,
                    'url' => $this->getFileUrl($cloudPath)
                ];
            }

            return [
                'success' => false,
                'error' => 'Gagal upload ke cloud storage'
            ];

        } catch (\Exception $e) {
            Log::error('File migration error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
