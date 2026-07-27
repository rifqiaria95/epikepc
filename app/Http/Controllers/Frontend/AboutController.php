<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\CompanyJourney;
use App\Models\Galeri;
use App\Models\Service;
use App\Models\Testimoni;
use App\Services\FileStorageService;

class AboutController extends Controller
{
    public function __construct(protected FileStorageService $fileStorageService)
    {
    }

    public function index()
    {
        // About content
        $about = About::withoutTrashed()->latest()->first();
        if ($about) {
            $about->image_url = $about->image
                ? $this->tryUrl($about->image)
                : asset('frontend/img/placeholder.jpg');
        }

        // Services for services_slider
        $services = Service::withoutTrashed()
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get()
            ->each(function ($service) {
                $service->image_url = $service->image
                    ? $this->tryUrl($service->image)
                    : asset('frontend/img/placeholder.jpg');
            });

        // Gallery for gallery_list
        $gallery = collect();
        if (class_exists(\App\Models\Galeri::class)) {
            $gallery = Galeri::withoutTrashed()
                ->orderBy('created_at', 'desc')
                ->take(4)
                ->get()
                ->each(function ($item) {
                    $item->image_url = $item->image
                        ? $this->tryUrl($item->image)
                        : asset('frontend/img/placeholder.jpg');
                });
        }

        // Testimonials for reviews_slider
        $testimonials = Testimoni::forHomepage()->get();

        // Vision & Mission content sourced from company profile deck (PPT)
        $visionMission = [
            'vision' => 'To be a trusted and leading engineering, procurement, and construction partner for sustainable oil and gas infrastructure development in Indonesia.',
            'missions' => [
                'Deliver integrated EPC and operation & maintenance services with strong standards in safety, quality, and execution reliability.',
                'Build long-term value for clients through innovation, professional project management, and timely delivery.',
                'Support national energy resilience by developing infrastructure aligned with industry regulations and global certifications.',
            ],
        ];

        // Company profile video (video_url + poster from company_journeys)
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

        return view('frontend.about.index', compact(
            'about',
            'services',
            'gallery',
            'testimonials',
            'visionMission',
            'companyJourney'
        ));
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
