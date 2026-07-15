@props([
    'title',
    'items' => [],
])

<div class="tv-breadcrumb-area tv-breadcrumb-overlay tv-breadcrumb-ptb z-index-1 fix p-relative"
    data-background="{{ asset('frontend/img/breadcrumb/breadcrumb.jpg') }}">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="tv-breadcrumb-content z-index-1 text-center">
                    <div class="tv-breadcrumb-title-box">
                        <h3 class="tv-breadcrumb-title tv-spltv-text tv-spltv-in-right">{{ $title }}</h3>
                    </div>
                    <div class="tv-breadcrumb-list-wrap">
                        <div class="tv-breadcrumb-list">
                            @foreach ($items as $index => $item)
                                @if ($index > 0)
                                    <span class="dvdr">
                                        <svg width="7" height="12" viewBox="0 0 7 12" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="M4.47698 6.00058L0.352151 10.1253L1.53065 11.3038L6.83398 6.00058L1.53065 0.697266L0.352151 1.87577L4.47698 6.00058Z"
                                                fill="#F5F6F7" />
                                        </svg>
                                    </span>
                                @endif

                                @if (!empty($item['url']))
                                    <span><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></span>
                                @else
                                    <i>{{ $item['label'] }}</i>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
