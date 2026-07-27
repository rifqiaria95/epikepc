<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Mono\TagController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Mono\NewsController;
use App\Http\Controllers\Mono\HomeController;
use App\Http\Controllers\Frontend\NewsController as FrontendNewsController;
use App\Http\Controllers\Mono\UserController;
use App\Http\Controllers\Mono\AboutController;
use App\Http\Controllers\Mono\AdminController;
use App\Http\Controllers\Mono\GaleriController;
use App\Http\Controllers\Mono\KategoriController;
use App\Http\Controllers\Mono\DashboardController;
use App\Http\Controllers\Mono\KnowledgeController;
use App\Http\Controllers\Mono\MenuGroupController;
use App\Http\Controllers\Mono\MenuDetailController;
use App\Http\Controllers\Mono\OrganisasiController;
use App\Http\Controllers\Mono\PermissionController;
use App\Http\Controllers\Ext\RegistrationController;
use App\Http\Controllers\Mono\SubMenuDetailController;
use App\Http\Controllers\Mono\KategoriGaleriController;
use App\Http\Controllers\Mono\RolePermissionController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Mono\TestimoniController;
use App\Http\Controllers\Mono\PricingController;
use App\Http\Controllers\Mono\CoverageLocationController;
use App\Http\Controllers\Mono\ConsultationRequestController as MonoConsultationRequestController;
use App\Http\Controllers\Mono\ServiceTypeController;
use App\Http\Controllers\Mono\ServicesController as MonoServicesController;
use App\Http\Controllers\Mono\ProjectController as MonoProjectController;
use App\Http\Controllers\Frontend\ServicesController as FrontendServicesController;
use App\Http\Controllers\Frontend\ProjectController as FrontendProjectController;
use App\Http\Controllers\Frontend\ContactController as FrontendContactController;
use App\Http\Controllers\Frontend\ConsultationRequestController as FrontendConsultationRequestController;
use App\Http\Controllers\Frontend\CoverageController as FrontendCoverageController;
use App\Http\Controllers\Frontend\AboutController as FrontendAboutController;
use App\Http\Controllers\Frontend\TeamController as FrontendTeamController;
use App\Http\Controllers\Frontend\GalleryController as FrontendGalleryController;
use App\Http\Controllers\Frontend\FaqController as FrontendFaqController;

Route::get('/', [HomeController::class, 'index']);

// Frontend Routes
Route::get('/about', [FrontendAboutController::class, 'index'])->name('frontend.about.index');
Route::get('/news', [FrontendNewsController::class, 'index'])->name('frontend.news.index');
Route::get('/news/{slug}', [FrontendNewsController::class, 'show'])->name('news.show');
Route::get('/team', [FrontendTeamController::class, 'index'])->name('frontend.team.index');
Route::get('/gallery', [FrontendGalleryController::class, 'index'])->name('frontend.gallery.index');
Route::get('/faq', [FrontendFaqController::class, 'index'])->name('frontend.faq.index');
Route::get('/services', [FrontendServicesController::class, 'index'])->name('frontend.services.index');
Route::get('/services/{slug}', [FrontendServicesController::class, 'showByServiceType'])
    ->name('frontend.services.show')
    ->where('slug', '^(?!service_type|service_list).*$'); // Exclude internal routes
Route::get('/detail-service/{id}', [FrontendServicesController::class, 'detailService'])
    ->name('frontend.detail-service')
    ->where('id', '[0-9]+');
Route::get('/projects', [FrontendProjectController::class, 'index'])->name('frontend.projects.index');
Route::get('/projects/{slug}', [FrontendProjectController::class, 'show'])
    ->name('frontend.projects.show')
    ->where('slug', '[a-z0-9\-]+');
