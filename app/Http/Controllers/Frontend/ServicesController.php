<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceType;

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

        $sidebarServices = Service::forSidebar((int) $service->id)->get();

        return view('frontend.services.detail', compact('service', 'sidebarServices'));
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
