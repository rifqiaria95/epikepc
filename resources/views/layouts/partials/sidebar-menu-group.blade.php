@php
    $groupRoutes = $menuActive->collectGroupRoutes($menuGroup);
    $groupClass = $menuActive->groupClass($groupRoutes);
@endphp

<li class="menu-item {{ $groupClass }}">
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
                <li class="menu-item {{ $menuActive->itemClass($menuDetail->route, $groupRoutes) }}">
                    <a href="{{ $menuDetail->route }}" class="menu-link">
                        <div data-i18n="{{ $menuDetail->name }}">{{ $menuDetail->name }}</div>
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</li>
