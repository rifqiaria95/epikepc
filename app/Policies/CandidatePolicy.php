<?php

namespace App\Policies;

use App\Models\Career\Candidate;
use App\Models\User;

class CandidatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view_candidates');
    }

    public function view(User $user, Candidate $candidate): bool
    {
        return $user->can('view_candidates');
    }
}
