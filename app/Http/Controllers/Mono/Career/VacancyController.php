<?php

namespace App\Http\Controllers\Mono\Career;

use App\Enums\Career\EmploymentType;
use App\Enums\Career\ExperienceLevel;
use App\Enums\Career\QuestionType;
use App\Enums\Career\VacancyStatus;
use App\Enums\Career\WorkArrangement;
use App\Http\Controllers\Controller;
use App\Http\Requests\Career\VacancyStoreRequest;
use App\Models\Career\JobVacancy;
use App\Queries\Career\CareerSummaryQuery;
use App\Queries\Career\CareerVacancyQuery;
use App\Services\Career\VacancyManagementService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VacancyController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected CareerVacancyQuery $query,
        protected VacancyManagementService $manager,
        protected CareerSummaryQuery $summary,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', JobVacancy::class);

        if ($request->ajax()) {
            $rows = $this->query->cmsBaseQuery($request);

            return datatables()->of($rows)
                ->editColumn('status', function (JobVacancy $row) {
                    $class = match ($row->status) {
                        VacancyStatus::Published => 'bg-label-success',
                        VacancyStatus::Closed => 'bg-label-secondary',
                        VacancyStatus::Archived => 'bg-label-dark',
                        default => 'bg-label-warning',
                    };

                    return '<span class="badge '.$class.'">'.e($row->status->label()).'</span>';
                })
                ->addColumn('location', fn (JobVacancy $row) => e($row->locationLabel()))
                ->addColumn('applications_count', fn (JobVacancy $row) => (int) $row->applications_count)
                ->addColumn('aksi', fn () => '')
                ->rawColumns(['status', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal.career.vacancies.index', array_merge($this->formOptions(), [
            'stats' => $this->summary->vacancyStatCards(),
        ]));
    }

    public function store(VacancyStoreRequest $request): JsonResponse
    {
        $this->authorize('create', JobVacancy::class);

        $vacancy = $this->manager->create(
            $request->validated(),
            $request->input('questions', []),
            $request->user(),
        );

        return response()->json([
            'status' => 200,
            'message' => 'Lowongan disimpan sebagai draft.',
            'data' => $vacancy,
        ]);
    }

    public function edit(string $id): JsonResponse
    {
        $vacancy = JobVacancy::query()->with('questions')->findOrFail($id);
        $this->authorize('view', $vacancy);

        return response()->json([
            'success' => true,
            'vacancy' => $vacancy,
        ]);
    }

    public function update(VacancyStoreRequest $request, string $id): JsonResponse
    {
        $vacancy = JobVacancy::query()->findOrFail($id);
        $this->authorize('update', $vacancy);

        $this->manager->update($vacancy, $request->validated(), $request->input('questions', []), $request->user());

        return response()->json([
            'status' => 200,
            'message' => 'Lowongan diperbarui.',
        ]);
    }

    public function publish(string $id): JsonResponse
    {
        $vacancy = JobVacancy::query()->findOrFail($id);
        $this->authorize('publish', $vacancy);
        $this->manager->publish($vacancy, request()->user());

        return response()->json(['status' => 200, 'message' => 'Lowongan dipublikasikan.']);
    }

    public function close(string $id): JsonResponse
    {
        $vacancy = JobVacancy::query()->findOrFail($id);
        $this->authorize('close', $vacancy);
        $this->manager->close($vacancy, request()->user());

        return response()->json(['status' => 200, 'message' => 'Lowongan ditutup.']);
    }

    public function archive(string $id): JsonResponse
    {
        $vacancy = JobVacancy::query()->findOrFail($id);
        $this->authorize('archive', $vacancy);
        $this->manager->archive($vacancy, request()->user());

        return response()->json(['status' => 200, 'message' => 'Lowongan diarsipkan.']);
    }

    public function duplicate(string $id): JsonResponse
    {
        $vacancy = JobVacancy::query()->findOrFail($id);
        $this->authorize('create', JobVacancy::class);
        $copy = $this->manager->duplicate($vacancy, request()->user());

        return response()->json([
            'status' => 200,
            'message' => 'Salinan lowongan dibuat sebagai draft.',
            'data' => $copy,
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $vacancy = JobVacancy::query()->findOrFail($id);
        $this->authorize('delete', $vacancy);
        $this->manager->delete($vacancy, request()->user());

        return response()->json(['status' => 200, 'message' => 'Lowongan dihapus.']);
    }

    /**
     * @return array<string, mixed>
     */
    protected function formOptions(): array
    {
        return [
            'employmentTypes' => EmploymentType::options(),
            'workArrangements' => WorkArrangement::options(),
            'experienceLevels' => ExperienceLevel::options(),
            'statuses' => VacancyStatus::options(),
            'questionTypes' => QuestionType::options(),
        ];
    }
}
