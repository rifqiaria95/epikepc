<?php

namespace App\Policies;

use App\Models\Career\JobVacancy;
use App\Models\User;

class JobVacancyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_vacancies');
    }

    public function view(User $user, JobVacancy $vacancy): bool
    {
        return $user->can('view_vacancies');
    }

    public function create(User $user): bool
    {
        return $user->can('create_vacancies');
    }

    public function update(User $user, JobVacancy $vacancy): bool
    {
        return $user->can('edit_vacancies');
    }

    public function publish(User $user, JobVacancy $vacancy): bool
    {
        return $user->can('publish_vacancies');
    }

    public function close(User $user, JobVacancy $vacancy): bool
    {
        return $user->can('close_vacancies');
    }

    public function archive(User $user, JobVacancy $vacancy): bool
    {
        return $user->can('archive_vacancies');
    }

    public function delete(User $user, JobVacancy $vacancy): bool
    {
        return $user->can('edit_vacancies') && ! $vacancy->applications()->exists();
    }
}
