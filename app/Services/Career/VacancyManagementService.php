<?php

namespace App\Services\Career;

use App\Enums\Career\VacancyStatus;
use App\Exceptions\Career\CareerDomainException;
use App\Models\Career\JobVacancy;
use App\Models\Career\JobVacancyQuestion;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VacancyManagementService
{
    public function __construct(
        protected CareerReferenceNumberService $references,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, array<string, mixed>>  $questions
     */
    public function create(array $payload, array $questions, User $actor): JobVacancy
    {
        return DB::transaction(function () use ($payload, $questions, $actor) {
            $vacancy = JobVacancy::query()->create([
                ...$this->normalizedPayload($payload),
                'code' => $this->references->nextVacancyCode(),
                'slug' => JobVacancy::makeSlug($payload['title']),
                'status' => VacancyStatus::Draft,
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
            ]);

            $this->syncQuestions($vacancy, $questions);

            return $vacancy->fresh('questions');
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, array<string, mixed>>  $questions
     */
    public function update(JobVacancy $vacancy, array $payload, array $questions, User $actor): JobVacancy
    {
        if ($vacancy->status === VacancyStatus::Archived) {
            throw new CareerDomainException('Lowongan yang diarsipkan tidak dapat diubah.', 409);
        }

        return DB::transaction(function () use ($vacancy, $payload, $questions, $actor) {
            $data = $this->normalizedPayload($payload);
            unset($data['code']);

            if ($vacancy->status === VacancyStatus::Published) {
                unset($data['slug']);
            } elseif (! empty($payload['title']) && $payload['title'] !== $vacancy->title) {
                $data['slug'] = JobVacancy::makeSlug($payload['title'], $vacancy->id);
            }

            $vacancy->fill($data);
            $vacancy->updated_by = $actor->id;
            $vacancy->save();

            if ($vacancy->isEditable()) {
                $this->syncQuestions($vacancy, $questions);
            }

            return $vacancy->fresh('questions');
        });
    }

    public function publish(JobVacancy $vacancy, User $actor): JobVacancy
    {
        $this->assertPublishable($vacancy);

        $vacancy->fill([
            'status' => VacancyStatus::Published,
            'published_at' => $vacancy->published_at ?? now(),
            'updated_by' => $actor->id,
        ])->save();

        return $vacancy->fresh();
    }

    public function close(JobVacancy $vacancy, User $actor): JobVacancy
    {
        if ($vacancy->status !== VacancyStatus::Published) {
            throw new CareerDomainException('Hanya lowongan terpublikasi yang dapat ditutup.', 409);
        }

        $vacancy->fill([
            'status' => VacancyStatus::Closed,
            'closed_by' => $actor->id,
            'updated_by' => $actor->id,
            'closes_at' => $vacancy->closes_at && $vacancy->closes_at->isPast()
                ? $vacancy->closes_at
                : now(),
        ])->save();

        return $vacancy->fresh();
    }

    public function archive(JobVacancy $vacancy, User $actor): JobVacancy
    {
        if (! in_array($vacancy->status, [VacancyStatus::Closed, VacancyStatus::Draft], true)) {
            throw new CareerDomainException('Lowongan harus ditutup atau masih draft sebelum diarsipkan.', 409);
        }

        $vacancy->fill([
            'status' => VacancyStatus::Archived,
            'updated_by' => $actor->id,
        ])->save();

        return $vacancy->fresh();
    }

    public function duplicate(JobVacancy $vacancy, User $actor): JobVacancy
    {
        return DB::transaction(function () use ($vacancy, $actor) {
            $vacancy->load('questions');

            $copy = $vacancy->replicate([
                'code', 'slug', 'status', 'published_at', 'closes_at', 'closed_by',
            ]);
            $copy->code = $this->references->nextVacancyCode();
            $copy->title = $vacancy->title.' (Salinan)';
            $copy->slug = JobVacancy::makeSlug($copy->title);
            $copy->status = VacancyStatus::Draft;
            $copy->created_by = $actor->id;
            $copy->updated_by = $actor->id;
            $copy->save();

            foreach ($vacancy->questions as $question) {
                $clone = $question->replicate(['job_vacancy_id']);
                $clone->job_vacancy_id = $copy->id;
                $clone->save();
            }

            return $copy->fresh('questions');
        });
    }

    public function delete(JobVacancy $vacancy, User $actor): void
    {
        if ($vacancy->applications()->exists()) {
            throw new CareerDomainException('Lowongan yang sudah memiliki lamaran tidak dapat dihapus. Tutup atau arsipkan saja.', 409);
        }

        $vacancy->updated_by = $actor->id;
        $vacancy->save();
        $vacancy->delete();
    }

    /**
     * @param  array<int, array<string, mixed>>  $questions
     */
    public function syncQuestions(JobVacancy $vacancy, array $questions): void
    {
        $keep = [];

        foreach (array_values($questions) as $index => $row) {
            if (empty($row['question'])) {
                continue;
            }

            $attrs = [
                'question' => $row['question'],
                'help_text' => $row['help_text'] ?? null,
                'type' => $row['type'],
                'options' => $row['options'] ?? null,
                'is_required' => (bool) ($row['is_required'] ?? true),
                'sort_order' => $row['sort_order'] ?? $index,
            ];

            if (! empty($row['id'])) {
                $question = JobVacancyQuestion::query()
                    ->where('job_vacancy_id', $vacancy->id)
                    ->whereKey($row['id'])
                    ->first();

                if ($question) {
                    $question->fill($attrs)->save();
                    $keep[] = $question->id;

                    continue;
                }
            }

            $created = $vacancy->questions()->create($attrs);
            $keep[] = $created->id;
        }

        JobVacancyQuestion::query()
            ->where('job_vacancy_id', $vacancy->id)
            ->when($keep !== [], fn ($q) => $q->whereNotIn('id', $keep), fn ($q) => $q)
            ->delete();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    protected function normalizedPayload(array $payload): array
    {
        return [
            'title' => $payload['title'],
            'department' => $payload['department'],
            'location_city' => $payload['location_city'],
            'location_province' => $payload['location_province'],
            'employment_type' => $payload['employment_type'],
            'work_arrangement' => $payload['work_arrangement'],
            'experience_level' => $payload['experience_level'],
            'summary' => $payload['summary'],
            'description' => $this->sanitizeHtml($payload['description'] ?? ''),
            'responsibilities' => $this->sanitizeHtml($payload['responsibilities'] ?? ''),
            'qualifications' => $this->sanitizeHtml($payload['qualifications'] ?? ''),
            'preferred_qualifications' => $this->sanitizeHtml($payload['preferred_qualifications'] ?? null),
            'minimum_education' => $payload['minimum_education'] ?? null,
            'minimum_experience_years' => $payload['minimum_experience_years'] ?? null,
            'headcount' => $payload['headcount'] ?? config('career.defaults.headcount', 1),
            'requires_site_travel' => (bool) ($payload['requires_site_travel'] ?? false),
            'allows_salary_expectation' => (bool) ($payload['allows_salary_expectation'] ?? false),
            'closes_at' => $payload['closes_at'] ?? null,
            'seo_title' => $payload['seo_title'] ?? null,
            'seo_description' => $payload['seo_description'] ?? null,
        ];
    }

    public function assertPublishable(JobVacancy $vacancy): void
    {
        $required = [
            'title' => 'Judul',
            'department' => 'Departemen',
            'location_city' => 'Kota',
            'location_province' => 'Provinsi',
            'summary' => 'Ringkasan',
            'description' => 'Deskripsi',
            'responsibilities' => 'Tanggung jawab',
            'qualifications' => 'Kualifikasi',
        ];

        $missing = [];
        foreach ($required as $field => $label) {
            if (blank($vacancy->{$field})) {
                $missing[] = $label;
            }
        }

        if ($missing !== []) {
            throw new CareerDomainException(
                'Lowongan belum lengkap untuk dipublikasikan: '.implode(', ', $missing).'.',
                422,
                ['publish' => ['Lowongan belum lengkap untuk dipublikasikan.']]
            );
        }

        if (! in_array($vacancy->status, [VacancyStatus::Draft, VacancyStatus::Closed], true)) {
            throw new CareerDomainException('Hanya lowongan draft atau tertutup yang dapat dipublikasikan.', 409);
        }
    }

    protected function sanitizeHtml(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $allowed = '<p><br><ul><ol><li><strong><b><em><i><h3><h4><a>';

        $clean = strip_tags($html, $allowed);
        $clean = preg_replace('/on\w+=([\'"]).*?\1/i', '', $clean) ?? $clean;
        $clean = preg_replace('/javascript:/i', '', $clean) ?? $clean;

        return $clean;
    }
}
