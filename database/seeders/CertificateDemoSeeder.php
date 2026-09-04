<?php

namespace Database\Seeders;

use App\Enums\CertificateStatus;
use App\Models\Certificate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CertificateDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        $samples = [
            ['title' => 'ISO 9001:2015 Quality Management', 'issuer' => 'International Organization for Standardization'],
            ['title' => 'ISO 45001 Occupational Health & Safety', 'issuer' => 'International Organization for Standardization'],
            ['title' => 'SMK3 Construction Safety Certification', 'issuer' => 'Ministry of Manpower'],
            ['title' => 'Professional Engineer Registration', 'issuer' => 'Persatuan Insinyur Indonesia'],
            ['title' => 'Environmental Management Compliance', 'issuer' => 'Ministry of Environment'],
            ['title' => 'Construction Industry License Class A', 'issuer' => 'LPJK'],
        ];

        $disk = 'public';
        $order = 1;

        foreach ($samples as $sample) {
            $slug = Str::slug($sample['title']);
            $existing = Certificate::query()->where('slug', $slug)->first();

            if ($existing) {
                $this->refreshDemoImage($existing, $sample, $disk);
                $order++;

                continue;
            }

            $path = $this->generateCertificateImage($sample['title'], $sample['issuer'], $disk);

            if (! $path) {
                continue;
            }

            Certificate::query()->create([
                'title' => $sample['title'],
                'slug' => $slug,
                'issuer' => $sample['issuer'],
                'description' => 'Demonstrates EPIKEPC commitment to quality, safety, and professional standards.',
                'image_path' => $path,
                'image_alt' => $sample['title'].' certificate',
                'status' => CertificateStatus::Published,
                'is_featured' => $order <= 3,
                'display_order' => $order,
                'published_at' => now()->subDays($order),
                'issued_at' => now()->subYears(2)->addMonths($order),
            ]);

            $order++;
        }
    }

    protected function refreshDemoImage(Certificate $certificate, array $sample, string $disk): void
    {
        if (! extension_loaded('gd')) {
            return;
        }

        $path = $this->generateCertificateImage($sample['title'], $sample['issuer'], $disk);

        if (! $path) {
            return;
        }

        if ($certificate->image_path && $certificate->image_path !== $path) {
            Storage::disk($disk)->delete($certificate->image_path);
        }

        $certificate->update([
            'image_path' => $path,
            'image_alt' => $sample['title'].' certificate',
        ]);
    }

    protected function generateCertificateImage(string $title, string $issuer, string $disk): ?string
    {
        if (! extension_loaded('gd')) {
            return null;
        }

        $width = 560;
        $height = 760;
        $img = imagecreatetruecolor($width, $height);

        $white = imagecolorallocate($img, 255, 255, 255);
        $border = imagecolorallocate($img, 37, 60, 116);
        $gold = imagecolorallocate($img, 255, 223, 8);
        $text = imagecolorallocate($img, 30, 40, 60);
        $muted = imagecolorallocate($img, 100, 110, 130);

        imagefilledrectangle($img, 0, 0, $width, $height, $white);
        imagerectangle($img, 18, 18, $width - 19, $height - 19, $border);
        imagerectangle($img, 28, 28, $width - 29, $height - 29, $gold);

        imagestring($img, 5, 48, 60, 'CERTIFICATE OF COMPLIANCE', $border);
        imagestring($img, 3, 48, 110, 'EPIKEPC', $text);
        imagestring($img, 2, 48, 150, 'This certifies that', $muted);

        $this->drawWrappedText($img, 2, 48, 190, $width - 96, $title, $text);
        imagestring($img, 2, 48, 360, 'Issued by:', $muted);
        $this->drawWrappedText($img, 2, 48, 385, $width - 96, $issuer, $text);

        imagestring($img, 2, 48, $height - 80, 'Certificate No. EPK-'.strtoupper(Str::random(6)), $muted);
        imagestring($img, 2, 48, $height - 55, date('d M Y'), $muted);

        $relativeDir = 'uploads/'.date('Y/m').'/'.config('certificates.upload_directory');
        Storage::disk($disk)->makeDirectory($relativeDir);

        $filename = Str::random(40).'.png';
        $path = $relativeDir.'/'.$filename;
        $absolute = storage_path('app/public/'.$path);

        File::ensureDirectoryExists(dirname($absolute));
        imagepng($img, $absolute);
        imagedestroy($img);

        return $path;
    }

    protected function drawWrappedText($img, int $font, int $x, int $y, int $maxWidth, string $text, int $color): void
    {
        $words = explode(' ', $text);
        $line = '';
        $lineHeight = 18;

        foreach ($words as $word) {
            $test = $line === '' ? $word : $line.' '.$word;
            if (imagefontwidth($font) * strlen($test) > $maxWidth && $line !== '') {
                imagestring($img, $font, $x, $y, $line, $color);
                $y += $lineHeight;
                $line = $word;
            } else {
                $line = $test;
            }
        }

        if ($line !== '') {
            imagestring($img, $font, $x, $y, $line, $color);
        }
    }
}
