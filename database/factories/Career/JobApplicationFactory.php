<?php

namespace Database\Factories\Career;

use App\Enums\Career\ApplicationStatus;
use App\Enums\Career\AvailabilityType;
use App\Enums\Career\EmailVerificationStatus;
use App\Models\Career\Candidate;
use App\Models\Career\JobApplication;
use App\Models\Career\JobVacancy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobApplication>
 */
class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        return [
            'reference_number' => 'EPC-APP-'.Str::upper(Str::random(10)),
            'job_vacancy_id' => JobVacancy::factory(),
            'candidate_id' => Candidate::factory(),
            'status' => ApplicationStatus::PendingVerification,
            'email_verification_status' => EmailVerificationStatus::Pending,
            'availability_type' => AvailabilityType::Immediately,
            'consent_version' => '2026-09-01',
            'consent_at' => now(),
            'accuracy_declared' => true,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => [
            'status' => ApplicationStatus::Submitted,
            'email_verification_status' => EmailVerificationStatus::Verified,
            'submitted_at' => now(),
            'verified_at' => now(),
        ]);
    }
}
