<?php

namespace App\Services\Career;

use App\Models\Career\JobApplication;
use App\Models\User;
use App\Notifications\Career\ApplicationStatusChangedNotification;
use App\Notifications\Career\ApplicationSubmittedNotification;
use App\Notifications\Career\RecruiterNewApplicationNotification;
use App\Notifications\Career\VerifyApplicationNotification;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class CareerNotificationService
{
    public function sendVerification(JobApplication $application, string $plaintextToken): void
    {
        $application->loadMissing('candidate:id,full_name,email', 'vacancy:id,title');

        $this->toCandidate($application)->notify(
            new VerifyApplicationNotification($application, $plaintextToken)
        );
    }

    public function applicationSubmitted(JobApplication $application, string $statusPlaintextToken): void
    {
        $application->loadMissing('candidate:id,full_name,email', 'vacancy:id,title');

        $this->toCandidate($application)->notify(
            new ApplicationSubmittedNotification($application, $statusPlaintextToken)
        );

        $this->notifyRecruiters($application);
    }

    public function applicationStatusChanged(JobApplication $application): void
    {
        $application->loadMissing('candidate:id,full_name,email', 'vacancy:id,title');

        if (! $application->candidate?->email) {
            return;
        }

        $this->toCandidate($application)->notify(
            new ApplicationStatusChangedNotification($application)
        );
    }

    protected function notifyRecruiters(JobApplication $application): void
    {
        $emails = config('career.notifications.recruiter_emails', []);

        if ($emails !== []) {
            Notification::route('mail', $emails)
                ->notify(new RecruiterNewApplicationNotification($application));

            return;
        }

        $role = (string) config('career.notifications.recruiter_role', 'superadmin');

        if (! Role::query()->where('name', $role)->exists()) {
            return;
        }

        $users = User::query()
            ->role($role)
            ->whereNotNull('email')
            ->select(['id', 'name', 'email'])
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        Notification::send($users, new RecruiterNewApplicationNotification($application));
    }

    protected function toCandidate(JobApplication $application)
    {
        return Notification::route('mail', [
            $application->candidate->email => $application->candidate->full_name,
        ]);
    }
}
