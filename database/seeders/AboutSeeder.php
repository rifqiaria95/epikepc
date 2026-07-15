<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\User;
use Database\Seeders\Concerns\CopiesSeederMedia;
use Illuminate\Database\Seeder;

class AboutSeeder extends Seeder
{
    use CopiesSeederMedia;

    public function run(): void
    {
        $userId = User::query()->value('id');

        if (! $userId) {
            $this->command?->warn('AboutSeeder skipped: no users found.');

            return;
        }

        $image = $this->copyComproImageToStorage('image18.jpeg', 'about');

        About::query()->updateOrCreate(
            ['title' => 'Connecting Energy, Building for the Future'],
            [
                'subtitle'    => 'PT Energi Persada Inti Konstruksi (EPIK)',
                'description' => '<p>PT Energi Persada Inti Konstruksi (EPIK) is an integrated engineering and construction company focused on oil &amp; gas infrastructure in Indonesia. We deliver EPC, operation &amp; maintenance, agencies &amp; trading, and logistic transportation services for national energy projects.</p><p>Certified with ISO 9001:2015, ISO 14001:2015, ISO 45001:2018, and SBUJK qualifications, EPIK supports clients such as PGN, Pertamina, and the Ministry of Energy and Mineral Resources with reliable execution from engineering through commissioning.</p>',
                'image'       => $image,
                'video'       => null,
                'address'     => 'Bangka Raya 106, South Jakarta 12730, Indonesia',
                'phone'       => '(+62) 21 275 20059',
                'email'       => 'info@epikepc.com',
                'facebook'    => null,
                'instagram'   => 'epikepc',
                'twitter'     => null,
                'tiktok'      => null,
                'youtube'     => null,
                'created_by'  => $userId,
                'updated_by'  => $userId,
            ]
        );

        $this->command?->info('About/company profile seeded from EPIK company profile deck.');
    }
}
