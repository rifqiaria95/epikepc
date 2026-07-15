@extends('layouts.frontend.main')

@section('title', 'Our Projects | EPIKEPC')
@section('page', 'projects')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/projects.min.css') }}" />
    <style>
        .header_extension .plan[data-role="deco"] {
            width: min(82vw, 1200px);
            max-width: 1200px;
            right: -6%;
            bottom: -20%;
            opacity: .32;
            transform: none;
            pointer-events: none;
        }
    </style>
@endpush

@section('header_extension')
    <div class="header_extension">
        <div class="container">
            <div class="section_header">
                <span class="subtitle subtitle--extended">Building communities</span>
                <h1 class="title">Our Projects</h1>
                <ul class="breadcrumbs d-flex align-items-center">
                    <li class="breadcrumbs_item">
                        <a href="{{ route('frontend.about.index') }}">Home</a>
                    </li>
                    <li class="breadcrumbs_item breadcrumbs_item--current">
                        <span>Our Projects</span>
                    </li>
                </ul>
            </div>
        </div>
        <picture>
            <source data-srcset="{{ asset('frontend/img/lineart.png') }}" srcset="{{ asset('frontend/img/lineart.png') }}" type="image/webp" data-role="deco" />
            <img class="lazy plan" data-src="{{ asset('frontend/img/lineart.png') }}" src="{{ asset('frontend/img/lineart.png') }}" alt="media" data-role="deco" />
        </picture>
    </div>
@endsection

@section('content')
    <main class="projects section">
        <div class="container">
            <ul class="projects_list row g-0">
                @forelse ($projects as $index => $project)
                    <li class="projects_list-item col-12 col-md-6" data-order="{{ $index + 1 }}">
                        <div class="wrapper d-flex flex-wrap justify-content-between">
                            <div class="img-wrapper" data-aos="zoom-in-right" data-aos-duration="550" data-aos-once="true">
                                <picture>
                                    <source
                                        data-srcset="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        srcset="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="projects_list-item_img lazy"
                                        data-src="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        src="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        alt="{{ $project->title }}"
                                    />
                                </picture>
                            </div>
                            <div class="text-wrapper d-flex flex-column justify-content-between">
                                <h3 class="projects_list-item_title" data-aos="fade-in" data-aos-duration="500" data-aos-once="true">
                                    {{ $project->title }}
                                    <span
                                        class="divider divider--line"
                                        data-aos="slide-right"
                                        data-aos-duration="500"
                                        data-aos-once="true"
                                    ></span>
                                </h3>
                                <div class="projects_list-item_info d-flex flex-wrap align-items-center justify-content-between">
                                    <span
                                        class="location d-flex align-items-center"
                                        data-aos="fade-in"
                                        data-aos-duration="500"
                                        data-aos-delay="50"
                                        data-aos-once="true"
                                    >
                                        <i class="icon-location icon"></i>
                                        {{ $project->category ?? $project->excerpt ?? '' }}
                                    </span>
                                    <a
                                        class="link link-arrow"
                                        href="{{ route('frontend.projects.show', $project->slug) }}"
                                        data-aos="fade-in"
                                        data-aos-duration="500"
                                        data-aos-delay="50"
                                        data-aos-once="true"
                                    >
                                        Details
                                        <i class="icon-arrow_right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="col-12 text-center" style="padding: 60px 0;">
                        <p style="color: #888;">No projects published yet.</p>
                    </li>
                @endforelse
            </ul>

            {{-- Pagination --}}
            @if ($projects->hasPages())
                <div class="d-flex justify-content-center" style="margin-top: 40px;">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </main>
@endsection
