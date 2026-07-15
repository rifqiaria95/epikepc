<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

class FrontendMenuComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with('frontendServices', config('frontend_services.items', []));
    }
}
