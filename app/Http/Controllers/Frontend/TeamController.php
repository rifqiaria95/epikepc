<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Organisasi;
use App\Services\FileStorageService;

class TeamController extends Controller
{
    public function __construct(protected FileStorageService $fileStorageService)
    {
    }

    public function index()
    {
        $members = Organisasi::withoutTrashed()->orderBy('tahun')->get();

        $members->each(function ($member) {
            if ($member->image) {
                try {
                    $member->image_url = $this->fileStorageService->getFileUrl($member->image);
                } catch (\Exception $e) {
                    $member->image_url = asset('frontend/img/placeholder.jpg');
                }
            } else {
                $member->image_url = asset('frontend/img/placeholder.jpg');
            }
        });

        return view('frontend.team.index', compact('members'));
    }
}
