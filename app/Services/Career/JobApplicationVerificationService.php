<?php

namespace App\Services\Career;

use App\Enums\Career\ApplicationStatus;
use App\Enums\Career\CareerTokenPurpose;
use App\Enums\Career\EmailVerificationStatus;
use App\Exceptions\Career\CareerDomainException;
use App\Exceptions\Career\TokenException;
use App\Models\Career\JobApplication;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class JobApplicationVerificationService
{
    public function __construct(
        protected CareerTokenService $tokens,
        protected JobApplicationTransitionService $transitions,
        protected CareerNotificationService $notifications,
        protected CareerReferenceNumberService $references,
    ) {}

    public function verify(string $plaintext): JobApplication
    {
        return DB::transaction(function () use ($plaintext) {
            $token = $this->tokens->findValid($plaintext, CareerTokenPurpose::EmailVerification);

            /** @var JobApplication $application */
            $application = JobApplication::query()
                ->whereKey($token->job_application_id)
                ->lockForUpdate()
                ->with(['vacancy:id,title', 'candidate:id,full_name,email'])
                ->firstOrFail();

            if ($application->email_verification_status === EmailVerificationStatus::Verified
                && $application->status !== ApplicationStatus::PendingVerification) {
                $this->tokens->consume($token);

                return $application;
            }

            $this->tokens->consume($token);

            $application = $this->transitions->transition(
                $application,
                ApplicationStatus::Submitted,
                null,
                [
                    'reason_code' => 'EMAIL_VERIFIED',
                    'public_message' => 'Lamaran Anda telah diverifikasi dan diterima.',
                ],
                false,
            );

            $issued = $this->tokens->issue($application, CareerTokenPurpose::StatusAccess, request()->ip());

            DB::afterCommit(function () use ($application, $issued) {
                $this->notifications->applicationSubmitted($application, $issued['plaintext']);
            });

            $application->statusAccessPlaintext = $issued['plaintext'];

            return $application;
        });
    }

    public function resend(string $email, string $vacancyId, string $ip): void
    {
        $cooldown = (int) config('career.tokens.resend_cooldown_seconds', 60);
        $hourLimit = (int) config('career.tokens.max_resend_per_hour', 5);
        $normalized = mb_strtolower(trim($email));
        $hourKey = 'career:resend-hour:'.sha1($normalized.'|'.$vacancyId.'|'.$ip);
        $coolKey = 'career:resend-cool:'.sha1($normalized.'|'.$vacancyId);

        if (Cache::has($coolKey)) {
            throw new CareerDomainException('Tunggu sebentar sebelum mengirim ulang tautan verifikasi.', 429, [
                'email' => ['Tunggu sebentar sebelum mengirim ulang tautan verifikasi.'],
            ]);
        }

        $used = (int) Cache::get($hourKey, 0);
        if ($used >= $hourLimit) {
            throw new CareerDomainException('Batas pengiriman ulang tercapai. Coba lagi nanti.', 429, [
                'email' => ['Batas pengiriman ulang tercapai. Coba lagi nanti.'],
            ]);
        }

        Cache::put($coolKey, 1, $cooldown);
        Cache::put($hourKey, $used + 1, 3600);

        $application = JobApplication::query()
            ->where('job_vacancy_id', $vacancyId)
            ->where('email_verification_status', EmailVerificationStatus::Pending->value)
            ->whereHas('candidate', fn ($q) => $q->where('normalized_email', $normalized))
            ->latest('created_at')
            ->first();

        // Neutral response: do not reveal whether the email exists.
        if (! $application) {
            return;
        }

        $issued = $this->tokens->issue($application, CareerTokenPurpose::EmailVerification, $ip);
        $this->notifications->sendVerification($application, $issued['plaintext']);
    }

    /**
     * @return array{application: JobApplication, public: array<string, mixed>}
     */
    public function statusByToken(string $plaintext): array
    {
        $token = $this->tokens->findValid($plaintext, CareerTokenPurpose::StatusAccess);
        $this->tokens->touch($token);

        $application = JobApplication::query()
            ->whereKey($token->job_application_id)
            ->with([
                'vacancy:id,title,code,department',
                'statusHistories' => fn ($q) => $q->select([
                    'id', 'job_application_id', 'from_status', 'to_status', 'public_message', 'created_at',
                ]),
            ])
            ->firstOrFail();

        $timeline = $application->statusHistories->map(fn ($history) => [
            'status' => $history->to_status->publicLabel(),
            'message' => $history->public_message,
            'at' => $history->created_at?->toIso8601String(),
        ])->all();

        return [
            'application' => $application,
            'public' => array_merge($application->toPublicStatusPayload(), [
                'timeline' => $timeline,
            ]),
        ];
    }

    public function withdraw(string $plaintext, ?string $reason = null): JobApplication
    {
        if (! config('career.features.allow_withdrawal')) {
            throw new TokenException('Penarikan lamaran tidak tersedia.');
        }

        return DB::transaction(function () use ($plaintext, $reason) {
            try {
                $token = $this->tokens->findValid($plaintext, CareerTokenPurpose::Withdrawal);
            } catch (TokenException) {
                $token = $this->tokens->findValid($plaintext, CareerTokenPurpose::StatusAccess);
            }

            $application = JobApplication::query()
                ->whereKey($token->job_application_id)
                ->lockForUpdate()
                ->firstOrFail();

            return $this->transitions->transition(
                $application,
                ApplicationStatus::Withdrawn,
                null,
                [
                    'reason_code' => 'CANDIDATE_WITHDRAWAL',
                    'public_message' => 'Lamaran ditarik oleh kandidat.',
                    'internal_note' => $reason,
                ],
            );
        });
    }
}
