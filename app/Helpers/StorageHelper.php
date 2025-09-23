<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get file URL dari storage
     *
     * @param string|null $path
     * @param string $disk
     * @return string|null
     */
    public static function getFileUrl(?string $path, string $disk = null): ?string
    {
        if (empty($path)) {
            return null;
        }

        // Jika path sudah berupa URL lengkap, return langsung
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        $disk = $disk ?? config('filesystems.default');

        try {
            return Storage::disk($disk)->url($path);
        } catch (\Exception $e) {
            // Fallback ke local storage jika ada error
            return Storage::disk('local')->url($path);
        }
    }

    /**
     * Get image URL dengan fallback
     *
     * @param string|null $path
     * @param string $fallback
     * @param string $disk
     * @return string
     */
    public static function getImageUrl(?string $path, string $fallback = 'https://via.placeholder.com/300x200', string $disk = null): string
    {
        $url = self::getFileUrl($path, $disk);
        
        return $url ?? $fallback;
    }

    /**
     * Cek apakah file exists di storage
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public static function fileExists(string $path, string $disk = null): bool
    {
        if (empty($path)) {
            return false;
        }

        $disk = $disk ?? config('filesystems.default');

        try {
            return Storage::disk($disk)->exists($path);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get file size dari storage
     *
     * @param string $path
     * @param string $disk
     * @return int|null
     */
    public static function getFileSize(string $path, string $disk = null): ?int
    {
        if (empty($path)) {
            return null;
        }

        $disk = $disk ?? config('filesystems.default');

        try {
            return Storage::disk($disk)->size($path);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Format file size ke human readable
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function formatFileSize(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get file extension dari path
     *
     * @param string $path
     * @return string
     */
    public static function getFileExtension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Get file name dari path
     *
     * @param string $path
     * @return string
     */
    public static function getFileName(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    /**
     * Get file basename dari path
     *
     * @param string $path
     * @return string
     */
    public static function getFileBasename(string $path): string
    {
        return pathinfo($path, PATHINFO_BASENAME);
    }

    /**
     * Get directory dari path
     *
     * @param string $path
     * @return string
     */
    public static function getDirectory(string $path): string
    {
        return pathinfo($path, PATHINFO_DIRNAME);
    }

    /**
     * Generate thumbnail URL untuk image
     *
     * @param string $path
     * @param int $width
     * @param int $height
     * @param string $disk
     * @return string
     */
    public static function getThumbnailUrl(string $path, int $width = 300, int $height = 200, string $disk = null): string
    {
        // Untuk implementasi thumbnail, Anda bisa menggunakan:
        // 1. Cloudinary
        // 2. AWS CloudFront dengan Lambda@Edge
        // 3. Image processing service
        
        // Untuk sementara, return URL asli
        return self::getImageUrl($path, "https://via.placeholder.com/{$width}x{$height}", $disk);
    }

    /**
     * Get storage disk info
     *
     * @param string $disk
     * @return array
     */
    public static function getDiskInfo(string $disk = null): array
    {
        $disk = $disk ?? config('filesystems.default');
        
        return [
            'disk' => $disk,
            'driver' => config("filesystems.disks.{$disk}.driver"),
            'url' => config("filesystems.disks.{$disk}.url"),
            'bucket' => config("filesystems.disks.{$disk}.bucket"),
        ];
    }
}
