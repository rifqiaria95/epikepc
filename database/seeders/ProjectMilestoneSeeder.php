<?php

namespace Database\Seeders;

use App\Models\CompanyJourney;
use App\Models\CompanyMilestone;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ProjectMilestoneSeeder extends Seeder
{
    /**
     * Seed homepage Project Milestones timeline from EPIK project portfolio,
     * ordered oldest → newest by project_date.
     */
    public function run(): void
    {
        CompanyJourney::query()->updateOrCreate(
            ['id' => 1],
            [
                'timeline_subtitle' => 'Project History',
                'timeline_title' => 'Project Milestones',
            ]
        );

        $projects = collect([
            [
                'title' => 'Portable Production Facilities Tunggul Maung',
                'excerpt' => 'EPC construction of portable production facilities for PT Pertamina EP Asset 3 Subang.',
                'client' => 'PT Pertamina EP',
                'project_date' => '2021-01-01',
            ],
            [
                'title' => 'Polyethylene Pipe Material Procurement',
                'excerpt' => 'HDPE pipe procurement for PGN household gas network — 18,528 m PE 180mm and 153,600 m PE 63mm.',
                'client' => 'PT PGN',
                'project_date' => '2021-06-01',
            ],
            [
                'title' => 'Household Gas Pipeline Construction',
                'excerpt' => 'Household gas pipeline construction — 63m and 180m pipeline connections for Gaskita program.',
                'client' => 'PT PGN',
                'project_date' => '2021-09-01',
            ],
            [
                'title' => 'Serpong Offtake Handling Works',
                'excerpt' => 'Construction works for offtake handling at Serpong gas station area.',
                'client' => 'PT PGN',
                'project_date' => '2021-11-01',
            ],
            [
                'title' => 'Valve Pit Revitalization Works',
                'excerpt' => 'Valve pit revitalization works across multiple PGN operating areas (2022–2023).',
                'client' => 'PT PGN',
                'project_date' => '2022-03-01',
            ],
            [
                'title' => 'Pekerjaan Konstruksi Pipeline PT Perusahaan Gas Negara Kawasan Industri Kendal (KIK)',
                'excerpt' => 'EPC pipeline construction 8 km including HDD auger boring at Kendal Industrial Zone.',
                'client' => 'PT PGN',
                'project_date' => '2023-02-01',
            ],
            [
                'title' => 'Pekerjaan Konstruksi Metering PT Perusahaan Gas Negara di Area Kabupaten Kendal',
                'excerpt' => 'Engineering, procurement, construction, and commissioning of Kendal metering station.',
                'client' => 'PT PGN',
                'project_date' => '2023-04-14',
            ],
            [
                'title' => 'Pengadaan Barang dan Jasa Konstruksi Pekerjaan Relokasi dan Pembuatan Jembatan Pipa 16 Inch di Muara Karang',
                'excerpt' => 'EPC pipe bridge works including piping, civil, and steel structure at Muara Karang.',
                'client' => 'PT PGN',
                'project_date' => '2023-06-15',
            ],
            [
                'title' => 'Pekerjaan Jasa HDD Area B Senipah Balikpapan',
                'excerpt' => 'Horizontal directional drilling pipeline construction at Senipah, Balikpapan for Pertamina Gas.',
                'client' => 'PT PGAS SOLUTION',
                'project_date' => '2023-07-12',
            ],
            [
                'title' => 'Pekerjaan Pemasangan Infrastruktur Gas Customer Attachment Tahun 2024 Tahap III Area Batam',
                'excerpt' => 'EPC gas infrastructure in Batam — Sekupang & Batu Ampar including 11 km pipeline, 2 MRS, HDD, and hot tapping.',
                'client' => 'PT PGAS SOLUTION',
                'project_date' => '2024-01-01',
            ],
            [
                'title' => 'Pekerjaan Jasa Konstruksi MEPIC Pada Revitalisasi Tank F-6004',
                'excerpt' => 'EPC revitalization of double-wall LNG tanks including mechanical, electrical, piping, instrument, and civil works.',
                'client' => 'PT Perta Arun Gas',
                'project_date' => '2024-04-01',
            ],
            [
                'title' => 'Drilling & Heater Cable Installation LNG Tank',
                'excerpt' => 'Drilling, heater cable removal and installation on LNG tank foundations for Perta Arun Gas.',
                'client' => 'PT Perta Arun Gas',
                'project_date' => '2024-07-01',
            ],
            [
                'title' => 'Pengadaan Pekerjaan MEPIC Pada Proyek Uprading Panaran Station',
                'excerpt' => 'EPC upgrading of Panaran gas station — mechanical, electrical, piping, instrument, and civil.',
                'client' => 'PT PGAS SOLUTION',
                'project_date' => '2025-04-16',
            ],
            [
                'title' => 'Pengadaan EPC Fasilitas Injection Point untuk Biomethane dan Sumber Pasokan Lain Pada Jaringan Pipa Gas SP-STRA-005',
                'excerpt' => 'EPC biomethane injection point and alternative supply source facilities on gas pipeline network.',
                'client' => 'PT PGAS SOLUTION',
                'project_date' => '2025-11-18',
            ],
            [
                'title' => 'Hot Lean Amine Suction Pipe Replacement Pangkah',
                'excerpt' => 'Mechanical piping & instrument EPC for hot lean amine suction pipe replacement at Pangkah operations.',
                'client' => 'PT PGN SAKA',
                'project_date' => '2026-01-01',
            ],
            [
                'title' => 'Customer Attachment Tahap V Area Batam',
                'excerpt' => 'Ongoing EPC customer attachment Phase V in Batam — pipeline, HDD, civil, valve pit, and MRS.',
                'client' => 'PT PGAS SOLUTION',
                'project_date' => '2026-01-15',
            ],
        ])
            ->sortBy('project_date')
            ->values();

        CompanyMilestone::query()->forceDelete();

        foreach ($projects as $index => $project) {
            $date = Carbon::parse($project['project_date']);

            CompanyMilestone::create([
                'year' => $date->format('Y'),
                'month' => (int) $date->format('n'),
                'title' => $project['title'],
                'description' => sprintf(
                    '%s Client: %s.',
                    rtrim($project['excerpt'], '.').'.',
                    $project['client']
                ),
                'sort_order' => $index + 1,
                'is_active' => true,
            ]);
        }

        $this->command?->info(sprintf(
            'Project Milestones seeded: %d items (oldest → newest).',
            $projects->count()
        ));
    }
}
