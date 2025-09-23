<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
                <a href="index.html" class="app-brand-link">
                    <span class="app-brand-logo demo">
                        <img src="{{ url('assets/img/branding/logo.png') }}" alt="Logo" width="40">
                    </span>
                    <span class="app-brand-text demo menu-text fw-bold">Kainnova</span>
                </a>

                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                    <i class="ti menu-toggle-icon d-none d-xl-block align-middle"></i>
                    <i class="ti ti-x d-block d-xl-none ti-md align-middle"></i>
                </a>
            </div>

            <div class="menu-inner-shadow"></div>

            <ul class="menu-inner py-1">
                <!-- Dashboards -->
                <li class="menu-item  {{ Request::is('dashboard') ? 'active' : '' }}">
                    <a href="/dashboard" class="menu-link">
                        <i class="menu-icon tf-icons ti ti-smart-home"></i>
                        <div data-i18n="Dashboard">Dashboard</div>
                    </a>
                </li>
                @if (isset($menuGroups) && $menuGroups->isNotEmpty())
                        <li class="menu-header small">
                            <span class="menu-header-text" data-i18n="Apps & Pages">Apps & Pages</span>
                        </li>
                        @foreach ($menuGroups as $menuGroup)
                            @if ($menuGroup->jenis_menu == 9)
                                <li class="menu-item">
                                    <a href="javascript:void(0);"
                                        class="menu-link {{ $menuGroup->menuDetails->isNotEmpty() ? 'menu-toggle' : '' }}">
                                        <i class="menu-icon tf-icons ti ti-{{ $menuGroup->icon }}"></i>
                                        <div data-i18n="{{ $menuGroup->name }}">{{ $menuGroup->name }}</div>
                                        @if ($menuGroup->menuDetails->isNotEmpty())
                                            <span class="menu-arrow"></span>
                                        @endif
                                    </a>

                                    @if ($menuGroup->menuDetails->isNotEmpty())
                                        <ul class="menu-sub">
                                            @foreach ($menuGroup->menuDetails as $menuDetail)
                                                <li class="menu-item">
                                                    <a href="{{ $menuDetail->route }}" class="menu-link">
                                                        <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="menu-header small">
                            <span class="menu-header-text">No menu available</span>
                        </li>
                    @endif

                    @if (isset($menuGroups) && $menuGroups->isNotEmpty())
                        @foreach ($menuGroups as $menuGroup)
                            @if ($menuGroup->jenis_menu == 1)
                                <li class="menu-item">
                                    <a href="javascript:void(0);"
                                        class="menu-link {{ $menuGroup->menuDetails->isNotEmpty() ? 'menu-toggle' : '' }}">
                                        <i class="menu-icon tf-icons ti ti-{{ $menuGroup->icon }}"></i>
                                        <div data-i18n="{{ $menuGroup->name }}">{{ $menuGroup->name }}</div>
                                        @if ($menuGroup->menuDetails->isNotEmpty())
                                            <span class="menu-arrow"></span>
                                        @endif
                                    </a>

                                    @if ($menuGroup->menuDetails->isNotEmpty())
                                        <ul class="menu-sub">
                                            @foreach ($menuGroup->menuDetails as $menuDetail)
                                                <li class="menu-item">
                                                    <a href="{{ $menuDetail->route }}" class="menu-link">
                                                        <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="menu-header small">
                            <span class="menu-header-text">No menu available</span>
                        </li>
                    @endif

                    @if (isset($menuGroups) && $menuGroups->isNotEmpty())
                        @foreach ($menuGroups as $menuGroup)
                            @if ($menuGroup->jenis_menu == 2)
                                <li class="menu-item">
                                    <a href="javascript:void(0);"
                                        class="menu-link {{ $menuGroup->menuDetails->isNotEmpty() ? 'menu-toggle' : '' }}">
                                        <i class="menu-icon tf-icons ti ti-{{ $menuGroup->icon }}"></i>
                                        <div data-i18n="{{ $menuGroup->name }}">{{ $menuGroup->name }}</div>
                                        @if ($menuGroup->menuDetails->isNotEmpty())
                                            <span class="menu-arrow"></span>
                                        @endif
                                    </a>

                                    @if ($menuGroup->menuDetails->isNotEmpty())
                                        <ul class="menu-sub">
                                            @foreach ($menuGroup->menuDetails as $menuDetail)
                                                <li class="menu-item">
                                                    <a href="{{ $menuDetail->route }}" class="menu-link">
                                                        <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="menu-header small">
                            <span class="menu-header-text">No menu available</span>
                        </li>
                    @endif

                    @if (isset($menuGroups) && $menuGroups->isNotEmpty())
                        @foreach ($menuGroups as $menuGroup)
                            @if ($menuGroup->jenis_menu == 3)
                                <li class="menu-item">
                                    <a href="javascript:void(0);"
                                        class="menu-link {{ $menuGroup->menuDetails->isNotEmpty() ? 'menu-toggle' : '' }}">
                                        <i class="menu-icon tf-icons ti ti-{{ $menuGroup->icon }}"></i>
                                        <div data-i18n="{{ $menuGroup->name }}">{{ $menuGroup->name }}</div>
                                        @if ($menuGroup->menuDetails->isNotEmpty())
                                            <span class="menu-arrow"></span>
                                        @endif
                                    </a>

                                    @if ($menuGroup->menuDetails->isNotEmpty())
                                        <ul class="menu-sub">
                                            @foreach ($menuGroup->menuDetails as $menuDetail)
                                                <li class="menu-item">
                                                    <a href="{{ $menuDetail->route }}" class="menu-link">
                                                        <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="menu-header small">
                            <span class="menu-header-text">No menu available</span>
                        </li>
                    @endif

                    @if (isset($menuGroups) && $menuGroups->isNotEmpty())
                        @foreach ($menuGroups as $menuGroup)
                            @if ($menuGroup->jenis_menu == 4)
                                <li class="menu-item">
                                    <a href="javascript:void(0);"
                                        class="menu-link {{ $menuGroup->menuDetails->isNotEmpty() ? 'menu-toggle' : '' }}">
                                        <i class="menu-icon tf-icons ti ti-{{ $menuGroup->icon }}"></i>
                                        <div data-i18n="{{ $menuGroup->name }}">{{ $menuGroup->name }}</div>
                                        @if ($menuGroup->menuDetails->isNotEmpty())
                                            <span class="menu-arrow"></span>
                                        @endif
                                    </a>

                                    @if ($menuGroup->menuDetails->isNotEmpty())
                                        <ul class="menu-sub">
                                            @foreach ($menuGroup->menuDetails as $menuDetail)
                                                <li class="menu-item">
                                                    <a href="{{ $menuDetail->route }}" class="menu-link">
                                                        <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="menu-header small">
                            <span class="menu-header-text">No menu available</span>
                        </li>
                    @endif

                    @if (isset($menuGroups) && $menuGroups->isNotEmpty())
                        @foreach ($menuGroups as $menuGroup)
                            @if ($menuGroup->jenis_menu == 5)
                                <li class="menu-item">
                                    <a href="javascript:void(0);"
                                        class="menu-link {{ $menuGroup->menuDetails->isNotEmpty() ? 'menu-toggle' : '' }}">
                                        <i class="menu-icon tf-icons ti ti-{{ $menuGroup->icon }}"></i>
                                        <div data-i18n="{{ $menuGroup->name }}">{{ $menuGroup->name }}</div>
                                        @if ($menuGroup->menuDetails->isNotEmpty())
                                            <span class="menu-arrow"></span>
                                        @endif
                                    </a>

                                    @if ($menuGroup->menuDetails->isNotEmpty())
                                        <ul class="menu-sub">
                                            @foreach ($menuGroup->menuDetails as $menuDetail)
                                                <li class="menu-item">
                                                    <a href="{{ $menuDetail->route }}" class="menu-link">
                                                        <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="menu-header small">
                            <span class="menu-header-text">No menu available</span>
                        </li>
                    @endif

                    @if (isset($menuGroups) && $menuGroups->isNotEmpty())
                        @foreach ($menuGroups as $menuGroup)
                            @if ($menuGroup->jenis_menu == 6)
                                <li class="menu-item">
                                    <a href="javascript:void(0);"
                                        class="menu-link {{ $menuGroup->menuDetails->isNotEmpty() ? 'menu-toggle' : '' }}">
                                        <i class="menu-icon tf-icons ti ti-{{ $menuGroup->icon }}"></i>
                                        <div data-i18n="{{ $menuGroup->name }}">{{ $menuGroup->name }}</div>
                                        @if ($menuGroup->menuDetails->isNotEmpty())
                                            <span class="menu-arrow"></span>
                                        @endif
                                    </a>

                                    @if ($menuGroup->menuDetails->isNotEmpty())
                                        <ul class="menu-sub">
                                            @foreach ($menuGroup->menuDetails as $menuDetail)
                                                <li class="menu-item">
                                                    <a href="{{ $menuDetail->route }}" class="menu-link">
                                                        <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}
                                                        </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="menu-header small">
                            <span class="menu-header-text">No menu available</span>
                        </li>
                    @endif

                    @if (isset($menuGroups) && $menuGroups->isNotEmpty())
                        @foreach ($menuGroups as $menuGroup)
                            @if ($menuGroup->jenis_menu == 8)
                                <li class="menu-item">
                                    <a href="javascript:void(0);" class="menu-link menu-toggle">
                                        <i class="menu-icon tf-icons ti ti-{{ $menuGroup->icon }}"></i>
                                        <div data-i18n="{{ $menuGroup->name }}">{{ $menuGroup->name }}</div>
                                    </a>
                                    @if ($menuGroup->menuDetails->isNotEmpty())
                                        <ul class="menu-sub">
                                            @foreach ($menuGroup->menuDetails as $menuDetail)
                                                <li class="menu-item">
                                                    <a href="{{ $menuDetail->route }}" class="menu-link{{ $menuDetail->subMenuDetails->isNotEmpty() ? ' menu-toggle' : '' }}">
                                                        <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}</div>
                                                    </a>
                                                    @if ($menuDetail->subMenuDetails->isNotEmpty())
                                                        <ul class="menu-sub">
                                                            @foreach ($menuDetail->subMenuDetails as $subMenuDetail)
                                                                <li class="menu-item">
                                                                    <a href="{{ $subMenuDetail->route }}" class="menu-link">
                                                                        <div data-i18n="{{ $subMenuDetail->name }}">{{ $subMenuDetail->name }}</div>
                                                                    </a>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="menu-header small">
                            <span class="menu-header-text">Tidak ada menu tersedia</span>
                        </li>
                    @endif

                    @if (isset($menuGroups) && $menuGroups->isNotEmpty())
                        @foreach ($menuGroups as $menuGroup)
                            @if ($menuGroup->jenis_menu == 7)
                                    <li class="menu-item">
                                        <a href="javascript:void(0);"
                                            class="menu-link {{ $menuGroup->menuDetails->isNotEmpty() ? 'menu-toggle' : '' }}">
                                            <i class="menu-icon tf-icons ti ti-{{ $menuGroup->icon }}"></i>
                                            <div data-i18n="{{ $menuGroup->name }}">{{ $menuGroup->name }}</div>
                                            @if ($menuGroup->menuDetails->isNotEmpty())
                                                <span class="menu-arrow"></span>
                                            @endif
                                        </a>

                                        @if ($menuGroup->menuDetails->isNotEmpty())
                                            <ul class="menu-sub">
                                                @foreach ($menuGroup->menuDetails as $menuDetail)
                                                    <li class="menu-item">
                                                        <a href="{{ $menuDetail->route }}" class="menu-link">
                                                            <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}
                                                            </div>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </li>
                            @endif
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
