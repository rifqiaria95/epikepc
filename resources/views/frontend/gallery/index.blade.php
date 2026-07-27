@extends('layouts.frontend.main')

@section('title', 'Gallery | EPIKEPC')
@section('page', 'gallery')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/gallery-grid.min.css') }}" />
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Building communities',
        'title'    => 'Gallery',
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Gallery'],
        ],
    ])
@endsection

@section('content')
    <!-- GALLERY GRID CONTENT START -->
    <main class="gallery section">
        <div class="container">
            <ul class="gallery_grid d-grid">
                @forelse ($items as $index => $item)
                    <li class="gallery_grid-item"
                        data-aos="fade-up"
                        data-aos-once="true"
                        data-order="{{ $index + 1 }}">
                        <a
                            class="gallery_grid-item_trigger"
                            href="{{ $item->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                            data-caption="{{ $item->title }}"
                            data-role="gallery-link"
                        >
                            <picture>
                                <source
                                    data-srcset="{{ $item->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    srcset="{{ $item->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                />
                                <img
                                    class="gallery_grid-item_img lazy"
                                    data-src="{{ $item->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    src="{{ $item->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    alt="{{ $item->title }}"
                                />
                            </picture>
                            <div class="overlay d-flex flex-column justify-content-end">
                                <h4 class="overlay_caption">{{ $item->title }}</h4>
                                <span class="overlay_label">
                                    @if ($item->subtitle)
                                        {{ $item->subtitle }}
                                    @elseif ($item->kategoriGaleri)
                                        {{ $item->kategoriGaleri->name ?? $item->kategoriGaleri->nama ?? '' }}
                                    @endif
                                </span>
                            </div>
                        </a>
                    </li>
                @empty
                    <li class="text-center py-5">
                        <p>No gallery items available yet.</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </main>
    <!-- GALLERY GRID CONTENT END -->
@endsection
