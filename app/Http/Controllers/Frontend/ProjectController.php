<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\FileStorageService;
use App\Services\ProjectMapService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(
        protected FileStorageService $fileStorageService,
        protected ProjectMapService $projectMapService,
    ) {
    }

    public function index(Request $request): View
    {
        $statusFilter = ProjectStatus::tryFromMixed($request->query('status'))?->value;

        $projects = Project::forListing()
            ->status($statusFilter)
            ->paginate(12)
            ->withQueryString()
            ->through(function (Project $project) {
                $project->setAttribute(
                    'image_url',
                    $project->image
                        ? $this->tryUrl($project->image)
                        : asset('frontend/img/placeholder.jpg')
                );

                return $project;
            });

        // One optimized payload for map markers + status counts (no N+1).
        $projectMap = $this->projectMapService->buildFrontendPayload('status');

        return view('frontend.projects.index', [
            'projects' => $projects,
            'projectMap' => $projectMap,
            'statusFilter' => $statusFilter,
            'statusOptions' => ProjectStatus::options(),
        ]);
    }

    public function show(string $slug): View
    {
        $project = Project::forDetail()
            ->where('slug', $slug)
            ->firstOrFail();

        $project->setAttribute(
            'image_url',
            $project->image
                ? $this->tryUrl($project->image)
                : asset('frontend/img/placeholder.jpg')
        );

        $relatedProjects = Project::forListing()
            ->where('id', '!=', $project->id)
            ->take(4)
            ->get()
            ->each(function (Project $related) {
                $related->setAttribute(
                    'image_url',
                    $related->image
                        ? $this->tryUrl($related->image)
                        : asset('frontend/img/placeholder.jpg')
                );
            });

        return view('frontend.projects.detail', compact('project', 'relatedProjects'));
    }

    private function tryUrl(string $path): string
    {
        try {
            return $this->fileStorageService->getFileUrl($path);
        } catch (\Exception $e) {
            return asset('frontend/img/placeholder.jpg');
        }
    }
}
