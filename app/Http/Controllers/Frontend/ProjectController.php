<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\FileStorageService;

class ProjectController extends Controller
{
    public function __construct(protected FileStorageService $fileStorageService)
    {
    }

    public function index()
    {
        $projects = Project::forListing()
            ->paginate(12)
            ->through(function ($project) {
                $project->image_url = $project->image
                    ? $this->tryUrl($project->image)
                    : asset('frontend/img/placeholder.jpg');
                return $project;
            });

        return view('frontend.projects.index', compact('projects'));
    }

    public function show(string $slug)
    {
        $project = Project::forDetail()
            ->where('slug', $slug)
            ->firstOrFail();

        $project->image_url = $project->image
            ? $this->tryUrl($project->image)
            : asset('frontend/img/placeholder.jpg');

        $relatedProjects = Project::forListing()
            ->where('id', '!=', $project->id)
            ->take(4)
            ->get()
            ->each(function ($related) {
                $related->image_url = $related->image
                    ? $this->tryUrl($related->image)
                    : asset('frontend/img/placeholder.jpg');
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
