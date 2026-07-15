<div class="header_extension">
    <div class="container">
        <div class="section_header">
            @if (!empty($subtitle))
                <span class="subtitle subtitle--extended">{{ $subtitle }}</span>
            @endif
            <h1 class="title">{{ $title }}</h1>
            @if (!empty($items))
                <ul class="breadcrumbs d-flex align-items-center">
                    @foreach ($items as $index => $item)
                        @if ($loop->last)
                            <li class="breadcrumbs_item breadcrumbs_item--current">
                                <span>{{ $item['label'] }}</span>
                            </li>
                        @else
                            <li class="breadcrumbs_item">
                                <a href="{{ $item['url'] ?? '#' }}">{{ $item['label'] }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
    <picture>
        <source data-srcset="{{ asset('frontend/img/placeholder.jpg') }}" srcset="{{ asset('frontend/img/placeholder.jpg') }}" type="image/webp" data-role="deco" />
        <img class="lazy plan" data-src="{{ asset('frontend/img/placeholder.jpg') }}" src="{{ asset('frontend/img/placeholder.jpg') }}" alt="" data-role="deco" />
    </picture>
</div>
