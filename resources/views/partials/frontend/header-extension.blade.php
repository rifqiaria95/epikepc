{{-- Shared header_extension — same structure as About page --}}
<div class="header_extension">
    <div class="container">
        <div class="section_header">
            @if (!empty($subtitle))
                <span class="subtitle subtitle--extended">{{ $subtitle }}</span>
            @endif
            <h1 class="title">{{ $title }}</h1>
            @if (!empty($items))
                <ul class="breadcrumbs d-flex align-items-center">
                    @foreach ($items as $item)
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
        <source data-srcset="{{ asset('frontend/img/lineart.png') }}" srcset="{{ asset('frontend/img/lineart.png') }}" type="image/png" data-role="deco" />
        <img class="lazy plan" data-src="{{ asset('frontend/img/lineart.png') }}" src="{{ asset('frontend/img/lineart.png') }}" alt="Construction lineart decoration" data-role="deco" />
    </picture>
</div>
