<?php

namespace App\Services;

use App\Enums\ProjectStatus;
use App\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectMapService
{
    public function __construct(protected FileStorageService $fileStorageService)
    {
    }

    /**
     * Build map payload once for frontend (markers + status counts).
     * Uses two efficient queries — no N+1, no per-marker API calls.
     *
     * @return array{
     *     markers: list<array<string, mixed>>,
     *     counts: array{all: int, ongoing: int, completed: int},
     *     config: array{filterMode: string, leafletBasePath: string}
     * }
     */
    public function buildFrontendPayload(string $filterMode = 'status'): array
    {
        $markers = $this->getMapMarkers();
        $counts = $this->getPublishedStatusCounts();

        return [
            'markers' => $markers->values()->all(),
            'counts' => $counts,
            'config' => [
                'filterMode' => $filterMode,
                'leafletBasePath' => asset('frontend/img/leaflet'),
            ],
        ];
    }

    /**
     * Slim published projects with coordinates for Leaflet markers.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getMapMarkers(): Collection
    {
        return Project::query()
            ->forMap()
            ->get()
            ->map(fn (Project $project) => $this->transformMarker($project));
    }

    /**
     * Aggregate status counts in a single GROUP BY query.
     *
     * @return array{all: int, ongoing: int, completed: int}
     */
    public function getPublishedStatusCounts(): array
    {
        $rows = Project::query()
            ->withoutTrashed()
            ->where('is_published', true)
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $ongoing = (int) ($rows[ProjectStatus::Ongoing->value] ?? 0);
        $completed = (int) ($rows[ProjectStatus::Completed->value] ?? 0);

        return [
            'all' => $ongoing + $completed,
            'ongoing' => $ongoing,
            'completed' => $completed,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function transformMarker(Project $project): array
    {
        $status = $project->status instanceof ProjectStatus
            ? $project->status
            : ProjectStatus::tryFromMixed($project->status) ?? ProjectStatus::Completed;

        $categoryLabel = filled($project->category)
            ? trim((string) Str::of($project->category)->before(','))
            : 'Project';

        $categoryKey = Str::slug($categoryLabel) ?: 'project';

        return [
            'id' => $project->id,
            'slug' => $project->slug,
            'lat' => (float) $project->latitude,
            'lng' => (float) $project->longitude,
            'name' => $project->title,
            'city' => $project->location ?: 'Indonesia',
            'year' => $project->project_date?->format('Y') ?: (string) $project->created_at?->format('Y'),
            'description' => Str::limit(strip_tags((string) $project->excerpt), 180),
            'category' => $categoryKey,
            'categoryLabel' => $categoryLabel,
            'status' => $status->value,
            'statusLabel' => $status->label(),
            'url' => route('frontend.projects.show', $project->slug),
            'image' => $this->resolveImageUrl($project->image),
        ];
    }

    protected function resolveImageUrl(?string $path): string
    {
        if (blank($path)) {
            return asset('frontend/img/placeholder.jpg');
        }

        try {
            return $this->fileStorageService->getFileUrl($path);
        } catch (\Throwable) {
            return asset('frontend/img/placeholder.jpg');
        }
    }
}
