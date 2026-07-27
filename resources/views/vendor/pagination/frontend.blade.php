@if ($paginator->hasPages())
    {{-- Previous Page Link --}}
    @if ($paginator->onFirstPage())
        <a
            class="pagination_control pagination_control--prev d-flex justify-content-center align-items-center"
            aria-disabled="true"
            aria-label="{{ __('pagination.previous') }}"
        >
            <i class="icon-arrow_left icon"></i>
        </a>
    @else
        <a
            class="pagination_control pagination_control--prev d-flex justify-content-center align-items-center"
            href="{{ $paginator->previousPageUrl() }}"
            rel="prev"
            aria-label="{{ __('pagination.previous') }}"
        >
            <i class="icon-arrow_left icon"></i>
        </a>
    @endif

    <ul class="pagination_list d-flex align-items-center">
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="pagination_list-item">
                    <span class="pagination_list-item_link">{{ $element }}</span>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="pagination_list-item">
                            <a class="pagination_list-item_link pagination_list-item_link--current" href="{{ $url }}" aria-current="page">{{ $page }}</a>
                        </li>
                    @else
                        <li class="pagination_list-item">
                            <a class="pagination_list-item_link" href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach
    </ul>

    {{-- Next Page Link --}}
    @if ($paginator->hasMorePages())
        <a
            class="pagination_control pagination_control--next d-flex justify-content-center align-items-center"
            href="{{ $paginator->nextPageUrl() }}"
            rel="next"
            aria-label="{{ __('pagination.next') }}"
        >
            <i class="icon-arrow_right icon"></i>
        </a>
    @else
        <a
            class="pagination_control pagination_control--next d-flex justify-content-center align-items-center"
            aria-disabled="true"
            aria-label="{{ __('pagination.next') }}"
        >
            <i class="icon-arrow_right icon"></i>
        </a>
    @endif
@endif
