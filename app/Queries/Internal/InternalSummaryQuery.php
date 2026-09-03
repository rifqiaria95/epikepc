<?php

namespace App\Queries\Internal;

use App\Enums\ProjectStatus;
use App\Models\ConsultationRequest;
use App\Support\Cms\Concerns\BuildsAggregateMetrics;
use App\Support\Cms\StatCardPresenter;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InternalSummaryQuery
{
    use BuildsAggregateMetrics;

    public function __construct(
        protected StatCardPresenter $presenter,
    ) {}

    /**
     * @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}>
     */
    public function cards(string $page): array
    {
        return match ($page) {
            'projects' => $this->projectCards(),
            'services' => $this->serviceCards(),
            'about' => $this->aboutCards(),
            'galeri' => $this->galeriCards(),
            'service_types' => $this->serviceTypeCards(),
            'kategori' => $this->kategoriCards(),
            'tags' => $this->tagCards(),
            'news' => $this->newsCards(),
            'pricing' => $this->pricingCards(),
            'coverage' => $this->coverageCards(),
            'testimoni' => $this->testimoniCards(),
            'organisasi' => $this->organisasiCards(),
            'consultation' => $this->consultationCards(),
            'users' => $this->userCards(),
            'roles' => $this->roleCards(),
            'permissions' => $this->permissionCards(),
            'menu_groups' => $this->menuGroupCards(),
            'menu_details' => $this->menuDetailCards(),
            'sub_menu_details' => $this->subMenuDetailCards(),
            'knowledge' => $this->knowledgeCards(),
            'kategori_galeri' => $this->kategoriGaleriCards(),
            'trash' => $this->trashCards(),
            default => throw new InvalidArgumentException("Unknown internal summary page [{$page}]."),
        };
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function projectCards(): array
    {
        $recent = $this->recentSince();
        $row = DB::table('projects')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_published = true THEN 1 ELSE 0 END) as published')
            ->selectRaw('SUM(CASE WHEN is_published = false THEN 1 ELSE 0 END) as draft')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as ongoing', [ProjectStatus::Ongoing->value])
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Project', 'hint' => 'Semua project', 'icon' => 'ti-briefcase', 'color' => 'primary'],
            ['key' => 'published', 'label' => 'Published', 'hint' => 'Tampil di website', 'icon' => 'ti-eye', 'color' => 'success'],
            ['key' => 'ongoing', 'label' => 'On Going', 'hint' => 'Status berjalan', 'icon' => 'ti-progress', 'color' => 'warning'],
            ['key' => 'recent', 'label' => 'Baru (30 Hari)', 'hint' => 'Project ditambahkan bulan ini', 'icon' => 'ti-clock', 'color' => 'info'],
        ], $this->castCounts($row, ['total', 'published', 'ongoing', 'recent']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function serviceCards(): array
    {
        $recent = $this->recentSince();
        $row = DB::table('service')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN image IS NOT NULL AND image != '' THEN 1 ELSE 0 END) as with_image")
            ->selectRaw('SUM(CASE WHEN service_type_id IS NOT NULL THEN 1 ELSE 0 END) as with_type')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Services', 'hint' => 'Semua layanan', 'icon' => 'ti-briefcase', 'color' => 'primary'],
            ['key' => 'with_image', 'label' => 'With Image', 'hint' => 'Memiliki foto', 'icon' => 'ti-photo', 'color' => 'success'],
            ['key' => 'with_type', 'label' => 'With Type', 'hint' => 'Sudah dikategorikan', 'icon' => 'ti-category', 'color' => 'info'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $this->castCounts($row, ['total', 'with_image', 'with_type', 'recent']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function aboutCards(): array
    {
        $recent = $this->recentSince();
        $row = DB::table('company_milestones')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_active = true THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN is_active = false THEN 1 ELSE 0 END) as inactive')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Milestone', 'hint' => 'Semua milestone', 'icon' => 'ti-timeline', 'color' => 'primary'],
            ['key' => 'active', 'label' => 'Active', 'hint' => 'Tampil di homepage', 'icon' => 'ti-eye', 'color' => 'success'],
            ['key' => 'inactive', 'label' => 'Inactive', 'hint' => 'Disembunyikan', 'icon' => 'ti-eye-off', 'color' => 'secondary'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $this->castCounts($row, ['total', 'active', 'inactive', 'recent']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function galeriCards(): array
    {
        $recent = $this->recentSince();
        $row = DB::table('galeri')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN kategori_galeri_id IS NOT NULL THEN 1 ELSE 0 END) as with_category')
            ->selectRaw("SUM(CASE WHEN image IS NOT NULL AND image != '' THEN 1 ELSE 0 END) as with_image")
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Gallery', 'hint' => 'Semua item galeri', 'icon' => 'ti-photo', 'color' => 'primary'],
            ['key' => 'with_category', 'label' => 'With Category', 'hint' => 'Sudah berkategori', 'icon' => 'ti-category', 'color' => 'success'],
            ['key' => 'with_image', 'label' => 'With Image', 'hint' => 'Memiliki foto', 'icon' => 'ti-camera', 'color' => 'info'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $this->castCounts($row, ['total', 'with_category', 'with_image', 'recent']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function serviceTypeCards(): array
    {
        $recent = $this->recentSince();
        $linkedTypeIds = DB::table('service')
            ->whereNull('deleted_at')
            ->whereNotNull('service_type_id')
            ->distinct()
            ->pluck('service_type_id');

        $row = DB::table('service_type')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN type = 'it' THEN 1 ELSE 0 END) as it_types")
            ->selectRaw("SUM(CASE WHEN type = 'design' THEN 1 ELSE 0 END) as design_types")
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        $metrics = $this->castCounts($row, ['total', 'it_types', 'design_types', 'recent']);
        $metrics['linked'] = $linkedTypeIds->isEmpty()
            ? 0
            : (int) DB::table('service_type')->whereNull('deleted_at')->whereIn('id', $linkedTypeIds)->count();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Types', 'hint' => 'Semua tipe layanan', 'icon' => 'ti-category', 'color' => 'primary'],
            ['key' => 'linked', 'label' => 'Digunakan', 'hint' => 'Memiliki layanan', 'icon' => 'ti-link', 'color' => 'success'],
            ['key' => 'it_types', 'label' => 'IT Types', 'hint' => 'Kategori IT', 'icon' => 'ti-code', 'color' => 'info'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $metrics);
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function kategoriCards(): array
    {
        $recent = $this->recentSince();
        $usedIds = DB::table('category_news')->distinct()->pluck('category_id');

        $row = DB::table('kategori')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        $metrics = $this->castCounts($row, ['total', 'recent']);
        $metrics['used'] = $usedIds->isEmpty()
            ? 0
            : (int) DB::table('kategori')->whereNull('deleted_at')->whereIn('id', $usedIds)->count();
        $metrics['unused'] = max(0, $metrics['total'] - $metrics['used']);

        return $this->present([
            ['key' => 'total', 'label' => 'Total Kategori', 'hint' => 'Semua kategori berita', 'icon' => 'ti-category', 'color' => 'primary'],
            ['key' => 'used', 'label' => 'Digunakan', 'hint' => 'Terhubung ke berita', 'icon' => 'ti-link', 'color' => 'success'],
            ['key' => 'unused', 'label' => 'Belum Digunakan', 'hint' => 'Tanpa berita', 'icon' => 'ti-unlink', 'color' => 'secondary'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $metrics);
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function tagCards(): array
    {
        $recent = $this->recentSince();
        $usedIds = DB::table('news_tag')->distinct()->pluck('tag_id');

        $row = DB::table('tags')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        $metrics = $this->castCounts($row, ['total', 'recent']);
        $metrics['used'] = $usedIds->isEmpty()
            ? 0
            : (int) DB::table('tags')->whereNull('deleted_at')->whereIn('id', $usedIds)->count();
        $metrics['unused'] = max(0, $metrics['total'] - $metrics['used']);

        return $this->present([
            ['key' => 'total', 'label' => 'Total Tags', 'hint' => 'Semua tag berita', 'icon' => 'ti-tags', 'color' => 'primary'],
            ['key' => 'used', 'label' => 'Digunakan', 'hint' => 'Terhubung ke berita', 'icon' => 'ti-link', 'color' => 'success'],
            ['key' => 'unused', 'label' => 'Belum Digunakan', 'hint' => 'Tanpa berita', 'icon' => 'ti-unlink', 'color' => 'secondary'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $metrics);
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function newsCards(): array
    {
        $recent = $this->recentSince();
        $row = DB::table('news')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published")
            ->selectRaw("SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft")
            ->selectRaw("SUM(CASE WHEN status = 'archived' THEN 1 ELSE 0 END) as archived")
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total News', 'hint' => 'Semua berita', 'icon' => 'ti-news', 'color' => 'primary'],
            ['key' => 'published', 'label' => 'Published', 'hint' => 'Sudah dipublikasikan', 'icon' => 'ti-world', 'color' => 'success'],
            ['key' => 'draft', 'label' => 'Draft', 'hint' => 'Belum dipublikasikan', 'icon' => 'ti-file-pencil', 'color' => 'warning'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'info'],
        ], $this->castCounts($row, ['total', 'published', 'draft', 'recent']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function pricingCards(): array
    {
        $row = DB::table('pricing')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_active = true THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN is_active = false THEN 1 ELSE 0 END) as inactive')
            ->selectRaw('SUM(CASE WHEN is_popular = true THEN 1 ELSE 0 END) as popular')
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Paket', 'hint' => 'Semua pricing plan', 'icon' => 'ti-currency-dollar', 'color' => 'primary'],
            ['key' => 'active', 'label' => 'Active', 'hint' => 'Ditampilkan', 'icon' => 'ti-circle-check', 'color' => 'success'],
            ['key' => 'popular', 'label' => 'Popular', 'hint' => 'Ditandai populer', 'icon' => 'ti-star', 'color' => 'warning'],
            ['key' => 'inactive', 'label' => 'Inactive', 'hint' => 'Nonaktif', 'icon' => 'ti-circle-x', 'color' => 'secondary'],
        ], $this->castCounts($row, ['total', 'active', 'popular', 'inactive']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function coverageCards(): array
    {
        $recent = $this->recentSince();
        $row = DB::table('coverage_locations')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN is_active = true THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN is_active = false THEN 1 ELSE 0 END) as inactive')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Lokasi', 'hint' => 'Semua coverage area', 'icon' => 'ti-map-pin', 'color' => 'primary'],
            ['key' => 'active', 'label' => 'Active', 'hint' => 'Ditampilkan', 'icon' => 'ti-eye', 'color' => 'success'],
            ['key' => 'inactive', 'label' => 'Inactive', 'hint' => 'Disembunyikan', 'icon' => 'ti-eye-off', 'color' => 'secondary'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $this->castCounts($row, ['total', 'active', 'inactive', 'recent']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function testimoniCards(): array
    {
        $recent = $this->recentSince();
        $row = DB::table('testimoni')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN gambar IS NOT NULL AND gambar != '' THEN 1 ELSE 0 END) as with_image")
            ->selectRaw("SUM(CASE WHEN gambar IS NULL OR gambar = '' THEN 1 ELSE 0 END) as without_image")
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Testimoni', 'hint' => 'Semua testimoni', 'icon' => 'ti-message-star', 'color' => 'primary'],
            ['key' => 'with_image', 'label' => 'With Photo', 'hint' => 'Memiliki foto', 'icon' => 'ti-photo', 'color' => 'success'],
            ['key' => 'without_image', 'label' => 'No Photo', 'hint' => 'Tanpa foto', 'icon' => 'ti-user', 'color' => 'secondary'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $this->castCounts($row, ['total', 'with_image', 'without_image', 'recent']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function organisasiCards(): array
    {
        $recent = $this->recentSince();
        $row = DB::table('organisasi')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN image IS NOT NULL AND image != '' THEN 1 ELSE 0 END) as with_image")
            ->selectRaw('COUNT(DISTINCT lokasi) as locations')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Anggota', 'hint' => 'Semua profil tim', 'icon' => 'ti-users', 'color' => 'primary'],
            ['key' => 'with_image', 'label' => 'With Photo', 'hint' => 'Memiliki foto', 'icon' => 'ti-photo', 'color' => 'success'],
            ['key' => 'locations', 'label' => 'Lokasi', 'hint' => 'Lokasi unik', 'icon' => 'ti-map-pin', 'color' => 'info'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $this->castCounts($row, ['total', 'with_image', 'locations', 'recent']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function consultationCards(): array
    {
        $week = $this->weekSince();
        $row = DB::table('consultation_requests')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as new_requests', [ConsultationRequest::STATUS_NEW])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as contacted', [ConsultationRequest::STATUS_CONTACTED])
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as closed', [ConsultationRequest::STATUS_CLOSED])
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week', [$week])
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Permintaan', 'hint' => 'Semua konsultasi', 'icon' => 'ti-messages', 'color' => 'primary'],
            ['key' => 'new_requests', 'label' => 'Baru', 'hint' => 'Belum dihubungi', 'icon' => 'ti-mail', 'color' => 'warning'],
            ['key' => 'contacted', 'label' => 'Dihubungi', 'hint' => 'Sedang ditindaklanjuti', 'icon' => 'ti-phone', 'color' => 'info'],
            ['key' => 'this_week', 'label' => 'Minggu Ini', 'hint' => '7 hari terakhir', 'icon' => 'ti-clock', 'color' => 'success'],
        ], $this->castCounts($row, ['total', 'new_requests', 'contacted', 'this_week']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function userCards(): array
    {
        $recent = $this->recentSince();
        $withRoles = (int) DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->distinct('model_id')
            ->count('model_id');

        $row = DB::table('users')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN active = true THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN active = false THEN 1 ELSE 0 END) as inactive')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        $metrics = $this->castCounts($row, ['total', 'active', 'inactive', 'recent']);
        $metrics['with_roles'] = $withRoles;

        return $this->present([
            ['key' => 'total', 'label' => 'Total Users', 'hint' => 'Semua pengguna CMS', 'icon' => 'ti-users', 'color' => 'primary'],
            ['key' => 'active', 'label' => 'Active', 'hint' => 'Akun aktif', 'icon' => 'ti-user-check', 'color' => 'success'],
            ['key' => 'with_roles', 'label' => 'With Role', 'hint' => 'Sudah punya role', 'icon' => 'ti-shield', 'color' => 'info'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $metrics);
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function roleCards(): array
    {
        $recent = $this->recentSince();
        $withPermissions = (int) DB::table('roles')
            ->whereIn('id', DB::table('role_has_permissions')->select('role_id'))
            ->count();

        $row = DB::table('roles')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        $metrics = $this->castCounts($row, ['total', 'recent']);
        $metrics['with_permissions'] = $withPermissions;
        $metrics['without_permissions'] = max(0, $metrics['total'] - $withPermissions);

        return $this->present([
            ['key' => 'total', 'label' => 'Total Roles', 'hint' => 'Semua role', 'icon' => 'ti-shield', 'color' => 'primary'],
            ['key' => 'with_permissions', 'label' => 'With Permissions', 'hint' => 'Sudah diassign', 'icon' => 'ti-lock', 'color' => 'success'],
            ['key' => 'without_permissions', 'label' => 'Empty Roles', 'hint' => 'Belum ada permission', 'icon' => 'ti-lock-off', 'color' => 'secondary'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $metrics);
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function permissionCards(): array
    {
        $assignedIds = DB::table('role_has_permissions')->distinct()->pluck('permission_id');

        $row = DB::table('permissions')->selectRaw('COUNT(*) as total')->first();

        $metrics = $this->castCounts($row, ['total']);
        $metrics['assigned'] = $assignedIds->isEmpty()
            ? 0
            : (int) DB::table('permissions')->whereIn('id', $assignedIds)->count();
        $metrics['unassigned'] = max(0, $metrics['total'] - $metrics['assigned']);

        $menuLinked = (int) DB::table('menu_detail_permission')->distinct('permission_id')->count('permission_id');
        $metrics['menu_linked'] = $menuLinked;

        return $this->present([
            ['key' => 'total', 'label' => 'Total Permissions', 'hint' => 'Semua permission', 'icon' => 'ti-key', 'color' => 'primary'],
            ['key' => 'assigned', 'label' => 'Assigned', 'hint' => 'Terhubung ke role', 'icon' => 'ti-link', 'color' => 'success'],
            ['key' => 'menu_linked', 'label' => 'Menu Linked', 'hint' => 'Terhubung ke menu', 'icon' => 'ti-menu-2', 'color' => 'info'],
            ['key' => 'unassigned', 'label' => 'Unassigned', 'hint' => 'Belum dipakai role', 'icon' => 'ti-unlink', 'color' => 'secondary'],
        ], $metrics);
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function menuGroupCards(): array
    {
        $recent = $this->recentSince();
        $withDetails = (int) DB::table('menu_groups')
            ->whereNull('deleted_at')
            ->whereIn('id', DB::table('menu_details')->whereNull('deleted_at')->select('menu_group_id'))
            ->count();

        $row = DB::table('menu_groups')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        $metrics = $this->castCounts($row, ['total', 'recent']);
        $metrics['with_details'] = $withDetails;
        $metrics['empty'] = max(0, $metrics['total'] - $withDetails);

        return $this->present([
            ['key' => 'total', 'label' => 'Total Menu Groups', 'hint' => 'Semua grup menu', 'icon' => 'ti-layout-grid', 'color' => 'primary'],
            ['key' => 'with_details', 'label' => 'With Items', 'hint' => 'Memiliki menu detail', 'icon' => 'ti-list', 'color' => 'success'],
            ['key' => 'empty', 'label' => 'Empty Groups', 'hint' => 'Belum ada item', 'icon' => 'ti-box', 'color' => 'secondary'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $metrics);
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function menuDetailCards(): array
    {
        $recent = $this->recentSince();
        $withSubMenus = (int) DB::table('menu_details')
            ->whereNull('deleted_at')
            ->whereIn('id', DB::table('sub_menu_details')->whereNull('deleted_at')->select('menu_detail_id'))
            ->count();

        $row = DB::table('menu_details')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN status != 1 THEN 1 ELSE 0 END) as inactive')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        $metrics = $this->castCounts($row, ['total', 'active', 'inactive', 'recent']);
        $metrics['with_submenus'] = $withSubMenus;

        return $this->present([
            ['key' => 'total', 'label' => 'Total Menu Items', 'hint' => 'Semua menu detail', 'icon' => 'ti-menu-2', 'color' => 'primary'],
            ['key' => 'active', 'label' => 'Active', 'hint' => 'Status aktif', 'icon' => 'ti-circle-check', 'color' => 'success'],
            ['key' => 'with_submenus', 'label' => 'With Submenu', 'hint' => 'Memiliki submenu', 'icon' => 'ti-subtask', 'color' => 'info'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $metrics);
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function subMenuDetailCards(): array
    {
        $recent = $this->recentSince();
        $row = DB::table('sub_menu_details')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active')
            ->selectRaw('SUM(CASE WHEN status != 1 THEN 1 ELSE 0 END) as inactive')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total Submenu', 'hint' => 'Semua submenu', 'icon' => 'ti-subtask', 'color' => 'primary'],
            ['key' => 'active', 'label' => 'Active', 'hint' => 'Status aktif', 'icon' => 'ti-circle-check', 'color' => 'success'],
            ['key' => 'inactive', 'label' => 'Inactive', 'hint' => 'Nonaktif', 'icon' => 'ti-circle-x', 'color' => 'secondary'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $this->castCounts($row, ['total', 'active', 'inactive', 'recent']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function knowledgeCards(): array
    {
        $recent = $this->recentSince();
        $week = $this->weekSince();
        $row = DB::table('knowledge')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_week', [$week])
            ->selectRaw("SUM(CASE WHEN answer IS NOT NULL AND answer != '' THEN 1 ELSE 0 END) as answered")
            ->first();

        return $this->present([
            ['key' => 'total', 'label' => 'Total FAQ', 'hint' => 'Semua knowledge base', 'icon' => 'ti-help', 'color' => 'primary'],
            ['key' => 'answered', 'label' => 'Answered', 'hint' => 'Memiliki jawaban', 'icon' => 'ti-message-check', 'color' => 'success'],
            ['key' => 'this_week', 'label' => 'Minggu Ini', 'hint' => '7 hari terakhir', 'icon' => 'ti-calendar', 'color' => 'info'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $this->castCounts($row, ['total', 'answered', 'this_week', 'recent']));
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function kategoriGaleriCards(): array
    {
        $recent = $this->recentSince();
        $usedIds = DB::table('galeri')
            ->whereNull('deleted_at')
            ->whereNotNull('kategori_galeri_id')
            ->distinct()
            ->pluck('kategori_galeri_id');

        $row = DB::table('kategori_galeri')
            ->whereNull('deleted_at')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as recent', [$recent])
            ->first();

        $metrics = $this->castCounts($row, ['total', 'recent']);
        $metrics['with_items'] = $usedIds->isEmpty()
            ? 0
            : (int) DB::table('kategori_galeri')->whereNull('deleted_at')->whereIn('id', $usedIds)->count();
        $metrics['empty'] = max(0, $metrics['total'] - $metrics['with_items']);

        return $this->present([
            ['key' => 'total', 'label' => 'Total Kategori', 'hint' => 'Semua kategori galeri', 'icon' => 'ti-category', 'color' => 'primary'],
            ['key' => 'with_items', 'label' => 'Digunakan', 'hint' => 'Memiliki item galeri', 'icon' => 'ti-link', 'color' => 'success'],
            ['key' => 'empty', 'label' => 'Kosong', 'hint' => 'Belum ada item', 'icon' => 'ti-box', 'color' => 'secondary'],
            ['key' => 'recent', 'label' => 'Recent', 'hint' => '30 hari terakhir', 'icon' => 'ti-clock', 'color' => 'warning'],
        ], $metrics);
    }

    /** @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}> */
    public function trashCards(): array
    {
        $week = $this->weekSince();
        $counts = [
            'pegawai' => $this->trashedCount('pegawai'),
            'menu' => $this->trashedCount('menu_details'),
            'users' => $this->trashedCount('users'),
            'gudang' => $this->trashedCount('gudang'),
            'items' => $this->trashedCount('item'),
        ];

        $metrics = [
            'total' => array_sum($counts),
            'users' => $counts['users'],
            'menu' => $counts['menu'],
            'others' => $counts['pegawai'] + $counts['gudang'] + $counts['items'],
        ];

        return $this->present([
            ['key' => 'total', 'label' => 'Total Deleted', 'hint' => 'Semua data terhapus', 'icon' => 'ti-trash', 'color' => 'primary'],
            ['key' => 'users', 'label' => 'Users', 'hint' => 'User di trash', 'icon' => 'ti-users', 'color' => 'warning'],
            ['key' => 'menu', 'label' => 'Menu', 'hint' => 'Menu detail di trash', 'icon' => 'ti-menu-2', 'color' => 'info'],
            ['key' => 'others', 'label' => 'Others', 'hint' => 'Pegawai, gudang, item', 'icon' => 'ti-archive', 'color' => 'secondary'],
        ], $metrics);
    }

    protected function trashedCount(string $table): int
    {
        if (! DB::getSchemaBuilder()->hasTable($table)) {
            return 0;
        }

        if (! DB::getSchemaBuilder()->hasColumn($table, 'deleted_at')) {
            return 0;
        }

        return (int) DB::table($table)->whereNotNull('deleted_at')->count();
    }

    /**
     * @param  array<int, array{key: string, label: string, hint?: string, icon?: string, color?: string}>  $definitions
     * @param  array<string, int|float|string|null>  $metrics
     * @return array<int, array{label: string, value: string, hint?: string, icon: string, color: string}>
     */
    protected function present(array $definitions, array $metrics): array
    {
        return $this->presenter->present($definitions, $metrics);
    }
}
