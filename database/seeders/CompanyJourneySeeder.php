<?php

namespace Database\Seeders;

use App\Models\CompanyJourney;
use Illuminate\Database\Seeder;

class CompanyJourneySeeder extends Seeder
{
    public function run(): void
    {
        CompanyJourney::query()->updateOrCreate(
            ['id' => 1],
            [
                'section_subtitle'        => 'Our Story',
                'section_title'           => 'Company',
                'section_title_highlight' => 'Journey',
                'section_description'     => 'PT Energi Persada Inti Konstruksi (EPIK) delivers integrated EPC and O&M services for oil & gas infrastructure across Indonesia — from pipeline networks and metering stations to HDD crossings and LNG facilities.',
                'video_url'               => null,
                'video_poster_tag'        => 'Company Profile',
                'video_poster_title'      => 'PT Energi Persada Inti Konstruksi',
                'video_established'       => 'Energy Infrastructure Partner',
                'video_location'          => 'Jakarta, Indonesia',
                'video_caption'           => 'Connecting Energy, Building for the Future',
                'video_duration'          => null,
                'timeline_subtitle'       => 'Project History',
                'timeline_title'          => 'Project Milestones',
                'is_active'               => true,
            ]
        );

        $this->command?->info('Company Journey section settings seeded.');
    }
}
