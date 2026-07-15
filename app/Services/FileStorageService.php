<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileStorageService
{
    protected string $disk;

    protected string $basePath;

    public function __construct()
    {
        $this->disk = 'public';
        $this->basePath = 'uploads/'.date('Y/m');
    }

    /**
     * Upload file ke storage public (persisten via Docker volume pada storage/app/public).
     */
    public function uploadFile(UploadedFile $file, string $directory = 'general', array $options = [])
    {
        try {
            $filename = $this->generateUniqueFilename($file);
            $path = $this->basePath.'/'.$directory.'/'.$filename;

            $uploaded = Storage::disk($this->disk)->putFileAs(
                dirname($path),
                $file,
                basename($path),
                $options
            );

            if (! $uploaded) {
                throw new \Exception('Failed to upload file');
            }

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $path,
                'url' => $this->getFileUrl($path),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'disk' => $this->disk,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Upload image dengan validasi tipe MIME.
     */
    public function uploadImage(UploadedFile $file, string $directory = 'images', array $options = [])
    {
        if (! $file->isValid() || ! in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])) {
            return [
                'success' => false,
                'error' => 'File bukan gambar yang valid',
            ];
        }

        $options['visibility'] = 'public';

        return $this->uploadFile($file, $directory, $options);
    }

    public function deleteFile(string $path): bool
    {
        try {
            if (Storage::disk($this->disk)->exists($path)) {
                return Storage::disk($this->disk)->delete($path);
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function fileExists(string $path): bool
    {
        try {
            return Storage::disk($this->disk)->exists($path);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getFileUrl(string $path): string
    {
        return Storage::disk($this->disk)->url(ltrim($path, '/'));
    }

    protected function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40);

        return $filename.'.'.$extension;
    }

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

    public function getDiskInfo(): array
    {
        return [
            'disk' => $this->disk,
            'driver' => config("filesystems.disks.{$this->disk}.driver"),
            'base_path' => $this->basePath,
        ];
    }

    /**
     * Pindahkan file dari disk local (private) ke disk public.
     */
    public function migrateToCloud(string $localPath, string $cloudPath): array
    {
        try {
            if (! Storage::disk('local')->exists($localPath)) {
                return [
                    'success' => false,
                    'error' => 'Local file not found',
                ];
            }

            $content = Storage::disk('local')->get($localPath);
            $uploaded = Storage::disk($this->disk)->put($cloudPath, $content, 'public');

            if ($uploaded) {
                Storage::disk('local')->delete($localPath);

                return [
                    'success' => true,
                    'path' => $cloudPath,
                    'url' => $this->getFileUrl($cloudPath),
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to copy to public storage',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
