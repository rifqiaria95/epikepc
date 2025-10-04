<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Google\Cloud\Storage\StorageClient;

class FileStorageService
{
    protected $disk;
    protected $basePath;
    protected $gcsClient;
    protected $bucket;

    public function __construct()
    {
        // Gunakan disk 'gcs' untuk file yang perlu diakses via web
        $this->disk = 'gcs';
        $this->basePath = 'uploads/' . date('Y/m');
        
        // Initialize GCS client
        $this->initializeGcsClient();
    }
    
    /**
     * Initialize Google Cloud Storage client
     */
    protected function initializeGcsClient()
    {
        try {
            $projectId = config('filesystems.disks.gcs.project_id');
            $keyFile = config('filesystems.disks.gcs.key_file');
            $bucketName = config('filesystems.disks.gcs.bucket');
            
            
            // Validate and fix key file path
            if (empty($keyFile) || !is_string($keyFile)) {
                return;
            }
            
            // Check if it's a hash (invalid path)
            if (preg_match('/^[a-f0-9]{40}$/', $keyFile)) {
                return;
            }
            
            // Convert relative path to absolute path
            if (!file_exists($keyFile)) {
                $keyFile = base_path($keyFile);
            }
            
            if (file_exists($keyFile)) {
                $this->gcsClient = new StorageClient([
                    'projectId' => $projectId,
                    'keyFilePath' => $keyFile,
                ]);
                
                $this->bucket = $this->gcsClient->bucket($bucketName);
            }
        } catch (\Exception $e) {
            // Silent fail - will fallback to local storage
        }
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

            // Try to upload to GCS first
            if ($this->bucket) {
                $gcsResult = $this->uploadToGcs($file, $path, $options);
                if ($gcsResult['success']) {
                    return $gcsResult;
                }
            }

            // Fallback to local public storage when GCS is not available
            $fallbackDisk = 'public';
            $uploaded = Storage::disk($fallbackDisk)->putFileAs(
                dirname($path),
                $file,
                basename($path),
                $options
            );

            if (!$uploaded) {
                throw new \Exception('Gagal mengupload file');
            }

            // Get URL (public disk)
            $publicBaseUrl = rtrim(config('filesystems.disks.public.url'), '/');
            $url = $publicBaseUrl . '/' . ltrim($path, '/');

            return [
                'success' => true,
                'filename' => $filename,
                'path' => $path,
                'url' => $url,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'disk' => $fallbackDisk
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Upload file directly to Google Cloud Storage
     *
     * @param UploadedFile $file
     * @param string $path
     * @param array $options
     * @return array
     */
    protected function uploadToGcs(UploadedFile $file, string $path, array $options = [])
    {
        try {
            if (!$this->bucket) {
                throw new \Exception('GCS bucket not initialized');
            }

            // Get file content
            $content = file_get_contents($file->getRealPath());
            
            if ($content === false) {
                throw new \Exception('Failed to read file content');
            }
            
            // Upload to GCS (without predefinedAcl for uniform bucket-level access)
            $object = $this->bucket->upload($content, [
                'name' => $path,
                'metadata' => [
                    'contentType' => $file->getMimeType(),
                ],
                // Remove predefinedAcl for uniform bucket-level access compatibility
            ]);

            // Get public URL
            $url = 'https://storage.googleapis.com/' . $this->bucket->name() . '/' . $path;

            return [
                'success' => true,
                'filename' => basename($path),
                'path' => $path,
                'url' => $url,
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'disk' => 'gcs'
            ];

        } catch (\Exception $e) {
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
            if ($this->bucket) {
                // Use GCS client directly for deletion
                $object = $this->bucket->object($path);
                if ($object->exists()) {
                    $object->delete();
                    return true;
                }
                return true; // File doesn't exist, consider it deleted
            } else {
                // Fallback to Laravel Storage for local disk
                if (Storage::disk($this->disk)->exists($path)) {
                    return Storage::disk($this->disk)->delete($path);
                }
                return true;
            }
        } catch (\Exception $e) {
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
        try {
            if ($this->bucket) {
                // Use GCS client directly for existence check
                $object = $this->bucket->object($path);
                return $object->exists();
            } else {
                // Fallback to Laravel Storage for local disk
                return Storage::disk($this->disk)->exists($path);
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get file URL
     *
     * @param string $path
     * @return string
     */
    public function getFileUrl(string $path): string
    {
        // Jika object diupload ke GCS dengan client langsung (bucket siap), gunakan URL GCS
        if ($this->bucket) {
            try {
                $object = $this->bucket->object($path);
                if ($object->exists()) {
                    $baseUrl = config('filesystems.disks.gcs.url');
                    if (empty($baseUrl)) {
                        $bucket = config('filesystems.disks.gcs.bucket');
                        return 'https://storage.googleapis.com/' . $bucket . '/' . $path;
                    }
                    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
                }
                // Jika object TIDAK ada di GCS, fallback ke public URL
                $publicBaseUrl = config('filesystems.disks.public.url');
                return rtrim($publicBaseUrl, '/') . '/' . ltrim($path, '/');
            } catch (\Exception $e) {
                // ignore and fallback to public below
            }
        }

        // Jika fallback ke public disk
        if ($this->disk === 'public' || !$this->bucket) {
            $publicBaseUrl = config('filesystems.disks.public.url');
            return rtrim($publicBaseUrl, '/') . '/' . ltrim($path, '/');
        }

        // Fallback: kembalikan path relatif bila bukan public/gcs
        return $path;
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
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