Route::get('/contact', [FrontendContactController::class, 'index'])->name('frontend.contact.index');
Route::post('/contact', [FrontendContactController::class, 'store'])->name('frontend.contact.store')->middleware('throttle:10,1');
Route::post('/consultation', [FrontendConsultationRequestController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('frontend.consultation.store');
Route::post('/coverage/check', [FrontendCoverageController::class, 'check'])
    ->middleware('throttle:30,1')
    ->name('frontend.coverage.check');
Route::get('/coverage/suggest', [FrontendCoverageController::class, 'suggest'])
    ->middleware('throttle:60,1')
    ->name('frontend.coverage.suggest');
Route::get('/coverage/reverse', [FrontendCoverageController::class, 'reverse'])
    ->middleware('throttle:30,1')
    ->name('frontend.coverage.reverse');

// Route untuk Semua Role
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/ext-dashboard', [DashboardController::class, 'extDashboard'])->name('ext-dashboard');

    // Analytics API routes
    Route::get('/api/analytics', [DashboardController::class, 'getAnalytics'])->name('analytics.get');
    Route::post('/api/analytics/track', [DashboardController::class, 'trackAnalytics'])->name('analytics.track');

    // Route Kategori
    Route::prefix('internal/news/kategori')->name('kategori.')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('index');
        Route::post('/store', [KategoriController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [KategoriController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [KategoriController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [KategoriController::class, 'destroy'])->name('destroy');
    });

    // Route Tag
    Route::prefix('internal/news/tag')->name('tag.')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('index');
        Route::post('/store', [TagController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [TagController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [TagController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [TagController::class, 'destroy'])->name('destroy');
    });

    // Route Service Type
    Route::prefix('internal/services/service_type')->name('service_type.')->group(function () {
        Route::get('/', [ServiceTypeController::class, 'index'])
        ->name('index');
        Route::post('/store', [ServiceTypeController::class, 'store'])
        ->name('store');
        Route::get('/edit/{id}', [ServiceTypeController::class, 'edit'])
        ->name('edit');
        Route::put('/update/{id}', [ServiceTypeController::class, 'update'])
        ->name('update');
        Route::delete('/delete/{id}', [ServiceTypeController::class, 'destroy'])
        ->name('destroy');
    });

    // Route Services
    Route::prefix('internal/services/service_list')->name('service_list.')->group(function () {
        Route::get('/', [MonoServicesController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_services');
        Route::post('/store', [MonoServicesController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_services');
        Route::get('/edit/{id}', [MonoServicesController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_services');
        Route::put('/update/{id}', [MonoServicesController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_services');
        Route::delete('/delete/{id}', [MonoServicesController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_services');
    });

    // Route Pricing
    Route::prefix('internal/pricing')->name('pricing.')->group(function () {
        Route::get('/', [PricingController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_pricing');
        Route::post('/store', [PricingController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_pricing');
        Route::get('/edit/{id}', [PricingController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_pricing');
        Route::put('/update/{id}', [PricingController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_pricing');
        Route::delete('/delete/{id}', [PricingController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_pricing');
    });

    // Route Coverage Area
    Route::prefix('internal/coverage')->name('coverage.')->group(function () {
        Route::get('/', [CoverageLocationController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_coverage');
        Route::get('/options', [CoverageLocationController::class, 'options'])
            ->name('options')
            ->middleware('permission:view_coverage');
        Route::post('/store', [CoverageLocationController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_coverage');
        Route::get('/edit/{id}', [CoverageLocationController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_coverage');
        Route::put('/update/{id}', [CoverageLocationController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_coverage');
        Route::delete('/delete/{id}', [CoverageLocationController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_coverage');
    });

    Route::prefix('internal/consultation')->name('consultation.')->group(function () {
        Route::get('/', [MonoConsultationRequestController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_consultation');
        Route::post('/store', [MonoConsultationRequestController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_consultation');
        Route::get('/edit/{id}', [MonoConsultationRequestController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_consultation');
        Route::put('/update/{id}', [MonoConsultationRequestController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_consultation');
        Route::delete('/delete/{id}', [MonoConsultationRequestController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_consultation');
    });

    // Route News
    Route::prefix('internal/news')->name('news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_news');
        Route::post('/store', [NewsController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_news');
        Route::get('/edit/{id:uuid}', [NewsController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_news');
        Route::put('/update/{id}', [NewsController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_news');
        Route::delete('/delete/{id}', [NewsController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_news');
    });

    // Route Company (Journey + Milestones)
    Route::prefix('internal/profile/about')->name('about.')->group(function () {
        Route::get('/', [AboutController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_profile');
        Route::put('/journey', [AboutController::class, 'updateJourney'])
            ->name('journey.update')
            ->middleware('permission:edit_profile');
        Route::post('/milestones/store', [AboutController::class, 'storeMilestone'])
            ->name('milestones.store')
            ->middleware('permission:create_profile');
        Route::get('/milestones/edit/{id}', [AboutController::class, 'editMilestone'])
            ->name('milestones.edit')
            ->middleware('permission:edit_profile');
        Route::put('/milestones/update/{id}', [AboutController::class, 'updateMilestone'])
            ->name('milestones.update')
            ->middleware('permission:edit_profile');
        Route::delete('/milestones/delete/{id}', [AboutController::class, 'destroyMilestone'])
            ->name('milestones.destroy')
            ->middleware('permission:delete_profile');
    });

    // Route Project
    Route::prefix('internal/project')->name('project.')->group(function () {
        Route::get('/', [MonoProjectController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_project');
        Route::post('/store', [MonoProjectController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_project');
        Route::get('/edit/{id}', [MonoProjectController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_project');
        Route::put('/update/{id}', [MonoProjectController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_project');
        Route::delete('/delete/{id}', [MonoProjectController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_project');
    });

    // Route Galeri
    Route::prefix('internal/galeri/list-galeri')->name('list-galeri.')->group(function () {
        Route::get('/', [GaleriController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_galeri');
        Route::post('/store', [GaleriController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_galeri');
        Route::get('/edit/{id}', [GaleriController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_galeri');
        Route::put('/update/{id}', [GaleriController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_galeri');
        Route::delete('/delete/{id}', [GaleriController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_galeri');
    });

    // Route Kategori Galeri
    Route::prefix('internal/galeri/kategori-galeri')->name('kategori-galeri.')->group(function () {
        Route::get('/', [KategoriGaleriController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_galeri');
        Route::post('/store', [KategoriGaleriController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_galeri');
        Route::get('/edit/{id}', [KategoriGaleriController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:show_galeri');
        Route::put('/update/{id}', [KategoriGaleriController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_galeri');
        Route::delete('/delete/{id}', [KategoriGaleriController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_galeri');
    });

    // Route Organisasi
    Route::prefix('internal/organisasi')->name('organisasi.')->group(function () {
        Route::get('/', [OrganisasiController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_organisasi');
        Route::post('/store', [OrganisasiController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_organisasi');
        Route::get('/edit/{id}', [OrganisasiController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_organisasi');
        Route::put('/update/{id}', [OrganisasiController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_organisasi');
        Route::delete('/delete/{id}', [OrganisasiController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_organisasi');
    });

    // Route Testimoni
    Route::prefix('internal/testimoni')->name('testimoni.')->group(function () {
        Route::get('/', [TestimoniController::class, 'index'])
            ->name('index')
            ->middleware('permission:view_testimoni');
        Route::post('/store', [TestimoniController::class, 'store'])
            ->name('store')
            ->middleware('permission:create_testimoni');
        Route::get('/edit/{id}', [TestimoniController::class, 'edit'])
            ->name('edit')
            ->middleware('permission:edit_testimoni');
        Route::put('/update/{id}', [TestimoniController::class, 'update'])
            ->name('update')
            ->middleware('permission:edit_testimoni');
        Route::delete('/delete/{id}', [TestimoniController::class, 'destroy'])
            ->name('destroy')
            ->middleware('permission:delete_testimoni');
    });

    // Route User
    Route::prefix('/admin/users')->name('user.')->group(function () {
        Route::get('/profile/{id}', [UserController::class, 'profile'])->name('profile');
    });

    // Route Knowledge
    Route::prefix('admin/knowledge')->name('knowledge.')->group(function () {
        Route::get('/', [KnowledgeController::class, 'index'])->name('index');
        Route::post('/store', [KnowledgeController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [KnowledgeController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [KnowledgeController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [KnowledgeController::class, 'destroy'])->name('destroy');
    });
});

// Route untuk Superadmin
Route::middleware(['auth', 'role:superadmin'])->group(function () {

    // Route User
    Route::prefix('admin/users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::get('/getPermission/{id}', [UserController::class, 'getPermission'])->name('getPermission');
        Route::post('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Route Role
    Route::prefix('admin/role')->name('role.')->group(function () {
        Route::get('/', [RolePermissionController::class, 'index'])->name('index');
        Route::post('/store', [RolePermissionController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [RolePermissionController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [RolePermissionController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [RolePermissionController::class, 'destroy'])->name('destroy');
        Route::get('/profile/{id}', [RolePermissionController::class, 'profile'])->name('profile');
        Route::get('/permissions', [RolePermissionController::class, 'getPermissions']);
    });

    // Route Permission
    Route::prefix('admin/permission')->name('permission.')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        Route::post('/store', [PermissionController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [PermissionController::class, 'destroy'])->name('destroy');
        Route::delete('/delete-batch', [PermissionController::class, 'destroyBatch'])->name('destroy.batch');
        Route::get('/profile/{id}', [PermissionController::class, 'profile'])->name('profile');
        Route::get('/permissions', [PermissionController::class, 'getPermissions']);
        Route::get('/get-menu-groups', [PermissionController::class, 'getMenuGroups'])->name('get.menu.groups');
        Route::get('/get-menu-details', [PermissionController::class, 'getMenuDetails'])->name('get.menu.details');
        Route::get('/get-menu-details-by-group', [PermissionController::class, 'getMenuDetailsByGroup'])->name('get.menu.details.by.group');

    });

    // Route Menu Group
    Route::prefix('admin/menu-group')->name('menu-group.')->group(function () {
        Route::get('/', [MenuGroupController::class, 'index'])->name('index');
        Route::post('/store', [MenuGroupController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MenuGroupController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MenuGroupController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MenuGroupController::class, 'destroy'])->name('destroy');
    });

    // Route Menu Details
    Route::prefix('admin/menu-detail')->name('menu-detail.')->group(function () {
        Route::get('/', [MenuDetailController::class, 'index'])->name('index');
        Route::post('/store', [MenuDetailController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MenuDetailController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [MenuDetailController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MenuDetailController::class, 'destroy'])->name('destroy');
        Route::delete('/delete-batch', [MenuDetailController::class, 'destroyBatch'])->name('destroy.batch');
    });

    // Route Sub Menu Details
    Route::prefix('admin/sub-menu-detail')->name('sub-menu-detail.')->group(function () {
        Route::get('/', [SubMenuDetailController::class, 'index'])->name('index');
        Route::post('/store', [SubMenuDetailController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [SubMenuDetailController::class, 'edit'])->name('edit');
        Route::post('/update/{id}', [SubMenuDetailController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [SubMenuDetailController::class, 'destroy'])->name('destroy');
    });

    // Route Trash
    Route::get('/deleted/data', [AdminController::class, 'getDeletedRecords'])->name('deleted.data');
    Route::post('/deleted/restore', [AdminController::class, 'restoreRecord'])->name('deleted.restore');
    Route::post('/deleted/delete', [AdminController::class, 'deleteRecord'])->name('deleted.delete');
});

require __DIR__.'/auth.php';
