<?php

namespace Database\Seeders;

use App\Models\Galeri;
use App\Models\KategoriGaleri;
use App\Models\News;
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
        $this->seedNews($userId);
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
                    'subtitle'    => 'Project ' . $item['year'],
                    'description' => 'Project documentation from PT EPIK company profile portfolio.',
                    'image'       => $this->copyComproImageToStorage($item['image'], 'gallery'),
                    'created_by'  => $userId,
                    'updated_by'  => $userId,
                ]
            );

            $seededTitles[] = $item['title'];
        }

        Galeri::query()
            ->where('kategori_galeri_id', $category->id)
            ->whereNotIn('title', $seededTitles)
            ->delete();
    }

    private function seedNews(int $userId): void
    {
        $articles = [
            [
                'title' => 'EPIK Completes Kendal Industrial Zone Pipeline EPC',
                'slug' => 'epik-completes-kendal-industrial-zone-pipeline-epc',
                'summary' => 'PT EPIK successfully completed 8 km pipeline construction including HDD works at PGN Kendal Industrial Zone.',
                'content' => "PT Energi Persada Inti Konstruksi (EPIK) has completed EPC pipeline construction at PGN Kendal Industrial Zone, covering 8 kilometers of pipeline scope with HDD auger boring for obstacle crossings.\n\nThe project strengthens gas supply infrastructure for industrial customers in Central Java and demonstrates EPIK's integrated EPC execution capability.",
            ],
            [
                'title' => 'Batam Gas Infrastructure Project Completed by EPIK',
                'slug' => 'batam-gas-infrastructure-project-completed-by-epik',
                'summary' => 'Customer attachment gas infrastructure in Batam Sekupang and Batu Ampar completed with pipeline, MRS, HDD, and hot tapping scopes.',
                'content' => "EPIK completed EPC gas infrastructure works for customer attachment areas in Batam, including pipeline installation in Sekupang and Batu Ampar, two MRS units, HDD crossings, and hot tapping execution.\n\nThe project supports PGN's gas network expansion strategy in the Riau Islands.",
            ],
            [
                'title' => 'LNG Tank Revitalization Project at Perta Arun Gas',
                'slug' => 'lng-tank-revitalization-project-at-perta-arun-gas',
                'summary' => 'EPIK delivers EPC revitalization of double-wall LNG tanks with full mechanical, electrical, piping, and civil scopes.',
                'content' => "PT EPIK completed construction revitalization works for LNG tanks at PT Perta Arun Gas, covering double-wall tank systems, mechanical, electrical, piping, instrument, and civil engineering scopes.\n\nThe project enhances asset reliability and operational safety for LNG storage facilities.",
            ],
            [
                'title' => 'EPIK Expands Biomethane Injection Facility EPC Portfolio',
                'slug' => 'epik-expands-biomethane-injection-facility-epc-portfolio',
                'summary' => 'New EPC project for biomethane injection point and alternative gas supply sources on PGN pipeline networks.',
                'content' => "EPIK is executing EPC works for biomethane injection point facilities and alternative supply sources on PGN gas pipeline networks.\n\nThe project includes civil, electrical, instrument, and piping scopes supporting Indonesia's energy transition and gas network diversification.",
            ],
        ];

        $seededSlugs = [];

        foreach ($articles as $article) {
            News::updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'title'        => $article['title'],
                    'content'      => $article['content'],
                    'summary'      => $article['summary'],
                    'thumbnail'    => $this->copyComproImageToStorage('image78.png', 'news'),
                    'status'       => 'published',
                    'published_at' => now()->subDays(rand(5, 60)),
                    'author_id'    => $userId,
                    'created_by'   => $userId,
                ]
            );

            $seededSlugs[] = $article['slug'];
        }

        News::query()->whereNotIn('slug', $seededSlugs)->update(['status' => 'draft']);
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
                'gambar'     => null,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]));
        }
    }

    private function deactivateLegacyPricing(int $userId): void
    {
        Pricing::query()->update([
            'is_active'  => false,
            'updated_by' => $userId,
        ]);
    }
}
