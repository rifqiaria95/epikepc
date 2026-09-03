<div class="layout-wrapper layout-content-navbar">
    @php($menuActive = $menuActive ?? app(\App\Support\Menu\MenuActiveMatcher::class))
    <div class="layout-container">
        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
                <a href="index.html" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        <img src="{{ url('assets/img/branding/icon-5.png') }}" alt="Logo" width="30">
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold">EPIKEPC</span>
                </a>

                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                    <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
                    <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
                </a>
            </div>

            <div class="menu-inner-shadow"></div>

            <ul class="menu-inner py-1">
                <li class="menu-item {{ $menuActive->itemClass('/dashboard') }}">
                    <a href="/dashboard" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-smart-home"></i>
                        <div data-i18n="Dashboard">Dashboard</div>
                    </a>
                </li>

                @if (isset($menuGroups) && $menuGroups->isNotEmpty())
                    @if ($menuGroups->where('jenis_menu', 9)->isNotEmpty())
                        <li class="menu-header small">
                            <span class="menu-header-text" data-i18n="Apps & Pages">Apps & Pages</span>
                        </li>
                        @foreach ($menuGroups->where('jenis_menu', 9) as $menuGroup)
                            @include('layouts.partials.sidebar-menu-group', ['menuGroup' => $menuGroup])
                        @endforeach
                    @endif

                    @foreach ([1, 2, 3, 4, 5, 6, 8, 7] as $jenisMenu)
                        @foreach ($menuGroups->where('jenis_menu', $jenisMenu) as $menuGroup)
                            @if ($jenisMenu === 8)
                                @include('layouts.partials.sidebar-menu-group-nested', ['menuGroup' => $menuGroup])
                            @else
                                @include('layouts.partials.sidebar-menu-group', ['menuGroup' => $menuGroup])
                            @endif
                        @endforeach
                    @endforeach
                @else
                    <li class="menu-header small">
                        <span class="menu-header-text">No menu available</span>
                    </li>
                @endif
            </ul>
        </aside>
        <!-- / Menu -->

        <div class="layout-page">
