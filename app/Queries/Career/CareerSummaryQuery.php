<?php

namespace App\Queries\Career;

use App\Enums\Career\ApplicationStatus;
use App\Enums\Career\EmailVerificationStatus;
use App\Enums\Career\VacancyStatus;
use App\Support\Cms\StatCardPresenter;
use Illuminate\Support\Facades\DB;

class CareerSummaryQuery
{
    public function __construct(
        protected StatCardPresenter $presenter,
    ) {}

    /** @return array<string, int> */
    public function vacancyMetrics(): array
    {
        $now = now();
        $soon = $now->copy()->addDays(14);

        $row = DB::table('job_vacancies')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as draft',
                [VacancyStatus::Draft->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as published',
                [VacancyStatus::Published->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as closed',
                [VacancyStatus::Closed->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as archived',
                [VacancyStatus::Archived->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? AND published_at IS NOT NULL AND published_at <= ? AND (closes_at IS NULL OR closes_at > ?) THEN 1 ELSE 0 END) as active',
                [VacancyStatus::Published->value, $now, $now],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? AND closes_at IS NOT NULL AND closes_at BETWEEN ? AND ? THEN 1 ELSE 0 END) as closing_soon',
                [VacancyStatus::Published->value, $now, $soon],
            )
            ->first();

        $applications = (int) DB::table('job_applications')->count();

        return [
            'total' => (int) ($row->total ?? 0),
            'draft' => (int) ($row->draft ?? 0),
            'published' => (int) ($row->published ?? 0),
            'closed' => (int) ($row->closed ?? 0),
            'archived' => (int) ($row->archived ?? 0),
            'active' => (int) ($row->active ?? 0),
            'closing_soon' => (int) ($row->closing_soon ?? 0),
            'applications' => $applications,
        ];
    }

    /** @return array<string, int> */
    public function applicationMetrics(): array
    {
        $weekAgo = now()->subDays(7);
        $pipeline = $this->quotedStatuses([
            ApplicationStatus::Screening,
            ApplicationStatus::Shortlisted,
            ApplicationStatus::Interview,
            ApplicationStatus::Offered,
        ]);

        $row = DB::table('job_applications')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as pending_verification',
                [ApplicationStatus::PendingVerification->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN email_verification_status = ? AND status != ? THEN 1 ELSE 0 END) as verified_ready',
                [EmailVerificationStatus::Verified->value, ApplicationStatus::PendingVerification->value],
            )
            ->selectRaw(
                "SUM(CASE WHEN status IN ({$pipeline}) THEN 1 ELSE 0 END) as in_pipeline",
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as hired',
                [ApplicationStatus::Hired->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as rejected',
                [ApplicationStatus::Rejected->value],
            )
            ->selectRaw(
                'SUM(CASE WHEN email_verification_status = ? AND verified_at >= ? THEN 1 ELSE 0 END) as verified_this_week',
                [EmailVerificationStatus::Verified->value, $weekAgo],
            )
            ->first();

        return [
            'total' => (int) ($row->total ?? 0),
            'pending_verification' => (int) ($row->pending_verification ?? 0),
            'verified_ready' => (int) ($row->verified_ready ?? 0),
            'in_pipeline' => (int) ($row->in_pipeline ?? 0),
            'hired' => (int) ($row->hired ?? 0),
            'rejected' => (int) ($row->rejected ?? 0),
            'verified_this_week' => (int) ($row->verified_this_week ?? 0),
        ];
    }

    /** @return array<string, int> */
    public function candidateMetrics(): array
    {
        $weekAgo = now()->subDays(7);
        $terminal = $this->quotedStatuses([
            ApplicationStatus::Hired,
            ApplicationStatus::Rejected,
            ApplicationStatus::Withdrawn,
            ApplicationStatus::Expired,
        ]);

        $row = DB::table('candidates')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw(
                'SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_this_week',
                [$weekAgo],
            )
            ->first();

        $repeatApplicants = (int) DB::query()
            ->fromSub(
                DB::table('job_applications')
                    ->select('candidate_id')
                    ->groupBy('candidate_id')
                    ->havingRaw('COUNT(*) > 1'),
                'repeat_candidates',
            )
            ->count();

        $withActiveApplications = (int) DB::table('candidates')
            ->whereExists(function ($query) use ($terminal) {
                $query->select(DB::raw(1))
                    ->from('job_applications')
                    ->whereColumn('job_applications.candidate_id', 'candidates.id')
                    ->whereRaw("status NOT IN ({$terminal})");
            })
            ->count();

        $withApplications = (int) DB::table('candidates')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('job_applications')
                    ->whereColumn('job_applications.candidate_id', 'candidates.id');
            })
            ->count();

