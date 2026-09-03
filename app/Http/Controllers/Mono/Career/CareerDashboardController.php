<?php

namespace App\Http\Controllers\Mono\Career;

use App\Http\Controllers\Controller;
use App\Queries\Career\CareerApplicationQuery;
use Illuminate\View\View;

class CareerDashboardController extends Controller
{
    public function __construct(
        protected CareerApplicationQuery $applications,
    ) {}

    public function index(): View
    {
        abort_unless(request()->user()?->can('view_career_dashboard'), 403);

        return view('internal.career.dashboard.index', [
            'metrics' => $this->applications->overviewMetrics(),
        ]);
    }
}
