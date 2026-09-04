<?php

namespace App\Providers;

use App\Contracts\MalwareScannerInterface;
use App\Http\View\Composers\FrontendMenuComposer;
use App\Http\View\Composers\MenuComposer;
use App\Models\Certificate;
use App\Models\Career\Candidate;
use App\Models\Career\JobApplication;
use App\Models\Career\JobApplicationDocument;
use App\Models\Career\JobVacancy;
use App\Policies\CertificatePolicy;
use App\Policies\CandidatePolicy;
use App\Policies\JobApplicationDocumentPolicy;
use App\Policies\JobApplicationPolicy;
use App\Policies\JobVacancyPolicy;
use App\Services\Career\NullMalwareScanner;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MalwareScannerInterface::class, NullMalwareScanner::class);
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
        View::composer(
            ['layouts.frontend.header', 'layouts.frontend.footer', 'index'],
            FrontendMenuComposer::class
        );

        // Custom pesan validasi
        Validator::replacer('required', function ($message, $attribute, $rule, $parameters) {
            return 'Kolom '.str_replace('_', ' ', $attribute).' harus diisi!';
        });

        Validator::extendImplicit('custom', function () {
            return false;
        });

        Validator::replacer('custom', function ($message, $attribute) {
            return 'Kolom '.ucfirst(str_replace('_', ' ', $attribute)).' wajib diisi!';
        });

        Gate::policy(Certificate::class, CertificatePolicy::class);
        Gate::policy(JobVacancy::class, JobVacancyPolicy::class);
        Gate::policy(JobApplication::class, JobApplicationPolicy::class);
        Gate::policy(Candidate::class, CandidatePolicy::class);
        Gate::policy(JobApplicationDocument::class, JobApplicationDocumentPolicy::class);

        RateLimiter::for('career-apply', function (Request $request) {
            $email = mb_strtolower(trim((string) $request->input('email')));

            return [
                Limit::perMinutes(60, 10)->by('career-ip:'.$request->ip()),
                Limit::perMinutes(60, 5)->by('career-email:'.$email.'|'.$request->ip()),
            ];
        });

        RateLimiter::for('career-verify', function (Request $request) {
            return Limit::perMinutes(60, 30)->by('career-verify:'.$request->ip());
        });

        RateLimiter::for('career-resend', function (Request $request) {
            return Limit::perMinutes(60, 5)->by('career-resend:'.$request->ip());
        });
    }
}
