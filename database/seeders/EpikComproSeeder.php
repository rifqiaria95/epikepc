<?php

namespace Database\Seeders;

use App\Models\Galeri;
use App\Models\KategoriGaleri;
use App\Models\Pricing;
use App\Models\Testimoni;
use App\Models\User;
use Database\Seeders\Concerns\CopiesSeederMedia;
use Illuminate\Database\Seeder;

class EpikComproSeeder extends Seeder
{
    use CopiesSeederMedia;

    public function run(): void
    {
        $userId = User::query()->value('id');

        if (! $userId) {
            $this->command?->warn('EpikComproSeeder skipped: no users found.');

            return;
        }

        $this->seedGallery($userId);
        $this->seedTestimonials($userId);
        $this->deactivateLegacyPricing($userId);

        $this->command?->info('EPIK company profile supplemental content seeded successfully!');
    }

    private function seedGallery(int $userId): void
    {
        $category = KategoriGaleri::updateOrCreate(
            ['slug' => 'project-gallery'],
            ['name' => 'Project Gallery']
        );

        $items = [
            ['title' => 'Portable Production Facilities Tunggul Maung', 'image' => 'image66.png', 'year' => '2021'],
            ['title' => 'Polyethylene Pipe Material Procurement', 'image' => 'image71.png', 'year' => '2021'],
            ['title' => 'Household Pipeline Construction', 'image' => 'image72.png', 'year' => '2021'],
            ['title' => 'Serpong Offtake Handling', 'image' => 'image74.png', 'year' => '2021'],
            ['title' => 'Valve Pit Revitalization', 'image' => 'image76.png', 'year' => '2022'],
            ['title' => 'PGN Kendal Industrial Zone Pipeline', 'image' => 'image78.png', 'year' => '2023'],
            ['title' => 'Metering Station Kendal', 'image' => 'image80.png', 'year' => '2023'],
            ['title' => 'HDD Senipah Balikpapan', 'image' => 'image82.png', 'year' => '2023'],
            ['title' => 'Muara Karang Pipe Bridge', 'image' => 'image84.png', 'year' => '2023'],
            ['title' => 'Batam Gas Infrastructure', 'image' => 'image85.png', 'year' => '2024'],
            ['title' => 'Revitalization Tank LNG', 'image' => 'image87.jpeg', 'year' => '2024'],
            ['title' => 'MEPIC Upgrading Panaran Station', 'image' => 'image89.jpeg', 'year' => '2025'],
        ];

        $seededTitles = [];

        foreach ($items as $index => $item) {
            Galeri::updateOrCreate(
                ['title' => $item['title'], 'kategori_galeri_id' => $category->id],
                [
                    'subtitle' => 'Project '.$item['year'],
                    'description' => 'Project documentation from PT EPIK company profile portfolio.',
                    'image' => $this->copyComproImageToStorage($item['image'], 'gallery'),
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]
            );

            $seededTitles[] = $item['title'];
        }

        Galeri::query()
            ->where('kategori_galeri_id', $category->id)
            ->whereNotIn('title', $seededTitles)
            ->delete();
    }

    private function seedTestimonials(int $userId): void
    {
        $items = [
            [
                'nama' => 'Project Team PGN',
                'instansi' => 'PT PGN',
                'testimoni' => 'EPIK consistently delivers pipeline and gas infrastructure projects with strong discipline in safety, quality, and schedule. Their EPC execution on our network expansion programs has been reliable and professional.',
            ],
            [
                'nama' => 'Operations Division',
                'instansi' => 'PT Pertamina EP',
                'testimoni' => 'The portable production facility project at Tunggul Maung was executed with excellent coordination and technical competence. EPIK demonstrated strong capability in oil & gas construction environments.',
            ],
            [
                'nama' => 'Asset Management',
                'instansi' => 'PT Perta Arun Gas',
                'testimoni' => 'EPIK successfully completed LNG tank revitalization and heater cable installation works with comprehensive EPC scope coverage. Their team handled complex mechanical and electrical requirements effectively.',
            ],
            [
                'nama' => 'PGN SAKA Project Office',
                'instansi' => 'PT PGN SAKA',
                'testimoni' => 'We appreciate EPIK\'s responsiveness and engineering quality on ongoing EPC projects in Batam and Pangkah operations. Their HDD and piping teams perform to industry standards.',
            ],
            [
                'nama' => 'Energy Infrastructure Program',
                'instansi' => 'Kementerian ESDM',
                'testimoni' => 'EPIK has contributed to national household gas network programs with dependable procurement and construction execution, supporting broader energy access objectives.',
            ],
        ];

        Testimoni::query()->forceDelete();

        foreach ($items as $item) {
            Testimoni::create(array_merge($item, [
                'gambar' => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]));
        }
    }

    private function deactivateLegacyPricing(int $userId): void
    {
        Pricing::query()->update([
            'is_active' => false,
            'updated_by' => $userId,
        ]);
    }
}
