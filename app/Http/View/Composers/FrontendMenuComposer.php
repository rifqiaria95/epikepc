<?php

namespace App\Http\View\Composers;

use App\Models\ServiceType;
use App\Models\Service;
use Illuminate\View\View;

class FrontendMenuComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Ambil service types dengan type 'it' beserta services-nya
        $itServiceTypes = ServiceType::where('type', 'it')
            ->with(['services' => function ($query) {
                $query->orderBy('title');
            }])
            ->orderBy('name')
            ->get();

        // Ambil service types dengan type 'design' beserta services-nya
        $designServiceTypes = ServiceType::where('type', 'design')
            ->with(['services' => function ($query) {
                $query->orderBy('title');
            }])
            ->orderBy('name')
            ->get();

        // Ambil semua service types untuk footer (tanpa soft deleted)
        $serviceTypes = ServiceType::withoutTrashed()
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        // Ambil semua services untuk footer dengan relasi serviceType
        $services = Service::withoutTrashed()
            ->with(['serviceType'])
            ->orderBy('created_at', 'desc')
            ->get();

        $view->with('itServiceTypes', $itServiceTypes);
        $view->with('designServiceTypes', $designServiceTypes);
        $view->with('serviceTypes', $serviceTypes);
        $view->with('services', $services);
    }
}

