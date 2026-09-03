<?php

namespace App\Http\Controllers\Frontend\Career;

use App\Enums\Career\EmploymentType;
use App\Enums\Career\WorkArrangement;
use App\Http\Controllers\Controller;
use App\Http\Requests\Career\PublicApplicationStoreRequest;
use App\Http\Requests\Career\ResendVerificationRequest;
use App\Models\Career\JobVacancy;
use App\Queries\Career\CareerVacancyQuery;
use App\Services\Career\JobApplicationSubmissionService;
use App\Services\Career\JobApplicationVerificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function __construct(
        protected CareerVacancyQuery $vacancies,
        protected JobApplicationSubmissionService $submissions,
        protected JobApplicationVerificationService $verifications,
    ) {}

    public function index(Request $request): View
    {
        return view('frontend.careers.index', [
            'vacancies' => $this->vacancies->publicListing($request),
            'filters' => $this->vacancies->filterOptions(),
            'employmentTypes' => EmploymentType::options(),
            'workArrangements' => WorkArrangement::options(),
        ]);
    }

    public function show(string $slug): View
    {
        $vacancy = $this->vacancies->findPublicBySlug($slug);
        $related = $this->vacancies->relatedPublic($vacancy);

        return view('frontend.careers.show', [
            'vacancy' => $vacancy,
            'related' => $related,
            'acceptsApplications' => $vacancy->acceptsApplications(),
        ]);
    }

    public function applyForm(string $slug): View|RedirectResponse
    {
        $vacancy = $this->vacancies->findPublicBySlug($slug);

        if (! $vacancy->acceptsApplications()) {
            return redirect()
                ->route('frontend.careers.show', $vacancy->slug)
                ->with('error', 'Lowongan ini sudah ditutup dan tidak lagi menerima lamaran.');
        }

        return view('frontend.careers.apply', [
            'vacancy' => $vacancy,
            'maxCvKb' => (int) config('career.documents.max_cv_kilobytes', 5120),
            'privacyUrl' => config('career.privacy.notice_url'),
            'retentionMonths' => (int) config('career.privacy.retention_months'),
        ]);
    }

    public function store(PublicApplicationStoreRequest $request, JobVacancy $vacancy): JsonResponse|RedirectResponse
    {
        $vacancy->load('questions');

        $this->submissions->submit(
            $vacancy,
            $request->candidatePayload() + ['answers' => $request->input('answers', [])],
            $request->file('cv'),
            (string) $request->ip(),
        );

        $message = 'Kami telah menerima data Anda. Jika alamat email valid, tautan verifikasi akan dikirim. Periksa kotak masuk dan folder spam.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'redirect' => route('frontend.careers.received', $vacancy->slug),
            ]);
        }

        return redirect()
            ->route('frontend.careers.received', $vacancy->slug)
            ->with('status', $message)
            ->with('resend_email', $request->input('email'));
    }

    public function received(string $slug): View
    {
        $vacancy = $this->vacancies->findPublicBySlug($slug);

        return view('frontend.careers.received', [
            'vacancy' => $vacancy,
        ]);
    }

    public function verify(string $token): View
    {
        $application = $this->verifications->verify($token);

        return view('frontend.careers.verified', [
            'application' => $application,
            'public' => $application->toPublicStatusPayload(),
        ]);
    }

    public function resend(ResendVerificationRequest $request, JobVacancy $vacancy): JsonResponse|RedirectResponse
    {
        $this->verifications->resend(
            (string) $request->input('email'),
            $vacancy->id,
            (string) $request->ip(),
        );

        $message = 'Jika alamat email valid dan masih menunggu verifikasi, tautan baru telah dikirim.';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('status', $message);
    }

    public function status(string $token): View
    {
        $payload = $this->verifications->statusByToken($token);

        return view('frontend.careers.status', [
            'public' => $payload['public'],
            'token' => $token,
            'allowWithdrawal' => (bool) config('career.features.allow_withdrawal'),
        ]);
    }

    public function withdraw(Request $request, string $token): RedirectResponse
    {
        $this->verifications->withdraw($token, $request->input('reason'));

        return redirect()
            ->route('frontend.careers.status', $token)
            ->with('status', 'Lamaran Anda telah ditarik.');
    }
}
