<?php

namespace Database\Seeders;

use App\Enums\Career\QuestionType;
use App\Enums\Career\VacancyStatus;
use App\Models\Career\JobVacancy;
use Illuminate\Database\Seeder;

class CareerDemoSeeder extends Seeder
{
    public function run(): void
    {
        $codes = [
            'EPC-DEMO-DRAFT' => VacancyStatus::Draft,
            'EPC-DEMO-PUB' => VacancyStatus::Published,
            'EPC-DEMO-CLOSED' => VacancyStatus::Closed,
            'EPC-DEMO-ARCH' => VacancyStatus::Archived,
        ];

        foreach ($codes as $code => $status) {
            $vacancy = JobVacancy::query()->firstOrCreate(
                ['code' => $code],
                [
                    'title' => match ($status) {
                        VacancyStatus::Draft => 'Site Engineer (Draft)',
                        VacancyStatus::Published => 'Project Engineer',
                        VacancyStatus::Closed => 'Quantity Surveyor',
                        VacancyStatus::Archived => 'HSSE Officer',
                    },
                    'slug' => strtolower($code),
                    'department' => 'Engineering',
                    'location_city' => 'Jakarta',
                    'location_province' => 'DKI Jakarta',
                    'employment_type' => 'FULL_TIME',
                    'work_arrangement' => 'ONSITE',
                    'experience_level' => 'MID',
                    'summary' => 'Posisi contoh untuk modul Career EPIKEPC.',
                    'description' => '<p>Deskripsi lowongan contoh. Bukan lowongan nyata.</p>',
                    'responsibilities' => '<ul><li>Koordinasi lapangan</li></ul>',
                    'qualifications' => '<ul><li>S1 Teknik Sipil</li></ul>',
                    'preferred_qualifications' => '<ul><li>Pengalaman proyek EPC</li></ul>',
                    'headcount' => 1,
                    'status' => $status,
                    'published_at' => $status === VacancyStatus::Draft ? null : now()->subDays(10),
                    'closes_at' => $status === VacancyStatus::Published ? now()->addMonth() : now()->subDay(),
                ]
            );

            if ($vacancy->questions()->exists()) {
                continue;
            }

            if ($status === VacancyStatus::Published) {
                $vacancy->questions()->create([
                    'question' => 'Apakah Anda bersedia ditugaskan di luar kota?',
                    'type' => QuestionType::Boolean,
                    'is_required' => true,
                    'sort_order' => 1,
                ]);
                $vacancy->questions()->create([
                    'question' => 'Software engineering yang dikuasai',
                    'type' => QuestionType::MultipleChoice,
                    'options' => ['AutoCAD', 'SAP2000', 'MS Project', 'Lainnya'],
                    'is_required' => true,
                    'sort_order' => 2,
                ]);
            }
        }

    }
}
