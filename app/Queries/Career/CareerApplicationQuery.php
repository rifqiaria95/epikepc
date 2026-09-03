<?php

namespace App\Queries\Career;

use App\Enums\Career\ApplicationStatus;
use App\Enums\Career\EmailVerificationStatus;
use App\Models\Career\JobApplication;
use App\Support\Career\QueryLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CareerApplicationQuery
{
    use QueryLike;

    public function cmsBaseQuery(Request $request): Builder
    {
        $query = JobApplication::query()->forCmsListing();

        if ($search = $this->datatablesSearchTerm($request)) {
            $like = $this->likeOperator();
            $term = '%'.mb_strtolower($search).'%';

            $query->where(function (Builder $q) use ($like, $term) {
                $q->whereRaw("LOWER(reference_number) {$like} ?", [$term])
                    ->orWhereHas('candidate', function (Builder $c) use ($like, $term) {
                        $c->whereRaw("LOWER(full_name) {$like} ?", [$term])
                            ->orWhereRaw("LOWER(email) {$like} ?", [$term])
                            ->orWhereRaw("LOWER(phone) {$like} ?", [$term])
                            ->orWhereRaw("LOWER(COALESCE(normalized_phone, '')) {$like} ?", [$term]);
                    });
            });
        }

        if ($vacancyId = $request->get('job_vacancy_id')) {
            $query->where('job_vacancy_id', $vacancyId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($verification = $request->get('email_verification_status')) {
            $query->where('email_verification_status', $verification);
        }

        if ($recruiter = $request->get('assigned_recruiter_id')) {
            $query->where('assigned_recruiter_id', $recruiter);
        }

        if ($from = $request->get('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->get('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        if ($education = $request->get('highest_education')) {
            $query->whereHas('candidate', fn (Builder $c) => $c->where('highest_education', $education));
        }

        if ($request->filled('min_experience')) {
            $query->whereHas('candidate', fn (Builder $c) => $c->where('total_experience_years', '>=', (float) $request->get('min_experience')));
        }

        // Default: hide pending verification from "ready" views unless explicitly filtered
        if (! $request->filled('email_verification_status') && ! $request->boolean('include_unverified')) {
            $query->where(function (Builder $q) {
                $q->where('email_verification_status', EmailVerificationStatus::Verified->value)
                    ->orWhere('status', '!=', ApplicationStatus::PendingVerification->value);
            });
        }

        return $query->orderByDesc('created_at');
    }

    public function overviewMetrics(): array
    {
        $summary = app(CareerSummaryQuery::class);
        $vacancy = $summary->vacancyMetrics();
        $application = $summary->applicationMetrics();

        $statusCounts = JobApplication::query()
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $byVacancy = JobApplication::query()
            ->select('job_vacancy_id', DB::raw('COUNT(*) as aggregate'))
            ->where('email_verification_status', EmailVerificationStatus::Verified->value)
            ->groupBy('job_vacancy_id')
            ->with('vacancy:id,title,code')
            ->orderByDesc('aggregate')
            ->limit(10)
            ->get();

        $recent = JobApplication::query()
            ->forCmsListing()
            ->where('email_verification_status', EmailVerificationStatus::Verified->value)
            ->orderByDesc('verified_at')
            ->limit(8)
            ->get();

        return [
            'active_vacancies' => $vacancy['active'],
            'closing_soon' => $vacancy['closing_soon'],
            'new_verified' => $application['verified_this_week'],
            'screening' => (int) ($statusCounts[ApplicationStatus::Screening->value] ?? 0),
            'interview' => (int) ($statusCounts[ApplicationStatus::Interview->value] ?? 0),
            'offered' => (int) ($statusCounts[ApplicationStatus::Offered->value] ?? 0),
            'hired' => $application['hired'],
            'rejected' => $application['rejected'],
            'withdrawn' => (int) ($statusCounts[ApplicationStatus::Withdrawn->value] ?? 0),
            'by_vacancy' => $byVacancy,
            'recent' => $recent,
            'status_counts' => $statusCounts,
        ];
    }
}
