<?php

namespace App\Policies;

use App\Models\Career\JobApplication;
use App\Models\User;

class JobApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_applications');
    }

    public function view(User $user, JobApplication $application): bool
    {
        return $user->can('view_applications');
    }

    public function review(User $user, JobApplication $application): bool
    {
        return $user->can('review_applications');
    }

    public function assign(User $user, JobApplication $application): bool
    {
        return $user->can('assign_applications');
    }

    public function changeStatus(User $user, JobApplication $application): bool
    {
        return $user->can('change_application_status') || $user->can('reject_applications');
    }

    public function reject(User $user, JobApplication $application): bool
    {
        return $user->can('reject_applications');
    }

    public function createNote(User $user, JobApplication $application): bool
    {
        return $user->can('create_application_notes');
    }

    public function deleteNote(User $user, JobApplication $application): bool
    {
        return $user->can('delete_application_notes');
    }
}
