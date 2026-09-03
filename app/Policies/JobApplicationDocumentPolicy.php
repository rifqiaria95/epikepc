<?php

namespace App\Policies;

use App\Models\Career\JobApplicationDocument;
use App\Models\User;

class JobApplicationDocumentPolicy
{
    public function view(User $user, JobApplicationDocument $document): bool
    {
        return $user->can('view_candidate_documents');
    }

    public function download(User $user, JobApplicationDocument $document): bool
    {
        return $user->can('download_candidate_documents')
            && $user->can('view_candidate_documents')
            && $user->can('view_applications');
    }
}
