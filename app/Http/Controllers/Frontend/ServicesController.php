<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\Testimoni;
use App\Services\ServiceGalleryAssembler;

class ServicesController extends Controller
{
    public function __construct(
        protected ServiceGalleryAssembler $galleryAssembler,
    ) {}

    public function index()
    {
        $services = Service::query()
            ->withoutTrashed()
            ->select(['id', 'title', 'subtitle', 'description', 'image', 'service_type_id', 'created_at'])
            ->with(['serviceType:id,name,slug'])
            ->orderByDesc('created_at')
            ->get();

        $galleryItems = $this->galleryAssembler->assemble(8);
        $contact = config('frontend_contact');
        $featureImage = $galleryItems->first()['url']
            ?? asset('frontend/img/img-1.png');
        $skillsImage = $galleryItems->skip(1)->first()['url']
            ?? $featureImage;

        return view('frontend.services.index', compact(
            'services',
            'galleryItems',
            'contact',
            'featureImage',
            'skillsImage',
        ));
    }

    public function showByServiceType(string $slug)
    {
        $serviceType = ServiceType::query()
            ->where('slug', $slug)
            ->firstOrFail();

        $services = Service::query()
            ->withoutTrashed()
            ->where('service_type_id', $serviceType->id)
            ->with([
                'serviceType:id,name,slug',
                'serviceDetails:id,service_id,title,subtitle,price,description',
            ])
            ->orderByDesc('created_at')
            ->get();

        $allServiceTypes = ServiceType::query()
            ->select(['id', 'name', 'slug', 'type'])
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        return view('frontend.services.by-type', compact('serviceType', 'services', 'allServiceTypes'));
    }

    public function detailService(int|string $id)
    {
        if (! is_numeric($id)) {
            abort(404);
        }

        $service = Service::forDetail()
            ->where('id', $id)
            ->firstOrFail();

        $testimonials = Testimoni::forHomepage(4)->get();

        $epcServiceItems = $service->serviceFeatures
            ->pluck('feature')
            ->filter()
            ->values()
            ->all();

        if ($epcServiceItems === []) {
            $epcServiceItems = [
                'Engineering, procurement, and construction delivery',
                'Oil & gas infrastructure execution',
                'Pipeline, metering, and facility works',
                'HSE-aligned project controls',
            ];
        }

        $serviceFaqs = $service->serviceFaqs
            ->map(fn ($faq) => [
                'question' => $faq->question,
                'answer' => $faq->answer,
            ])
            ->values()
            ->all();

        if ($serviceFaqs === []) {
            $serviceFaqs = [
                [
                    'question' => 'How can EPIK support this service scope?',
                    'answer' => 'Our team delivers end-to-end engineering and construction support for oil & gas infrastructure, from planning and procurement through site execution and commissioning.',
                ],
                [
                    'question' => 'How do we start a project consultation?',
                    'answer' => 'Share your project requirements through the Contact page. Our engineering team will review scope, schedule, and delivery approach with you.',
                ],
            ];
        }

        $subServices = $service->serviceDetails;

        return view('frontend.services.detail', compact(
            'service',
            'testimonials',
            'epcServiceItems',
            'serviceFaqs',
            'subServices',
        ));
    }

    public function kaiflowServices()
    {
        $service = Service::query()
            ->withoutTrashed()
            ->where('id', 2)
            ->firstOrFail();

        $recentServices = Service::query()
            ->withoutTrashed()
            ->select(['id', 'title', 'subtitle', 'image', 'created_at'])
            ->where('id', '!=', $service->id)
            ->orderByDesc('created_at')
            ->take(3)
            ->get();

        return view('frontend.services.kaiflow.index', compact('service', 'recentServices'));
    }
}
