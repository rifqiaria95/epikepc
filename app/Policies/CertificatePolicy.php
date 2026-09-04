<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_certificates');
    }

    public function view(User $user, Certificate $certificate): bool
    {
        return $user->can('view_certificates');
    }

    public function create(User $user): bool
    {
        return $user->can('create_certificates');
    }

    public function update(User $user, Certificate $certificate): bool
    {
        return $user->can('edit_certificates');
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return $user->can('delete_certificates');
    }

    public function publish(User $user, Certificate $certificate): bool
    {
        return $user->can('publish_certificates');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_certificates');
    }
}
