<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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
        
        $this->safeLog('info', 'FileStorageService constructor called');
        $this->safeLog('info', 'Disk set to: ' . $this->disk);
        $this->safeLog('info', 'Base path: ' . $this->basePath);
        
        // Initialize GCS client
        $this->initializeGcsClient();
        
        $this->safeLog('info', 'GCS bucket initialized: ' . ($this->bucket ? 'YES' : 'NO'));
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
            
            $this->safeLog('info', 'Initializing GCS client...');
            $this->safeLog('info', 'Project ID: ' . $projectId);
            $this->safeLog('info', 'Key file: ' . $keyFile);
            $this->safeLog('info', 'Bucket name: ' . $bucketName);
            
            // Convert relative path to absolute path
            if (!file_exists($keyFile)) {
                $keyFile = base_path($keyFile);
                $this->safeLog('info', 'Key file not found, trying with base_path: ' . $keyFile);
            }
            
            if (file_exists($keyFile)) {
                $this->safeLog('info', 'Key file exists, creating GCS client...');
                $this->gcsClient = new StorageClient([
                    'projectId' => $projectId,
                    'keyFilePath' => $keyFile,
                ]);
                
                $this->bucket = $this->gcsClient->bucket($bucketName);
                $this->safeLog('info', 'GCS client and bucket created successfully');
            } else {
                $this->safeLog('error', 'Key file does not exist: ' . $keyFile);
            }
        } catch (\Exception $e) {
            $this->safeLog('error', 'Failed to initialize GCS client: ' . $e->getMessage());
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
            $this->safeLog('info', 'FileStorageService::uploadFile called');
            $this->safeLog('info', 'Directory: ' . $directory);
            $this->safeLog('info', 'GCS bucket initialized: ' . ($this->bucket ? 'YES' : 'NO'));
            
            // Generate unique filename
            $filename = $this->generateUniqueFilename($file);

            // Set path
            $path = $this->basePath . '/' . $directory . '/' . $filename;
            
            $this->safeLog('info', 'Generated path: ' . $path);

            // Try to upload to GCS first
            if ($this->bucket) {
                $this->safeLog('info', 'Attempting GCS upload...');
                $gcsResult = $this->uploadToGcs($file, $path, $options);
                if ($gcsResult['success']) {
                    $this->safeLog('info', 'GCS upload successful');
                    return $gcsResult;
                } else {
                    $this->safeLog('error', 'GCS upload failed: ' . ($gcsResult['error'] ?? 'Unknown error'));
                }
            } else {
                $this->safeLog('warning', 'GCS bucket not initialized, falling back to local storage');
            }

            // Fallback to local storage
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
            $this->safeLog('error', 'File upload error: ' . $e->getMessage());

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

            $this->safeLog('info', 'Starting GCS upload: ' . $path);
            $this->safeLog('info', 'File size: ' . $file->getSize() . ' bytes');
            $this->safeLog('info', 'File MIME type: ' . $file->getMimeType());

            // Get file content
            $content = file_get_contents($file->getRealPath());
            
            if ($content === false) {
                throw new \Exception('Failed to read file content');
            }
            
            $this->safeLog('info', 'File content read successfully, size: ' . strlen($content) . ' bytes');
            
            // Upload to GCS
            $object = $this->bucket->upload($content, [
                'name' => $path,
                'metadata' => [
                    'contentType' => $file->getMimeType(),
                ],
            ]);

            $this->safeLog('info', 'File uploaded to GCS successfully: ' . $path);

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
            $this->safeLog('error', 'GCS upload error: ' . $e->getMessage());
            
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
                    $this->safeLog('info', 'File deleted from GCS: ' . $path);
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
            $this->safeLog('error', 'File delete error: ' . $e->getMessage());
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
            $this->safeLog('error', 'File exists check error: ' . $e->getMessage());
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
        if ($this->disk === 'local' || $this->disk === 'public') {
            return Storage::disk($this->disk)->url($path);
        }

        // For GCS, generate URL manually
        if ($this->disk === 'gcs') {
            return config('filesystems.disks.gcs.url') . '/' . $path;
        }

        return Storage::disk($this->disk)->url($path);
    }

    /**
     * Safe logging method that won't crash if logging fails
     *
     * @param string $level
     * @param string $message
     * @return void
     */
    protected function safeLog(string $level, string $message): void
    {
        try {
            // Check if we're in production and log file is not writable
            if (app()->environment('production')) {
                $logPath = storage_path('logs/laravel.log');
                if (!is_writable($logPath)) {
                    // Skip logging in production if log file is not writable
                    return;
                }
            }
            Log::$level($message);
        } catch (\Exception $e) {
            // If logging fails, we can't do much about it
            // Just continue without crashing the application
        }
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
