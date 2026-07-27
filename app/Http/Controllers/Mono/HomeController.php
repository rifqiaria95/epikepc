<?php

namespace App\Http\Controllers\Mono;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\CompanyJourney;
use App\Models\CompanyMilestone;
use App\Models\Galeri;
use App\Models\News;
use App\Models\Organisasi;
use App\Models\Pricing;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimoni;
use App\Services\FileStorageService;
use App\Services\ProjectMapService;

class HomeController extends Controller
{
    protected $fileStorageService;

    public function __construct(
        FileStorageService $fileStorageService,
        protected ProjectMapService $projectMapService,
    ) {
        $this->fileStorageService = $fileStorageService;
    }

    /**
     * Display homepage with about, published news, and testimonials
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get about data
        $about = About::withoutTrashed()->latest()->first();
        
        // Process about image URL
        if ($about) {
            if ($about->image) {
                try {
                    $about->image_url = $this->fileStorageService->getFileUrl($about->image);
                } catch (\Exception $e) {
                    $about->image_url = asset('frontend/img/bg-img/shape1.jpg');
                }
            } else {
                $about->image_url = asset('frontend/img/bg-img/shape1.jpg');
            }
        }
        
        // Get testimonials for homepage
        $testimonials = Testimoni::forHomepage()->get();

        // Get services for homepage (image_url resolved via model accessor)
        $services = Service::forHomepage()->get();

        // Get pricing plans for homepage (eager-loaded to avoid N+1)
        $pricingPlans = Pricing::forHomepage()->get();

        // Top 4 portfolio projects by value (highest first)
        $projects = Project::forHomepage(4)->get();

        // Map markers + counts from DB (single optimized payload, no N+1)
        $projectMap = $this->projectMapService->buildFrontendPayload('category');

        // Get news for homepage
        $news = News::forHomepage()->get();

        // Get gallery items for homepage section
        $galleryItems = Galeri::query()
            ->withoutTrashed()
            ->with('kategoriGaleri:id,name')
            ->latest('created_at')
            ->take(6)
            ->get();

        $galleryItems->each(function ($item) {
            if ($item->image) {
                try {
                    $item->image_url = $this->fileStorageService->getFileUrl($item->image);
                } catch (\Exception $e) {
                    $item->image_url = asset('frontend/img/img-1.png');
                }
            } else {
                $item->image_url = asset('frontend/img/img-1.png');
            }
        });

        // Get team members for board slider
        $teamMembers = Organisasi::withoutTrashed()->orderBy('tahun')->take(6)->get();
        $teamMembers->each(function ($member) {
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

        $companyJourney = CompanyJourney::query()->firstOrCreate(
            ['id' => 1],
            CompanyJourney::defaults()
        );

        if ($companyJourney->video_poster) {
            try {
                $companyJourney->poster_url = $this->fileStorageService->getFileUrl($companyJourney->video_poster);
            } catch (\Exception $e) {
                $companyJourney->poster_url = null;
            }
        }

        $companyMilestones = CompanyMilestone::forHomepage()->get();

        return view('index', compact(
            'about',
            'testimonials',
            'services',
            'pricingPlans',
            'projects',
            'projectMap',
            'news',
            'galleryItems',
            'teamMembers',
            'companyJourney',
            'companyMilestones'
        ));
    }
}
