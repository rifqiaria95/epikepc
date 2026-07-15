@php
    $excerpt = $service->subtitle ?: \Illuminate\Support\Str::limit(strip_tags($service->description), 120);
    $detailUrl = route('frontend.detail-service', $service->id);
@endphp

<div class="col-xl-3 col-lg-4 col-md-6 wow itfadeUp" data-wow-duration=".9s"
    data-wow-delay="{{ number_format($delay, 1) }}s">
    <div class="single-service-item style-2 mb-30">
        <div class="thumb">
            <a href="{{ $detailUrl }}">
                <img src="{{ $service->detail_image_url }}" alt="{{ $service->title }}">
            </a>
            <span class="icon">
                @include('partials.home.service-icon')
            </span>
        </div>
        <div class="service-content">
            <h2>
                <a href="{{ $detailUrl }}">{{ $service->title }}</a>
            </h2>
            <p>{{ $excerpt }}</p>
            <a href="{{ $detailUrl }}" class="tv-normal-btn">Selengkapnya <i
                    class="fa-solid fa-arrow-right"></i></a>
        </div>
    </div>
</div>
