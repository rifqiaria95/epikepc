@php
    $groupRoutes = $menuActive->collectGroupRoutes($menuGroup);
    $groupClass = $menuActive->groupClass($groupRoutes);
@endphp

<li class="menu-item {{ $groupClass }}">
    <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ti ti-{{ $menuGroup->icon }}"></i>
        <div data-i18n="{{ $menuGroup->name }}">{{ $menuGroup->name }}</div>
    </a>

    @if ($menuGroup->menuDetails->isNotEmpty())
        <ul class="menu-sub">
            @foreach ($menuGroup->menuDetails as $menuDetail)
                @php
                    $detailRoutes = collect($groupRoutes)
                        ->filter(fn ($route) => filled($route))
                        ->values()
                        ->all();
                    $detailClass = $menuActive->itemClass($menuDetail->route, $detailRoutes);
                    $hasSubMenus = $menuDetail->subMenuDetails->isNotEmpty();
                @endphp
                <li class="menu-item {{ $hasSubMenus ? $menuActive->groupClass($menuDetail->subMenuDetails->pluck('route')) : $detailClass }}">
                    <a href="{{ $menuDetail->route }}"
                        class="menu-link{{ $hasSubMenus ? ' menu-toggle' : '' }}">
                        <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}</div>
                    </a>

                    @if ($hasSubMenus)
                        <ul class="menu-sub">
                            @foreach ($menuDetail->subMenuDetails as $subMenuDetail)
                                <li class="menu-item {{ $menuActive->itemClass($subMenuDetail->route, $groupRoutes) }}">
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
