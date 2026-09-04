<?php

namespace App\Services\Certificate;

use App\Services\FileStorageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateImageService
{
    public function __construct(
        protected FileStorageService $storage,
    ) {}

    /**
     * @return array{success: bool, path?: string, thumbnail_path?: string, error?: string}
     */
    public function upload(UploadedFile $file): array
    {
        $validation = $this->validate($file);
        if (! $validation['success']) {
            return $validation;
        }

        $directory = config('certificates.upload_directory');
        $upload = $this->storage->uploadFile($file, $directory, ['visibility' => 'public']);

        if (! $upload['success']) {
            return [
                'success' => false,
                'error' => 'Gagal mengunggah gambar sertifikat.',
            ];
        }

        $thumbnailPath = $this->createThumbnail($file, $upload['path']);

        return [
            'success' => true,
            'path' => $upload['path'],
            'thumbnail_path' => $thumbnailPath,
        ];
    }

    public function deletePaths(?string $imagePath, ?string $thumbnailPath = null): void
    {
        if ($imagePath) {
            $this->storage->deleteFile($imagePath);
        }

        if ($thumbnailPath) {
            $this->storage->deleteFile($thumbnailPath);
        }
    }

    /**
     * @return array{success: bool, error?: string}
     */
    protected function validate(UploadedFile $file): array
    {
        if (! $file->isValid()) {
            return ['success' => false, 'error' => 'File upload tidak valid.'];
        }

        $maxKb = (int) config('certificates.max_file_size_kb');
        if ($file->getSize() > $maxKb * 1024) {
            return ['success' => false, 'error' => 'Ukuran gambar sertifikat maksimal '.($maxKb / 1024).' MB.'];
        }

        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, config('certificates.allowed_extensions'), true)) {
            return ['success' => false, 'error' => 'Format gambar harus JPEG, PNG, atau WebP.'];
        }

        $mime = $file->getMimeType();
        if (! in_array($mime, config('certificates.allowed_mimes'), true)) {
            return ['success' => false, 'error' => 'Format gambar harus JPEG, PNG, atau WebP.'];
        }

        if (! $this->hasValidSignature($file)) {
            return ['success' => false, 'error' => 'File gambar tidak valid atau rusak.'];
        }

        $info = @getimagesize($file->getRealPath());
        if ($info === false) {
            return ['success' => false, 'error' => 'File gambar tidak valid atau rusak.'];
        }

        [$width, $height] = $info;
        $pixels = $width * $height;

        if ($width < config('certificates.min_width') || $height < config('certificates.min_height')) {
            return ['success' => false, 'error' => 'Dimensi gambar terlalu kecil.'];
        }

        if ($width > config('certificates.max_width') || $height > config('certificates.max_height')) {
            return ['success' => false, 'error' => 'Dimensi gambar terlalu besar.'];
        }

        if ($pixels > config('certificates.max_pixels')) {
            return ['success' => false, 'error' => 'Resolusi gambar terlalu tinggi.'];
        }

        return ['success' => true];
    }

    protected function hasValidSignature(UploadedFile $file): bool
    {
        $handle = fopen($file->getRealPath(), 'rb');
        if ($handle === false) {
            return false;
        }

        $bytes = fread($handle, 12);
        fclose($handle);

        if ($bytes === false || strlen($bytes) < 3) {
            return false;
        }

        if (str_starts_with($bytes, "\xFF\xD8\xFF")) {
            return true;
        }

        if (str_starts_with($bytes, "\x89PNG\r\n\x1a\n")) {
            return true;
        }

        if (str_starts_with($bytes, 'RIFF') && str_contains(substr($bytes, 0, 12), 'WEBP')) {
            return true;
        }

        return false;
    }

    protected function createThumbnail(UploadedFile $file, string $originalPath): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $info = @getimagesize($file->getRealPath());
        if ($info === false) {
            return null;
        }

        [$width, $height, $type] = $info;
        $maxWidth = (int) config('certificates.thumbnail_max_width');

        if ($width <= $maxWidth) {
            return null;
        }

        $source = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($file->getRealPath()),
            IMAGETYPE_PNG => @imagecreatefrompng($file->getRealPath()),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($file->getRealPath()) : false,
            default => false,
        };

        if ($source === false) {
            return null;
        }

        $ratio = $maxWidth / $width;
        $targetWidth = $maxWidth;
        $targetHeight = (int) round($height * $ratio);
        $target = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
            imagealphablending($target, false);
            imagesavealpha($target, true);
        }

        imagecopyresampled($target, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $disk = config('certificates.disk');
        $thumbDir = config('certificates.thumbnail_directory');
        $filename = Str::random(40).'.webp';
        $thumbPath = 'uploads/'.date('Y/m').'/'.$thumbDir.'/'.$filename;
        $temp = tempnam(sys_get_temp_dir(), 'cert_thumb_');

        if ($temp === false) {
            imagedestroy($source);
            imagedestroy($target);

            return null;
        }

        $saved = function_exists('imagewebp') && imagewebp($target, $temp, 85);

        imagedestroy($source);
        imagedestroy($target);

        if (! $saved) {
            @unlink($temp);

            return null;
        }

        $stored = Storage::disk($disk)->put($thumbPath, file_get_contents($temp), 'public');
        @unlink($temp);

        return $stored ? $thumbPath : null;
    }
}
