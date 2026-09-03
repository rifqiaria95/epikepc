<?php

namespace App\Services\Career;

use App\Enums\Career\ApplicationStatus;
use App\Models\Career\Candidate;
use App\Models\Career\JobApplication;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class CandidateIdentityService
{
    /**
     * Find or create candidate by normalized email in a race-safe way.
     * Does not overwrite existing profile fields with empty/new application data.
     *
     * @param  array<string, mixed>  $payload
     */
    public function findOrCreate(array $payload): Candidate
    {
        $normalizedEmail = Candidate::normalizeEmail($payload['email']);
        $normalizedPhone = Candidate::normalizePhone($payload['phone'] ?? null);

        return DB::transaction(function () use ($payload, $normalizedEmail, $normalizedPhone) {
            $existing = Candidate::query()
                ->where('normalized_email', $normalizedEmail)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                $this->mergeNonDestructive($existing, $payload, $normalizedPhone);

                return $existing->fresh();
            }

            try {
                return Candidate::query()->create([
                    'full_name' => $payload['full_name'],
                    'email' => trim($payload['email']),
                    'normalized_email' => $normalizedEmail,
                    'phone' => $payload['phone'],
                    'normalized_phone' => $normalizedPhone,
                    'domicile_city' => $payload['domicile_city'],
                    'domicile_province' => $payload['domicile_province'],
                    'highest_education' => $payload['highest_education'],
                    'education_major' => $payload['education_major'] ?? null,
                    'institution_name' => $payload['institution_name'] ?? null,
                    'graduation_year' => $payload['graduation_year'] ?? null,
                    'total_experience_years' => $payload['total_experience_years'] ?? 0,
                    'current_or_last_company' => $payload['current_or_last_company'] ?? null,
                    'current_or_last_title' => $payload['current_or_last_title'] ?? null,
                    'linkedin_url' => $payload['linkedin_url'] ?? null,
                    'portfolio_url' => $payload['portfolio_url'] ?? null,
                ]);
            } catch (UniqueConstraintViolationException) {
                $candidate = Candidate::query()
                    ->where('normalized_email', $normalizedEmail)
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->mergeNonDestructive($candidate, $payload, $normalizedPhone);

                return $candidate->fresh();
            }
        });
    }

    public function hasApplicationForVacancy(Candidate $candidate, string $vacancyId): bool
    {
        return JobApplication::query()
            ->where('candidate_id', $candidate->id)
            ->where('job_vacancy_id', $vacancyId)
            ->whereNotIn('status', [
                ApplicationStatus::Withdrawn->value,
                ApplicationStatus::Expired->value,
            ])
            ->exists();
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function mergeNonDestructive(Candidate $candidate, array $payload, ?string $normalizedPhone): void
    {
        $updates = [];

        // Always refresh contact phone if provided (candidate may have new WhatsApp)
        if (! empty($payload['phone'])) {
            $updates['phone'] = $payload['phone'];
            $updates['normalized_phone'] = $normalizedPhone;
        }

        // Only fill empty optional profile fields; never clobber existing values
        $optionalFill = [
            'education_major',
            'institution_name',
            'graduation_year',
            'current_or_last_company',
            'current_or_last_title',
            'linkedin_url',
            'portfolio_url',
        ];

        foreach ($optionalFill as $field) {
            if (empty($candidate->{$field}) && ! empty($payload[$field])) {
                $updates[$field] = $payload[$field];
            }
        }

        // Update domicile / education / experience when provided (latest application intent)
        foreach (['full_name', 'domicile_city', 'domicile_province', 'highest_education', 'total_experience_years'] as $field) {
            if (array_key_exists($field, $payload) && $payload[$field] !== null && $payload[$field] !== '') {
                $updates[$field] = $payload[$field];
            }
        }

        if ($updates !== []) {
            $candidate->fill($updates)->save();
        }
    }
}
