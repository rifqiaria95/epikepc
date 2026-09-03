<?php

namespace App\Services\Career;

use App\Enums\Career\ApplicationStatus;
use App\Enums\Career\CareerTokenPurpose;
use App\Enums\Career\DocumentType;
use App\Enums\Career\EmailVerificationStatus;
use App\Enums\Career\QuestionType;
use App\Exceptions\Career\DuplicateApplicationException;
use App\Exceptions\Career\VacancyUnavailableException;
use App\Models\Career\JobApplication;
use App\Models\Career\JobApplicationAnswer;
use App\Models\Career\JobApplicationStatusHistory;
use App\Models\Career\JobVacancy;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class JobApplicationSubmissionService
{
    public function __construct(
        protected CandidateIdentityService $identities,
        protected CandidateDocumentService $documents,
        protected CareerTokenService $tokens,
        protected CareerNotificationService $notifications,
        protected CareerReferenceNumberService $references,
    ) {}

    /**
     * @param  array<string, mixed>  $payload
     * @return array{application: JobApplication}
     */
    public function submit(JobVacancy $vacancy, array $payload, UploadedFile $cv, string $ip): array
    {
        $storedPath = null;

        try {
            $result = DB::transaction(function () use ($vacancy, $payload, $cv, $ip, &$storedPath) {
                /** @var JobVacancy $lockedVacancy */
                $lockedVacancy = JobVacancy::query()
                    ->whereKey($vacancy->id)
                    ->lockForUpdate()
                    ->with(['questions' => fn ($q) => $q->orderBy('sort_order')])
                    ->firstOrFail();

                if (! $lockedVacancy->acceptsApplications()) {
                    throw new VacancyUnavailableException;
                }

                $candidate = $this->identities->findOrCreate($payload);

                $application = $this->createApplication($lockedVacancy, $candidate->id, $payload);

                $this->persistAnswers($application, $lockedVacancy, $payload['answers'] ?? []);

                $stored = $this->documents->store($application, $cv, DocumentType::Cv);
                $storedPath = $stored['stored_path'];

                JobApplicationStatusHistory::query()->create([
                    'job_application_id' => $application->id,
                    'from_status' => null,
                    'to_status' => ApplicationStatus::PendingVerification,
                    'reason_code' => 'APPLICATION_RECEIVED',
                    'public_message' => 'Lamaran diterima. Menunggu verifikasi email.',
                    'changed_by' => null,
                    'created_at' => now(),
                ]);

                $issued = $this->tokens->issue($application, CareerTokenPurpose::EmailVerification, $ip);

                DB::afterCommit(function () use ($application, $issued) {
                    $this->notifications->sendVerification($application->fresh(['candidate', 'vacancy']), $issued['plaintext']);
                });

                return ['application' => $application->fresh()];
            });
        } catch (\Throwable $e) {
            if ($storedPath) {
                $this->documents->cleanupStoredPath($storedPath);
            }

            throw $e;
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function createApplication(JobVacancy $vacancy, string $candidateId, array $payload): JobApplication
    {
        try {
            return JobApplication::query()->create([
                'reference_number' => $this->references->nextApplicationReference(),
                'job_vacancy_id' => $vacancy->id,
                'candidate_id' => $candidateId,
                'status' => ApplicationStatus::PendingVerification,
                'email_verification_status' => EmailVerificationStatus::Pending,
                'cover_letter' => $payload['cover_letter'] ?? null,
                'latest_salary_amount' => $payload['latest_salary_amount'] ?? null,
                'expected_salary_amount' => $vacancy->allows_salary_expectation
                    ? ($payload['expected_salary_amount'] ?? null)
                    : null,
                'salary_currency' => config('career.defaults.salary_currency', 'IDR'),
                'availability_type' => $payload['availability_type'],
                'available_from' => $payload['available_from'] ?? null,
                'willing_to_relocate' => $payload['willing_to_relocate'] ?? null,
                'willing_to_travel_to_site' => $payload['willing_to_travel_to_site'] ?? null,
                'referral_source' => $payload['referral_source'] ?? null,
                'referral_detail' => $payload['referral_detail'] ?? null,
                'consent_version' => config('career.privacy.consent_version'),
                'consent_at' => now(),
                'accuracy_declared' => true,
                'question_snapshot' => $vacancy->questions->map->toSnapshot()->values()->all(),
            ]);
        } catch (UniqueConstraintViolationException) {
            throw new DuplicateApplicationException;
        }
    }

    /**
     * @param  array<string, mixed>  $answers
     */
    protected function persistAnswers(JobApplication $application, JobVacancy $vacancy, array $answers): void
    {
        foreach ($vacancy->questions as $question) {
            $raw = $answers[$question->id] ?? null;
            $isEmpty = $raw === null || $raw === '' || $raw === [];

            if ($question->is_required && $isEmpty) {
                throw ValidationException::withMessages([
                    "answers.{$question->id}" => "Jawaban untuk pertanyaan '{$question->question}' wajib diisi.",
                ]);
            }

            if ($isEmpty) {
                continue;
            }

            $this->assertAnswerMatchesType($question->type, $raw, $question->options ?? [], $question->question);

            $text = is_array($raw) ? null : (string) $raw;
            $json = is_array($raw) ? array_values($raw) : null;

            if ($question->type === QuestionType::Boolean) {
                $text = filter_var($raw, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            }

            JobApplicationAnswer::query()->create([
                'job_application_id' => $application->id,
                'job_vacancy_question_id' => $question->id,
                'question_text' => $question->question,
                'question_type' => $question->type,
                'question_options' => $question->options,
                'answer_text' => $text,
                'answer_json' => $json,
            ]);
        }
    }

    /**
     * @param  array<int, mixed>|null  $options
     */
    protected function assertAnswerMatchesType(QuestionType $type, mixed $raw, ?array $options, string $question): void
    {
        $field = "Jawaban untuk pertanyaan '{$question}'";

        match ($type) {
            QuestionType::Text, QuestionType::Textarea => Validator::validate(
                ['value' => $raw],
                ['value' => ['required', 'string', 'max:5000']],
                ['value.required' => "{$field} wajib diisi."]
            ),
            QuestionType::Number => Validator::validate(
                ['value' => $raw],
                ['value' => ['required', 'numeric']],
                ['value.numeric' => "{$field} harus berupa angka."]
            ),
            QuestionType::Boolean => Validator::validate(
                ['value' => $raw],
                ['value' => ['required']],
            ),
            QuestionType::SingleChoice => $this->assertChoice($raw, $options, $field, false),
            QuestionType::MultipleChoice => $this->assertChoice($raw, $options, $field, true),
        };
    }

    /**
     * @param  array<int, mixed>|null  $options
     */
    protected function assertChoice(mixed $raw, ?array $options, string $field, bool $multiple): void
    {
        $allowed = array_map('strval', $options ?? []);
        $values = $multiple ? (array) $raw : [$raw];

        foreach ($values as $value) {
            if (! in_array((string) $value, $allowed, true)) {
                throw ValidationException::withMessages([
                    'answers' => ["{$field} tidak valid."],
                ]);
            }
        }
    }
}
