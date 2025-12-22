<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use App\Models\MenuGroup;
use App\Models\MenuDetail;
use Illuminate\Support\Facades\URL;
use App\Http\View\Composers\MenuComposer;
use App\Http\View\Composers\FrontendMenuComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Daftarkan view composer untuk menu internal
        if (Schema::hasTable('menu_groups')) {
            View::composer(['layouts.side-menu', 'internal.permission.index'], MenuComposer::class);
        }

        // Daftarkan view composer untuk menu frontend
        if (Schema::hasTable('service_type')) {
            View::composer(['layouts.frontend.header', 'layouts.frontend.footer'], FrontendMenuComposer::class);
        }

        // Custom pesan validasi
        Validator::replacer('required', function ($message, $attribute, $rule, $parameters) {
            return "Kolom " . str_replace('_', ' ', $attribute) . " harus diisi!";
        });

        \Illuminate\Support\Facades\Validator::extendImplicit('custom', function () {
            return false;
        });

        \Illuminate\Support\Facades\Validator::replacer('custom', function ($message, $attribute) {
            return "Kolom " . ucfirst(str_replace('_', ' ', $attribute)) . " wajib diisi!";
        });
    }
}
