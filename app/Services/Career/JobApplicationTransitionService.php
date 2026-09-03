<?php

namespace App\Services\Career;

use App\Enums\Career\ApplicationStatus;
use App\Enums\Career\EmailVerificationStatus;
use App\Exceptions\Career\InvalidTransitionException;
use App\Models\Career\JobApplication;
use App\Models\Career\JobApplicationStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class JobApplicationTransitionService
{
    /**
     * @var array<string, list<string>>
     */
    public const ALLOWED = [
        'PENDING_VERIFICATION' => ['SUBMITTED', 'EXPIRED'],
        'SUBMITTED' => ['SCREENING', 'WITHDRAWN'],
        'SCREENING' => ['SHORTLISTED', 'REJECTED', 'WITHDRAWN'],
        'SHORTLISTED' => ['INTERVIEW', 'REJECTED', 'WITHDRAWN'],
        'INTERVIEW' => ['OFFERED', 'REJECTED', 'WITHDRAWN'],
        'OFFERED' => ['HIRED', 'REJECTED', 'WITHDRAWN'],
        'HIRED' => [],
        'REJECTED' => [],
        'WITHDRAWN' => [],
        'EXPIRED' => [],
    ];

    public function __construct(
        protected CareerNotificationService $notifications,
    ) {}

    /**
     * @param  array{reason_code?: string|null, public_message?: string|null, internal_note?: string|null}  $context
     */
    public function transition(
        JobApplication $application,
        ApplicationStatus $to,
        ?User $actor,
        array $context = [],
        bool $notify = true,
    ): JobApplication {
        return DB::transaction(function () use ($application, $to, $actor, $context, $notify) {
            /** @var JobApplication $locked */
            $locked = JobApplication::query()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            $from = $locked->status;

            if ($from === $to) {
                return $locked;
            }

            if (! $this->isAllowed($from, $to)) {
                throw new InvalidTransitionException(
                    "Status tidak dapat diubah dari {$from->label()} ke {$to->label()}."
                );
            }

            $this->assertActorPermission($actor, $to, $from);

            $payload = [
                'status' => $to,
            ];

            $now = now();

            if ($to === ApplicationStatus::Submitted) {
                $payload['submitted_at'] = $locked->submitted_at ?? $now;
                $payload['verified_at'] = $locked->verified_at ?? $now;
                $payload['email_verification_status'] = EmailVerificationStatus::Verified;
            }

            if ($to === ApplicationStatus::Withdrawn) {
                $payload['withdrawn_at'] = $now;
            }

            if ($to === ApplicationStatus::Hired) {
                $payload['hired_at'] = $now;
            }

            if ($to === ApplicationStatus::Rejected) {
                $payload['rejected_at'] = $now;
            }

            if ($to === ApplicationStatus::Expired) {
                $payload['email_verification_status'] = EmailVerificationStatus::Expired;
            }

            $locked->fill($payload)->save();

            JobApplicationStatusHistory::query()->create([
                'job_application_id' => $locked->id,
                'from_status' => $from,
                'to_status' => $to,
                'reason_code' => $context['reason_code'] ?? null,
                'public_message' => $context['public_message'] ?? null,
                'internal_note' => $context['internal_note'] ?? null,
                'changed_by' => $actor?->id,
                'created_at' => $now,
            ]);

            if ($notify) {
                $fresh = $locked->fresh(['vacancy:id,title', 'candidate:id,full_name,email']);
                DB::afterCommit(fn () => $this->notifications->applicationStatusChanged($fresh));
            }

            return $locked->fresh();
        });
    }

    public function isAllowed(ApplicationStatus $from, ApplicationStatus $to): bool
    {
        return in_array($to->value, self::ALLOWED[$from->value] ?? [], true);
    }

    /**
     * @return list<string>
     */
    public function allowedTargets(ApplicationStatus $from): array
    {
        return self::ALLOWED[$from->value] ?? [];
    }

    protected function assertActorPermission(?User $actor, ApplicationStatus $to, ApplicationStatus $from): void
    {
        if ($actor === null) {
            $systemOnly = [
                ApplicationStatus::Submitted,
                ApplicationStatus::Expired,
                ApplicationStatus::Withdrawn,
            ];

            if (! in_array($to, $systemOnly, true)) {
                throw new InvalidTransitionException('Perubahan status ini hanya dapat dilakukan oleh sistem atau rekruter.');
            }

            return;
        }

        if ($to === ApplicationStatus::Rejected && ! $actor->can('reject_applications')) {
            throw new InvalidTransitionException('Anda tidak memiliki izin untuk menolak lamaran.');
        }

        if ($to !== ApplicationStatus::Rejected && ! $actor->can('change_application_status')) {
            throw new InvalidTransitionException('Anda tidak memiliki izin untuk mengubah status lamaran.');
        }
    }
}
