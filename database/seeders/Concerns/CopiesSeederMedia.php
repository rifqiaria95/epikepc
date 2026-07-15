<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\Storage;

trait CopiesSeederMedia
{
    protected function copyPublicImageToStorage(string $publicRelativePath, string $storageDirectory): ?string
    {
        $normalizedPath = ltrim(str_replace('\\', '/', $publicRelativePath), '/');
        $source = public_path($normalizedPath);

        if (! is_file($source)) {
            $this->command?->warn("Seeder image not found: {$normalizedPath}");

            return null;
        }

        $filename = basename($normalizedPath);
        $destinationDirectory = 'seed/' . trim($storageDirectory, '/');
        $destinationPath = $destinationDirectory . '/' . $filename;

        Storage::disk('public')->makeDirectory($destinationDirectory);

        if (! Storage::disk('public')->exists($destinationPath)) {
            Storage::disk('public')->put($destinationPath, file_get_contents($source));
        }

        return $destinationPath;
    }

    protected function copyComproImageToStorage(string $filename, string $storageDirectory): ?string
    {
        return $this->copyPublicImageToStorage('frontend/img/compro/' . ltrim($filename, '/'), $storageDirectory);
    }
}
