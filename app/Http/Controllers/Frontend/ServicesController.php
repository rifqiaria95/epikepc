<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceType;
use App\Models\Testimoni;

class ServicesController extends Controller
{
    public function index()
    {
        $service_type = ServiceType::query()
            ->select(['id', 'name', 'slug', 'type'])
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $services = Service::query()
            ->withoutTrashed()
            ->select(['id', 'title', 'subtitle', 'description', 'image', 'service_type_id', 'created_at'])
            ->with(['serviceType:id,name,slug'])
            ->orderByDesc('created_at')
            ->get();

        return view('frontend.services.index', compact('service_type', 'services'));
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

        $epcServiceItems = [
            'Gas Compressor Package',
            'Pipeline & Piping For Oil & Gas',
            'Onshore Civil & Building Work Include Piling Work',
            'Telecommunication Infrastructure',
            'Natural Gas, CNG & LNG Facilities',
            'SO2 & CO2 Removal',
            'Pressure Vessel And Tank',
            'Steel Structure Fabrication And Erection',
            'Mechanical Work',
            'Electrical & Instrument Work',
            'Painting, Fireproofing And Insulation Work',
            'HDD & Auger Services',
            'R&D High-End Intelligent Oil and Gas Equipment',
        ];

        $serviceFaqs = [
            [
                'question' => 'What EPC scopes does PT EPIK cover?',
                'answer' => 'PT EPIK provides integrated EPC services for oil & gas infrastructure, including gas compressor packages, pipeline & piping, pressure vessels and tanks, steel structure fabrication and erection, mechanical work, electrical & instrument work, painting, fireproofing and insulation, HDD & auger services, onshore civil & building with piling, telecommunication infrastructure, natural gas/CNG/LNG facilities, SO2 & CO2 removal, and R&D for high-end intelligent oil and gas equipment.',
            ],
            [
                'question' => 'Does EPIK deliver EPCIC, not only construction?',
                'answer' => 'Yes. We support Engineering, Procurement, Construction, Installation, and Commissioning (EPCIC) for large-scale energy projects — from technical planning and material supply through site execution, testing, and handover.',
            ],
            [
                'question' => 'Which industries and clients does EPIK typically serve?',
                'answer' => 'We focus on upstream and downstream oil & gas infrastructure for partners such as PT PGN, Pertamina EP, Pertamina Gas, Perta Arun Gas, and PGN SAKA — covering pipeline networks, metering stations, LNG facilities, and customer attachment works across Indonesia.',
            ],
            [
                'question' => 'How does EPIK manage safety and project quality?',
                'answer' => 'Every project is executed under disciplined HSE and quality procedures aligned with industry standards and our ISO certifications (ISO 9001, ISO 14001, and ISO 45001). We emphasize schedule reliability, constructability review, and clear progress reporting to clients.',
            ],
        ];

        return view('frontend.services.detail', compact(
            'service',
            'testimonials',
            'epcServiceItems',
            'serviceFaqs'
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