        return [
            'total' => (int) ($row->total ?? 0),
            'new_this_week' => (int) ($row->new_this_week ?? 0),
            'repeat_applicants' => $repeatApplicants,
            'with_active_applications' => $withActiveApplications,
            'with_applications' => $withApplications,
        ];
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function vacancyStatCards(): array
    {
        return $this->presenter->present([
            ['key' => 'total', 'label' => 'Total Lowongan', 'hint' => 'Semua status', 'icon' => 'ti-briefcase', 'color' => 'primary'],
            ['key' => 'active', 'label' => 'Lowongan Aktif', 'hint' => 'Dipublikasikan & masih buka', 'icon' => 'ti-circle-check', 'color' => 'success'],
            ['key' => 'draft', 'label' => 'Draft', 'hint' => 'Belum dipublikasikan', 'icon' => 'ti-file-pencil', 'color' => 'warning'],
            ['key' => 'closing_soon', 'label' => 'Segera Tutup', 'hint' => '14 hari ke depan', 'icon' => 'ti-clock', 'color' => 'info'],
        ], $this->vacancyMetrics());
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function applicationStatCards(): array
    {
        return $this->presenter->present([
            ['key' => 'total', 'label' => 'Total Lamaran', 'hint' => 'Semua lamaran', 'icon' => 'ti-mail', 'color' => 'primary'],
            ['key' => 'pending_verification', 'label' => 'Menunggu Verifikasi', 'hint' => 'Belum terverifikasi email', 'icon' => 'ti-mail-forward', 'color' => 'warning'],
            ['key' => 'in_pipeline', 'label' => 'Pipeline Aktif', 'hint' => 'Screening hingga offered', 'icon' => 'ti-arrows-shuffle', 'color' => 'info'],
            ['key' => 'hired', 'label' => 'Hired', 'hint' => 'Berhasil diterima', 'icon' => 'ti-user-check', 'color' => 'success'],
        ], $this->applicationMetrics());
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function candidateStatCards(): array
    {
        return $this->presenter->present([
            ['key' => 'total', 'label' => 'Total Kandidat', 'hint' => 'Profil unik', 'icon' => 'ti-users', 'color' => 'primary'],
            ['key' => 'new_this_week', 'label' => 'Baru (7 Hari)', 'hint' => 'Kandidat terdaftar minggu ini', 'icon' => 'ti-user-plus', 'color' => 'success'],
            ['key' => 'repeat_applicants', 'label' => 'Multi-lamaran', 'hint' => 'Lebih dari 1 lamaran', 'icon' => 'ti-layers-linked', 'color' => 'info'],
            ['key' => 'with_active_applications', 'label' => 'Lamaran Aktif', 'hint' => 'Masih dalam proses', 'icon' => 'ti-activity', 'color' => 'warning'],
        ], $this->candidateMetrics());
    }

    /**
     * @param  array<int, ApplicationStatus>  $statuses
     */
    protected function quotedStatuses(array $statuses): string
    {
        return collect($statuses)
            ->map(fn (ApplicationStatus $status) => "'".$status->value."'")
            ->implode(',');
    }
}
