<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use App\Services\FileStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    protected $fileStorageService;

    public function __construct(FileStorageService $fileStorageService)
    {
        $this->fileStorageService = $fileStorageService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $projects = Project::withoutTrashed()
                ->select([
                    'id', 'title', 'slug', 'excerpt', 'category', 'client',
                    'project_date', 'is_published', 'sort_order', 'image', 'created_at',
                ])
                ->with(['createdBy:id,name', 'updatedBy:id,name']);

            return datatables()->of($projects)
                ->addColumn('created_by_name', fn ($row) => optional($row->createdBy)->name ?? '-')
                ->addColumn('updated_by_name', fn ($row) => optional($row->updatedBy)->name ?? '-')
                ->editColumn('image', function ($row) {
                    if ($row->image) {
                        $url = $this->fileStorageService->getFileUrl($row->image);
                        return '<img src="' . $url . '" alt="Project Image" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->editColumn('is_published', function ($row) {
                    if ($row->is_published) {
                        return '<span class="badge bg-label-success">Published</span>';
                    }
                    return '<span class="badge bg-label-secondary">Draft</span>';
                })
                ->editColumn('project_date', fn ($row) => $row->project_date?->format('d M Y') ?? '-')
                ->addColumn('image_url', fn ($row) => $row->image ? $this->fileStorageService->getFileUrl($row->image) : null)
                ->addColumn('aksi', fn ($row) => '')
                ->rawColumns(['image', 'is_published', 'aksi'])
                ->addIndexColumn()
                ->toJson();
        }

        $totalProjects   = Project::withoutTrashed()->count();
        $published       = Project::withoutTrashed()->where('is_published', true)->count();
        $unpublished     = Project::withoutTrashed()->where('is_published', false)->count();
        $recentProjects  = Project::withoutTrashed()->whereDate('created_at', '>=', now()->subDays(30))->count();

        return view('internal/project.index', compact(
            'totalProjects', 'published', 'unpublished', 'recentProjects'
        ));
    }

    public function store(ProjectRequest $request)
    {
        $validatedData = $request->validated();

        $uploadedFiles = [];

        try {
            DB::beginTransaction();

            foreach (['image', 'image_secondary', 'image_tertiary'] as $field) {
                if ($request->hasFile($field)) {
                    $result = $this->fileStorageService->uploadImage(
                        $request->file($field),
                        'projects/images'
                    );

                    if (!$result['success']) {
                        throw new \Exception('Failed to upload ' . $field . ': ' . $result['error']);
                    }

                    $validatedData[$field] = $result['path'];
                    $uploadedFiles[$field] = $result['path'];
                }
            }

            $validatedData['created_by']  = auth()->id();
            $validatedData['is_published'] = (bool) ($validatedData['is_published'] ?? false);
            $validatedData['slug']         = Str::slug($validatedData['title']);

            $project = Project::create($validatedData);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Project saved successfully!',
                'data'    => $project,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedFiles as $path) {
                $this->fileStorageService->deleteFile($path);
            }

            return response()->json([
                'status'  => 500,
                'message' => 'A server error occurred.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $project = Project::withoutTrashed()
                ->with(['createdBy:id,name', 'updatedBy:id,name'])
                ->findOrFail($id);

            $data               = $project->toArray();
            $data['image_url']            = $project->image ? $this->fileStorageService->getFileUrl($project->image) : null;
            $data['image_secondary_url']  = $project->image_secondary ? $this->fileStorageService->getFileUrl($project->image_secondary) : null;
            $data['image_tertiary_url']   = $project->image_tertiary ? $this->fileStorageService->getFileUrl($project->image_tertiary) : null;
            $data['project_date_raw']     = $project->project_date?->format('Y-m-d');

            return response()->json([
                'success' => true,
                'project' => $data,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 500,
                'message' => 'A server error occurred.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update($id, ProjectRequest $request)
    {
        $uploadedFiles = [];

        try {
            DB::beginTransaction();

            $project       = Project::withoutTrashed()->findOrFail($id);
            $validatedData = $request->validated();

            foreach (['image', 'image_secondary', 'image_tertiary'] as $field) {
                if ($request->hasFile($field)) {
                    $result = $this->fileStorageService->uploadImage(
                        $request->file($field),
                        'projects/images'
                    );

                    if (!$result['success']) {
                        throw new \Exception('Failed to upload ' . $field . ': ' . $result['error']);
                    }

                    $oldPath = $project->{$field};
                    $validatedData[$field] = $result['path'];
                    $uploadedFiles[$field] = $result['path'];

                    if ($oldPath) {
                        $this->fileStorageService->deleteFile($oldPath);
                    }
                }
            }

            $validatedData['updated_by']   = auth()->id();
            $validatedData['is_published'] = (bool) ($validatedData['is_published'] ?? false);

            if (isset($validatedData['title'])) {
                $validatedData['slug'] = Str::slug($validatedData['title']);
            }

            $project->update($validatedData);

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Project updated successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            foreach ($uploadedFiles as $path) {
                $this->fileStorageService->deleteFile($path);
            }

            return response()->json([
                'status'  => 500,
                'message' => 'A server error occurred.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $project = Project::withoutTrashed()->findOrFail($id);

            foreach (['image', 'image_secondary', 'image_tertiary'] as $field) {
                if ($project->{$field}) {
                    $this->fileStorageService->deleteFile($project->{$field});
                }
            }

            $project->deleted_by = auth()->id();
            $project->save();
            $project->delete();

            DB::commit();

            return response()->json([
                'status'  => 200,
                'message' => 'Project deleted successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status'  => 500,
                'message' => 'A server error occurred.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
