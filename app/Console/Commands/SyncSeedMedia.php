<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SyncSeedMedia extends Command
{
    protected $signature = 'epik:sync-seed-media
                            {--force : Overwrite existing seed files}';

    protected $description = 'Copy company profile images from public/frontend/img/compro into storage/app/public/seed';

    /**
     * Storage subdirectories used by seeders / DB image paths (seed/{dir}/filename).
     */
    private array $directories = [
        'gallery',
        'news',
        'projects',
        'services',
        'about',
    ];

    public function handle(): int
    {
        $sourceDir = public_path('frontend/img/compro');

        if (! is_dir($sourceDir)) {
            $this->error("Source directory missing: {$sourceDir}");

            return self::FAILURE;
        }

        $disk = Storage::disk('public');
        $copied = 0;
        $skipped = 0;
        $force = (bool) $this->option('force');

        foreach ($this->directories as $directory) {
            $destinationDirectory = 'seed/'.$directory;
            $disk->makeDirectory($destinationDirectory);

            foreach (File::files($sourceDir) as $file) {
                $filename = $file->getFilename();
                $destinationPath = $destinationDirectory.'/'.$filename;

                if ($disk->exists($destinationPath) && ! $force) {
                    $skipped++;

                    continue;
                }

                $disk->put($destinationPath, file_get_contents($file->getPathname()));
                $copied++;
            }
        }

        if (! file_exists(public_path('storage'))) {
            $this->call('storage:link');
        }

        $this->info("Seed media sync complete. Copied: {$copied}, skipped: {$skipped}.");

        return self::SUCCESS;
    }
}
