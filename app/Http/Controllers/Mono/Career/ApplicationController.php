<?php

namespace App\Http\Controllers\Mono\Career;

use App\Enums\Career\ApplicationStatus;
use App\Enums\Career\EmailVerificationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Career\ApplicationNoteStoreRequest;
use App\Http\Requests\Career\ApplicationTransitionRequest;
use App\Http\Requests\Career\AssignRecruiterRequest;
use App\Models\Career\JobApplication;
use App\Models\Career\JobApplicationDocument;
use App\Models\Career\JobApplicationNote;
use App\Models\Career\JobVacancy;
use App\Models\User;
use App\Queries\Career\CareerApplicationQuery;
use App\Queries\Career\CareerSummaryQuery;
use App\Services\Career\CandidateDocumentService;
use App\Services\Career\JobApplicationTransitionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected CareerApplicationQuery $query,
        protected JobApplicationTransitionService $transitions,
        protected CandidateDocumentService $documents,
        protected CareerSummaryQuery $summary,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', JobApplication::class);

        if ($request->ajax()) {
            $rows = $this->query->cmsBaseQuery($request);

            return datatables()->of($rows)
                ->addColumn('candidate_name', fn (JobApplication $row) => e($row->candidate?->full_name))
                ->addColumn('candidate_email', fn (JobApplication $row) => e($row->candidate?->email))
                ->addColumn('vacancy_title', fn (JobApplication $row) => e($row->vacancy?->title))
                ->addColumn('recruiter_name', fn (JobApplication $row) => e($row->assignedRecruiter?->name ?? '—'))
                ->editColumn('status', function (JobApplication $row) {
                    return '<span class="badge bg-label-primary">'.e($row->status->label()).'</span>';
                })
                ->editColumn('email_verification_status', function (JobApplication $row) {
                    $class = $row->email_verification_status === EmailVerificationStatus::Verified
                        ? 'bg-label-success'
                        : 'bg-label-warning';

                    return '<span class="badge '.$class.'">'.e($row->email_verification_status->label()).'</span>';
                })
                ->addColumn('aksi', fn () => '')
                ->rawColumns(['status', 'email_verification_status', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal.career.applications.index', [
            'stats' => $this->summary->applicationStatCards(),
            'statuses' => ApplicationStatus::options(),
            'verifications' => [
                EmailVerificationStatus::Pending->value => 'Pending',
                EmailVerificationStatus::Verified->value => 'Verified',
                EmailVerificationStatus::Expired->value => 'Expired',
            ],
            'vacancies' => JobVacancy::query()->select(['id', 'title', 'code'])->orderBy('title')->get(),
            'recruiters' => User::query()->select(['id', 'name'])->orderBy('name')->get(),
        ]);
    }

    public function show(string $id): View
    {
        $application = JobApplication::query()
            ->with([
                'vacancy:id,title,code,department,location_city,location_province',
                'candidate',
                'assignedRecruiter:id,name,email',
                'answers',
                'documents',
                'statusHistories.changedBy:id,name',
                'notes.createdBy:id,name',
            ])
            ->findOrFail($id);

        $this->authorize('view', $application);

        return view('internal.career.applications.show', [
            'application' => $application,
            'allowedTargets' => $this->transitions->allowedTargets($application->status),
            'statuses' => ApplicationStatus::options(),
            'recruiters' => User::query()->select(['id', 'name'])->orderBy('name')->get(),
        ]);
    }

    public function transition(ApplicationTransitionRequest $request, string $id): JsonResponse
    {
        $application = JobApplication::query()->findOrFail($id);
        $this->authorize('changeStatus', $application);

        $to = ApplicationStatus::from($request->validated('to_status'));

        if ($to === ApplicationStatus::Rejected) {
            $this->authorize('reject', $application);
        }

        $this->transitions->transition($application, $to, $request->user(), $request->only([
            'reason_code', 'public_message', 'internal_note',
        ]));

        return response()->json(['status' => 200, 'message' => 'Status lamaran diperbarui.']);
    }

    public function assign(AssignRecruiterRequest $request, string $id): JsonResponse
    {
        $application = JobApplication::query()->findOrFail($id);
        $this->authorize('assign', $application);

        $application->forceFill([
            'assigned_recruiter_id' => $request->validated('assigned_recruiter_id'),
        ])->save();

        return response()->json(['status' => 200, 'message' => 'Rekruter ditetapkan.']);
    }

    public function storeNote(ApplicationNoteStoreRequest $request, string $id): JsonResponse
    {
        $application = JobApplication::query()->findOrFail($id);
        $this->authorize('createNote', $application);

        $note = $application->notes()->create([
            'note' => $request->validated('note'),
            'is_pinned' => $request->boolean('is_pinned'),
            'created_by' => $request->user()->id,
        ]);

        return response()->json(['status' => 200, 'message' => 'Catatan ditambahkan.', 'data' => $note]);
    }

    public function destroyNote(string $id, string $noteId): JsonResponse
    {
        $application = JobApplication::query()->findOrFail($id);
        $this->authorize('deleteNote', $application);

        JobApplicationNote::query()
            ->where('job_application_id', $application->id)
            ->whereKey($noteId)
            ->firstOrFail()
            ->delete();

        return response()->json(['status' => 200, 'message' => 'Catatan dihapus.']);
    }

    public function downloadDocument(Request $request, string $id, string $documentId): StreamedResponse
    {
        $document = JobApplicationDocument::query()
            ->where('job_application_id', $id)
            ->findOrFail($documentId);

        $this->authorize('download', $document);

        return $this->documents->download(
            $document,
            $request->user(),
            (string) $request->ip(),
            $request->userAgent(),
        );
    }
}
