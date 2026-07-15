@props([
    'services' => collect(),
    'activeId' => null,
])

<div class="tv-widget widget mb-40 wow-itfadeUp" data-wow-duratoin=".9s" data-wow-delay=".3s">
    <ul>
        @forelse ($services as $sidebarService)
            <li class="cat-item {{ (int) $activeId === (int) $sidebarService->id ? 'active' : '' }}">
                <a href="{{ route('frontend.detail-service', $sidebarService->id) }}">{{ $sidebarService->title }}</a>
                <span><i class="fa-regular fa-angle-right"></i></span>
            </li>
        @empty
            @foreach (config('frontend_services.items', []) as $serviceName)
                <li class="cat-item">
                    <a href="{{ route(config('frontend_services.route', 'frontend.services.index')) }}">{{ $serviceName }}</a>
                    <span><i class="fa-regular fa-angle-right"></i></span>
                </li>
            @endforeach
        @endforelse
    </ul>
</div>
