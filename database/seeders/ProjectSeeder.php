<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Database\Seeders\Concerns\CopiesSeederMedia;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    use CopiesSeederMedia;

    public function run(): void
    {
        $userId = User::query()->value('id');

        if (! $userId) {
            $this->command?->warn('ProjectSeeder skipped: no users found.');

            return;
        }

        $projects = [
            [
                'title' => 'Portable Production Facilities Tunggul Maung',
                'slug' => 'portabel-production-facilities-tunggul-maung',
                'excerpt' => 'EPC construction of portable production facilities for PT Pertamina EP Asset 3 Subang.',
                'content' => '<p>EPIK executed EPC works for portable production facilities at Tunggul Maung under PT Pertamina EP Asset 3 Subang, delivered through main contractor coordination with high safety and quality standards.</p>',
                'content_secondary' => 'Project completed in 2021 with full handover of production facility scope.',
                'challenge_solution' => '<p>Portable facility construction required modular execution and strict compliance with oil & gas operational standards in a live asset environment.</p>',
                'final_result' => '<p>Facilities completed and commissioned successfully, supporting Pertamina EP production operations.</p>',
                'client' => 'PT Pertamina EP',
                'category' => 'EPC, Oil & Gas',
                'project_date' => '2021-01-01',
                'image' => 'image66.png',
                'sort_order' => 1,
            ],
            [
                'title' => 'Polyethylene Pipe Material Procurement',
                'slug' => 'pengadaan-material-pipa-polyethylene',
                'excerpt' => 'HDPE pipe procurement for PGN household gas network — 18,528 m PE 180mm and 153,600 m PE 63mm.',
                'content' => '<p>Procurement of polyethylene pipe materials for PGN gas network projects, including PE 180 mm and PE 63 mm pipelines with verified material standards and delivery schedules.</p>',
                'client' => 'PT PGN',
                'category' => 'Procurement, Pipeline',
                'project_date' => '2021-06-01',
                'image' => 'image71.png',
                'sort_order' => 2,
            ],
            [
                'title' => 'Household Gas Pipeline Construction',
                'slug' => 'konstruksi-jalur-pipa-sambungan-rumah',
                'excerpt' => 'Household gas pipeline construction — 63m and 180m pipeline connections for Gaskita program.',
                'content' => '<p>Construction of household gas pipeline routes and house connections as part of the Gaskita 2021–2022 program for PGN, executed by PT PGAS Solution as main contractor.</p>',
                'client' => 'PT PGN',
                'category' => 'Construction, Household Gas',
                'project_date' => '2021-09-01',
                'image' => 'image72.png',
                'sort_order' => 3,
            ],
            [
                'title' => 'Serpong Offtake Handling Works',
                'slug' => 'pekerjaan-penanganan-offtake-serpong',
                'excerpt' => 'Construction works for offtake handling at Serpong gas station area.',
                'content' => '<p>Construction scope for offtake handling works at Serpong, supporting safe and reliable gas distribution infrastructure for PGN.</p>',
                'client' => 'PT PGN',
                'category' => 'Construction, Gas Station',
                'project_date' => '2021-11-01',
                'image' => 'image74.png',
                'sort_order' => 4,
            ],
            [
                'title' => 'Valve Pit Revitalization Works',
                'slug' => 'pekerjaan-revitalisasi-bak-valve',
                'excerpt' => 'Valve pit revitalization works across multiple PGN operating areas (2022–2023).',
                'content' => '<p>Revitalization of valve pits including civil, mechanical, and corrosion protection works to restore operational reliability of gas network assets.</p>',
                'client' => 'PT PGN',
                'category' => 'Revitalization, Pipeline',
                'project_date' => '2022-03-01',
                'image' => 'image76.png',
                'sort_order' => 5,
            ],
            [
                'title' => 'PGN Kendal Industrial Zone Pipeline Construction',
                'slug' => 'konstruksi-pipeline-pgn-kawasan-industri-kendal',
                'excerpt' => 'EPC pipeline construction 8 km including HDD auger boring at Kendal Industrial Zone.',
                'content' => '<p>EPC pipeline construction for PT PGN at Kendal Industrial Zone, covering 8 km pipeline scope with HDD auger boring for obstacle crossings.</p>',
                'client' => 'PT PGN',
                'category' => 'EPC, Pipeline, HDD',
                'project_date' => '2023-02-01',
                'image' => 'image78.png',
                'sort_order' => 6,
            ],
            [
                'title' => 'Metering Station Kendal',
                'slug' => 'metering-station-kendal',
                'excerpt' => 'Engineering, procurement, construction, and commissioning of Kendal metering station.',
                'content' => '<p>Full EPCIC delivery of metering station facilities at Kendal, supporting accurate gas measurement and network control for PGN industrial customers.</p>',
                'client' => 'PT PGN',
                'category' => 'EPCIC, Metering Station',
                'project_date' => '2023-05-01',
                'image' => 'image80.png',
                'sort_order' => 7,
            ],
            [
                'title' => 'HDD Senipah Balikpapan',
                'slug' => 'hdd-senipah-balikpapan',
                'excerpt' => 'Horizontal directional drilling pipeline construction at Senipah, Balikpapan for Pertamina Gas.',
                'content' => '<p>HDD pipeline construction scope at Senipah – Balikpapan, enabling trenchless pipe installation under ground obstacles with minimal environmental disruption.</p>',
                'client' => 'Pertamina Gas',
                'category' => 'HDD, Pipeline',
                'project_date' => '2023-08-01',
                'image' => 'image82.png',
                'sort_order' => 8,
            ],
            [
                'title' => 'Muara Karang Pipe Bridge',
                'slug' => 'jembatan-pipa-muara-karang',
                'excerpt' => 'EPC pipe bridge works including piping, civil, and steel structure at Muara Karang.',
                'content' => '<p>EPC scope for pipe bridge construction at Muara Karang including piping work, civil works, and steel structure (pipe bridge) execution.</p>',
                'client' => 'PT PGN',
                'category' => 'EPC, Steel Structure',
                'project_date' => '2023-10-01',
                'image' => 'image84.png',
                'sort_order' => 9,
            ],
            [
                'title' => 'Batam Customer Attachment Gas Infrastructure',
                'slug' => 'pemasangan-infrastruktur-gas-ca-batam',
                'excerpt' => 'EPC gas infrastructure in Batam — Sekupang & Batu Ampar including 11 km pipeline, 2 MRS, HDD, and hot tapping.',
                'content' => '<p>EPC gas infrastructure installation for customer attachment areas in Batu Ampar and Sekupang, including 10 inch and 8 inch pipelines, two MRS units, HDD, and hot tapping works.</p>',
                'client' => 'PT PGN',
                'category' => 'EPC, Gas Infrastructure',
                'project_date' => '2024-01-01',
                'image' => 'image85.png',
                'sort_order' => 10,
            ],
            [
                'title' => 'Construction Revitalization Tank LNG',
                'slug' => 'construction-revitalization-tank-lng',
                'excerpt' => 'EPC revitalization of double-wall LNG tanks including mechanical, electrical, piping, instrument, and civil works.',
                'content' => '<p>EPC revitalization project for LNG tanks at PT Perta Arun Gas covering double-wall tank works, mechanical, electrical, piping, instrument, and civil scopes.</p>',
                'client' => 'PT Perta Arun Gas',
                'category' => 'EPC, LNG',
                'project_date' => '2024-04-01',
                'image' => 'image87.jpeg',
                'sort_order' => 11,
            ],
            [
                'title' => 'Drilling & Heater Cable Installation LNG Tank',
                'slug' => 'drilling-heater-cable-installation-lng-tank',
                'excerpt' => 'Drilling, heater cable removal and installation on LNG tank foundations for Perta Arun Gas.',
                'content' => '<p>EPC scope including replacement works, drilling works, and electrical installation for heater cable systems on LNG tank foundations.</p>',
                'client' => 'PT Perta Arun Gas',
                'category' => 'EPC, LNG, Electrical',
                'project_date' => '2024-07-01',
                'image' => 'image88.jpeg',
                'sort_order' => 12,
            ],
            [
                'title' => 'MEPIC Upgrading Panaran Station',
                'slug' => 'mepic-upgrading-panaran-station',
                'excerpt' => 'EPC upgrading of Panaran gas station — mechanical, electrical, piping, instrument, and civil.',
                'content' => '<p>EPC upgrading works at Panaran Station covering gas station, electrical, piping, instrument, and civil scopes for PGN network enhancement.</p>',
                'client' => 'PT PGN',
                'category' => 'EPC, Gas Station',
                'project_date' => '2025-01-01',
                'image' => 'image89.jpeg',
                'sort_order' => 13,
            ],
            [
                'title' => 'EPC Fasilitas Injection Point Biomethane',
                'slug' => 'epc-fasilitas-injection-point-biomethane',
                'excerpt' => 'EPC biomethane injection point and alternative supply source facilities on gas pipeline network.',
                'content' => '<p>EPC works for biomethane injection point facilities and alternative supply sources on PGN gas pipeline networks, including civil, electrical, instrument, and piping scopes.</p>',
                'client' => 'PT PGN',
                'category' => 'EPC, Biomethane',
                'project_date' => '2025-06-01',
                'image' => 'image90.jpeg',
                'sort_order' => 14,
            ],
            [
                'title' => 'Hot Lean Amine Suction Pipe Replacement Pangkah',
                'slug' => 'hot-lean-amine-suction-pipe-replacement-pangkah',
                'excerpt' => 'Mechanical piping & instrument EPC for hot lean amine suction pipe replacement at Pangkah operations.',
                'content' => '<p>EPC services for hot lean amine suction pipe replacement including dismantling, erection, installation, and testing for PGN SAKA Pangkah operations.</p>',
                'client' => 'PT PGN SAKA',
                'category' => 'EPC, Mechanical Piping',
                'project_date' => '2026-01-01',
                'image' => 'image91.jpeg',
                'sort_order' => 15,
            ],
            [
                'title' => 'Customer Attachment Tahap V Area Batam',
                'slug' => 'customer-attachment-tahap-v-area-batam',
                'excerpt' => 'Ongoing EPC customer attachment Phase V in Batam — pipeline, HDD, civil, valve pit, and MRS.',
                'content' => '<p>Ongoing EPC scope for Customer Attachment Phase V in Batam including CS 6 & 4 inch and MDPE pipelines, HDD, civil works, valve pits, and MRS with foundations.</p>',
                'client' => 'PT PGN SAKA',
                'category' => 'EPC, Customer Attachment',
                'project_date' => '2026-01-15',
                'image' => 'image94.jpeg',
                'sort_order' => 16,
            ],
        ];

        $seededSlugs = [];

        foreach ($projects as $projectData) {
            $imageFile = $projectData['image'];
            unset($projectData['image']);

            $defaults = [
                'content_secondary'   => $projectData['content_secondary'] ?? null,
                'challenge_solution'  => $projectData['challenge_solution'] ?? null,
                'final_result'        => $projectData['final_result'] ?? null,
                'website_url'         => null,
                'is_published'        => true,
                'created_by'          => $userId,
                'updated_by'          => $userId,
            ];

            unset($projectData['content_secondary'], $projectData['challenge_solution'], $projectData['final_result']);

            Project::updateOrCreate(
                ['slug' => $projectData['slug']],
                array_merge($projectData, $defaults, [
                    'image' => $this->copyComproImageToStorage($imageFile, 'projects'),
                ])
            );

            $seededSlugs[] = $projectData['slug'];
        }

        Project::query()->whereNotIn('slug', $seededSlugs)->delete();

        $this->command?->info('EPIK projects seeded successfully from company profile deck!');
    }
}
