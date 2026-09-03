<?php

namespace App\Http\Controllers\Mono\Career;

use App\Http\Controllers\Controller;
use App\Models\Career\Candidate;
use App\Queries\Career\CareerSummaryQuery;
use App\Support\Career\QueryLike;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CandidateController extends Controller
{
    use AuthorizesRequests;
    use QueryLike;

    public function __construct(
        protected CareerSummaryQuery $summary,
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $this->authorize('viewAny', Candidate::class);

        if ($request->ajax()) {
            $query = Candidate::query()
                ->select([
                    'id', 'full_name', 'email', 'phone', 'domicile_city', 'domicile_province',
                    'highest_education', 'total_experience_years', 'created_at',
                ])
                ->withCount('applications');

            if ($search = $this->datatablesSearchTerm($request)) {
                $like = $this->likeOperator();
                $query->where(function (Builder $q) use ($search, $like) {
                    $q->where('full_name', $like, "%{$search}%")
                        ->orWhere('email', $like, "%{$search}%")
                        ->orWhere('phone', $like, "%{$search}%")
                        ->orWhere('normalized_phone', $like, "%{$search}%");
                });
            }

            return datatables()->of($query)
                ->addColumn('aksi', fn () => '')
                ->addIndexColumn()
                ->toJson();
        }

        return view('internal.career.candidates.index', [
            'stats' => $this->summary->candidateStatCards(),
        ]);
    }

    public function show(string $id): View
    {
        $candidate = Candidate::query()
            ->with([
                'applications' => fn ($q) => $q
                    ->select([
                        'id', 'reference_number', 'job_vacancy_id', 'candidate_id',
                        'status', 'email_verification_status', 'submitted_at', 'created_at',
                    ])
                    ->with(['vacancy:id,title,code', 'documents'])
                    ->orderByDesc('created_at'),
            ])
            ->findOrFail($id);

        $this->authorize('view', $candidate);

        return view('internal.career.candidates.show', [
            'candidate' => $candidate,
        ]);
    }
}
