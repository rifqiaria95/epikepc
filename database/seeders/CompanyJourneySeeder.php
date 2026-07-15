<?php

namespace Database\Seeders;

use App\Models\CompanyJourney;
use App\Models\CompanyMilestone;
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
                'timeline_subtitle'       => 'Company History',
                'timeline_title'          => 'Our Milestones',
                'is_active'               => true,
            ]
        );

        $milestones = [
            ['year' => '2015', 'title' => 'ISO 9001:2015 Certified', 'description' => 'Achieved ISO 9001:2015 quality management system certification for engineering and construction services.', 'sort_order' => 1],
            ['year' => '2016', 'title' => 'ISO 14001:2015 Certified', 'description' => 'Certified ISO 14001:2015 environmental management system to support sustainable project execution.', 'sort_order' => 2],
            ['year' => '2018', 'title' => 'ISO 45001:2018 Certified', 'description' => 'Achieved ISO 45001:2018 occupational health and safety management certification.', 'sort_order' => 3],
            ['year' => '2019', 'title' => 'SBUJK Qualified', 'description' => 'Registered SBUJK qualifications including MK009 and MK010 for construction business entity services.', 'sort_order' => 4],
            ['year' => '2021', 'title' => 'Household Gas Network Projects', 'description' => 'Completed major household natural gas network projects in Wajo and Banggai regencies (10,775 SR) for the Ministry of Energy and Mineral Resources.', 'sort_order' => 5],
            ['year' => '2022', 'title' => 'PGN Pipeline Portfolio Expansion', 'description' => 'Delivered multiple pipeline procurement and construction projects for PGN, including Semarang, Indramayu, Wajo, and valve revitalization works.', 'sort_order' => 6],
            ['year' => '2023', 'title' => 'Kendal & HDD Balikpapan', 'description' => 'Executed EPC pipeline construction at PGN Kendal Industrial Zone and HDD drilling works at Senipah, Balikpapan for Pertamina Gas.', 'sort_order' => 7],
            ['year' => '2024', 'title' => 'Batam Gas Infrastructure & LNG Revitalization', 'description' => 'Completed Batam customer attachment infrastructure and EPC revitalization of LNG tanks for PT Perta Arun Gas.', 'sort_order' => 8],
            ['year' => '2025', 'title' => 'Panaran Station & Biomethane Injection', 'description' => 'Delivered MEPIC upgrading at Panaran Station and EPC biomethane injection point facilities for PGN gas pipeline networks.', 'sort_order' => 9],
            ['year' => '2026', 'title' => 'Ongoing Major EPC Projects', 'description' => 'Continuing EPC works for PGN SAKA including Pangkah hot amine pipe replacement and Customer Attachment Phase V in Batam.', 'sort_order' => 10],
        ];

        CompanyMilestone::query()->forceDelete();

        foreach ($milestones as $milestone) {
            CompanyMilestone::create(array_merge($milestone, ['is_active' => true]));
        }

        $this->command?->info('Company Journey & Milestones seeded from EPIK company profile deck.');
    }
}
