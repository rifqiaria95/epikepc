<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FileStorageService;
use App\Models\News;
use Illuminate\Support\Facades\Storage;

class MigrateFilesToStorage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:migrate-to-storage 
                            {--model= : Model yang akan dimigrasi (News, dll)}
                            {--field=thumbnail : Field yang berisi path file}
                            {--dry-run : Jalankan tanpa benar-benar memigrasi file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrasi file dari local storage ke object storage';

    protected $fileStorageService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FileStorageService $fileStorageService)
    {
        parent::__construct();
        $this->fileStorageService = $fileStorageService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = $this->option('model') ?? 'News';
        $field = $this->option('field') ?? 'thumbnail';
        $dryRun = $this->option('dry-run');

        $this->info("Memulai migrasi file untuk model: {$model}");
        $this->info("Field: {$field}");
        $this->info("Dry run: " . ($dryRun ? 'Ya' : 'Tidak'));
        $this->info("Storage disk: " . config('filesystems.default'));
        $this->newLine();

        try {
            $modelClass = "App\\Models\\{$model}";
            
            if (!class_exists($modelClass)) {
                $this->error("Model {$modelClass} tidak ditemukan!");
                return 1;
            }

            $records = $modelClass::whereNotNull($field)
                ->where($field, '!=', '')
                ->get();

            if ($records->isEmpty()) {
                $this->warn("Tidak ada record dengan field {$field} yang perlu dimigrasi.");
                return 0;
            }

            $this->info("Ditemukan {$records->count()} record untuk dimigrasi.");
            $this->newLine();

            $successCount = 0;
            $errorCount = 0;
            $skippedCount = 0;

            $progressBar = $this->output->createProgressBar($records->count());
            $progressBar->start();

            foreach ($records as $record) {
                $oldPath = $record->{$field};
                
                // Skip jika sudah berupa URL atau path object storage
                if ($this->isAlreadyInStorage($oldPath)) {
                    $skippedCount++;
                    $progressBar->advance();
                    continue;
                }

                // Cek apakah file local masih ada
                $localPath = public_path('images/' . $oldPath);
                if (!file_exists($localPath)) {
                    $this->error("File tidak ditemukan: {$localPath}");
                    $errorCount++;
                    $progressBar->advance();
                    continue;
                }

                if (!$dryRun) {
                    // Tentukan path baru di object storage
                    $newPath = 'uploads/' . date('Y/m') . '/news/thumbnails/' . basename($oldPath);
                    
                    // Migrasi file
                    $result = $this->fileStorageService->migrateToCloud($oldPath, $newPath);
                    
                    if ($result['success']) {
                        // Update database dengan path baru
                        $record->update([$field => $newPath]);
                        $successCount++;
                    } else {
                        $this->error("Gagal migrasi file {$oldPath}: " . $result['error']);
                        $errorCount++;
                    }
                } else {
                    $successCount++;
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            // Tampilkan hasil
            $this->info("Hasil migrasi:");
            $this->info("- Berhasil: {$successCount}");
            $this->info("- Error: {$errorCount}");
            $this->info("- Dilewati: {$skippedCount}");

            if ($dryRun) {
                $this->warn("Ini adalah dry run. File belum benar-benar dimigrasi.");
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("Terjadi kesalahan: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Cek apakah path sudah berada di object storage
     *
     * @param string $path
     * @return bool
     */
    private function isAlreadyInStorage(string $path): bool
    {
        // Cek apakah sudah berupa URL lengkap
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return true;
        }

        // Cek apakah sudah menggunakan path object storage
        if (str_starts_with($path, 'uploads/')) {
            return true;
        }

        return false;
    }
}
