@extends('layouts.frontend.main')

@section('title', 'Our Projects | EPIKEPC')
@section('page', 'projects')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/projects.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/leaflet.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/indonesia-map.css') }}" />
    <style>
        .pagination {
            margin-top: 60px;
        }

        .pagination_control {
            border-radius: 50%;
            width: 30px;
            height: 30px;
            font-size: 16px;
            line-height: 22px;
        }

        .pagination_control .icon {
            transition: all .3s ease-in-out;
        }

        .pagination_control--prev:hover:not([aria-disabled="true"]) .icon,
        .pagination_control--prev:focus:not([aria-disabled="true"]) .icon {
            margin-right: 5px;
        }

        .pagination_control[aria-disabled="true"] {
            border: 1px solid #C8C8C8;
            color: #C8C8C8;
            cursor: default;
            pointer-events: none;
        }

        .pagination_control--next {
            border: 1px solid #253C74;
        }

        .pagination_control--next:hover:not([aria-disabled="true"]) .icon,
        .pagination_control--next:focus:not([aria-disabled="true"]) .icon {
            margin-left: 5px;
        }

        .pagination_list {
            margin: 0 30px;
            list-style: none;
            padding: 0;
        }

        .pagination_list-item {
            margin-right: 30px;
        }

        .pagination_list-item:last-of-type {
            margin-right: 0;
        }

        .pagination_list-item_link {
            color: #A9A9A9;
            transition: all .3s ease-in-out;
            text-decoration: none;
        }

        .pagination_list-item_link:hover,
        .pagination_list-item_link:focus,
        .pagination_list-item_link--current {
            color: #202C38;
            font-weight: 500;
        }

        /* Light-theme map adaptations for projects page */
        .proj-map--light {
            margin-bottom: 48px;
        }

        .proj-map--light .proj-map__canvas {
            border: 1px solid #e5e9f2;
            box-shadow: 0 12px 32px rgba(37, 60, 116, 0.08);
        }

        .proj-map--light .proj-map__panel {
            background: #f7f9fc;
            border: 1px solid #e5e9f2;
            backdrop-filter: none;
        }

        .proj-map--light .proj-map__placeholder-title,
        .proj-map--light .proj-map__detail-title {
            color: #202C38;
        }

        .proj-map--light .proj-map__placeholder-text,
        .proj-map--light .proj-map__detail-text,
        .proj-map--light .proj-map__detail-meta-item {
            color: #6b7280;
        }

        .proj-map--light .proj-map__placeholder-icon {
            background: rgba(37, 60, 116, 0.08);
        }

        .proj-map--light .proj-map__placeholder-icon .icon {
            color: #253C74;
        }

        .proj-map--light .proj-map__detail-close {
            background: #fff;
            border-color: #e5e9f2;
            color: #6b7280;
        }

        .proj-map--light .proj-map__detail-divider {
            background: #e5e9f2;
        }

        .proj-map--light .proj-map__detail-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            color: #253C74;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
        }

        .proj-map__detail-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 16px;
            color: #FFdf08;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
        }

        .projects-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .projects-stat {
            border: 1px solid #e5e9f2;
            border-radius: 14px;
            padding: 18px 20px;
            background: #fff;
        }

        .projects-stat__label {
            display: block;
            font-size: 0.8125rem;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .projects-stat__value {
            font-family: Archivo, sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #202C38;
            line-height: 1;
        }

        .projects-stat--ongoing .projects-stat__value { color: #b45309; }
        .projects-stat--completed .projects-stat__value { color: #1a6b45; }

        .projects-status-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 24px;
        }

        .projects-status-tab {
            font-family: Archivo, sans-serif;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 10px 18px;
            border: 1.5px solid #d7dde8;
            border-radius: 100px;
            background: transparent;
            color: #6b7280;
            text-decoration: none;
            transition: all .2s ease;
        }

        .projects-status-tab:hover,
        .projects-status-tab.is-active {
            border-color: #253C74;
            background: #253C74;
            color: #fff;
        }

        .projects-status-badge {
            display: inline-flex;
            align-items: center;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            border-radius: 100px;
            padding: 4px 10px;
            margin-left: 10px;
            vertical-align: middle;
        }

        .projects-status-badge--ongoing {
            background: rgba(180, 83, 9, 0.12);
            color: #b45309;
        }

        .projects-status-badge--completed {
            background: rgba(26, 107, 69, 0.12);
            color: #1a6b45;
        }

        @media (max-width: 767px) {
            .projects-stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Building communities',
        'title'    => 'Our Projects',
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Our Projects'],
        ],
    ])
@endsection

@section('content')
    @php
        $counts = $projectMap['counts'] ?? ['all' => 0, 'ongoing' => 0, 'completed' => 0];
        $activeStatus = $statusFilter ?: 'all';
    @endphp

    <main class="projects section">
        <div class="container">
            <div class="projects-stats" data-aos="fade-up">
                <div class="projects-stat">
                    <span class="projects-stat__label">All Projects</span>
                    <strong class="projects-stat__value">{{ number_format($counts['all']) }}</strong>
                </div>
                <div class="projects-stat projects-stat--ongoing">
                    <span class="projects-stat__label">On Going</span>
                    <strong class="projects-stat__value">{{ number_format($counts['ongoing']) }}</strong>
                </div>
                <div class="projects-stat projects-stat--completed">
                    <span class="projects-stat__label">Completed</span>
                    <strong class="projects-stat__value">{{ number_format($counts['completed']) }}</strong>
                </div>
            </div>

            <div class="projects-status-tabs" data-aos="fade-up" data-aos-delay="40">
                <a
                    href="{{ route('frontend.projects.index') }}"
                    class="projects-status-tab {{ $activeStatus === 'all' ? 'is-active' : '' }}"
                    data-status-filter="all"
                >All Projects</a>
                <a
                    href="{{ route('frontend.projects.index', ['status' => 'ongoing']) }}"
                    class="projects-status-tab {{ $activeStatus === 'ongoing' ? 'is-active' : '' }}"
                    data-status-filter="ongoing"
                >On Going</a>
                <a
                    href="{{ route('frontend.projects.index', ['status' => 'completed']) }}"
                    class="projects-status-tab {{ $activeStatus === 'completed' ? 'is-active' : '' }}"
                    data-status-filter="completed"
                >Completed</a>
            </div>

            <div class="proj-map proj-map--light" data-proj-map data-aos="fade-up" data-aos-delay="80">
                <div class="proj-map__layout">
                    <div class="proj-map__canvas" data-proj-map-canvas>
                        <div id="indonesia-leaflet-map"></div>
                    </div>
                    <div class="proj-map__panel" data-proj-map-panel>
                        <div class="proj-map__panel-placeholder" data-proj-map-placeholder>
                            <div class="proj-map__placeholder-icon">
                                <i class="icon icon-location" aria-hidden="true"></i>
                            </div>
                            <p class="proj-map__placeholder-title">Project Locations</p>
                            <p class="proj-map__placeholder-text">
                                Click a map marker to view project details. Filter by On Going or Completed to focus on project status.
                            </p>
                        </div>
                        <div class="proj-map__detail" data-proj-map-detail></div>
                    </div>
                </div>
            </div>

            <ul class="projects_list row g-0">
                @forelse ($projects as $index => $project)
                    @php
                        $statusValue = $project->status?->value ?? 'completed';
                        $statusLabel = $project->status_label;
                        $locationLabel = $project->location ?: ($project->category ?? $project->excerpt ?? '');
                    @endphp
                    <li class="projects_list-item col-12 col-md-6" data-order="{{ $index + 1 }}" data-status="{{ $statusValue }}">
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
                                    <span class="projects-status-badge projects-status-badge--{{ $statusValue }}">{{ $statusLabel }}</span>
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
                                        {{ $locationLabel }}
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
                        <p style="color: #888;">No projects found for this filter.</p>
                    </li>
                @endforelse
            </ul>

            @if ($projects->hasPages())
                <div class="pagination d-flex align-items-center justify-content-center">
                    {{ $projects->links('vendor.pagination.frontend') }}
                </div>
            @endif
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        window.PROJECT_MAP_DATA = @json($projectMap['markers'] ?? []);
        window.PROJECT_MAP_CONFIG = Object.assign(
            @json($projectMap['config'] ?? ['filterMode' => 'status', 'leafletBasePath' => asset('frontend/img/leaflet')]),
            { initialFilter: @json($activeStatus) }
        );
    </script>
    <script src="{{ asset('frontend/js/leaflet.min.js') }}"></script>
    <script src="{{ asset('frontend/js/indonesia-map.js') }}"></script>
    <script>
        (function () {
            document.querySelectorAll('[data-status-filter]').forEach(function (tab) {
                tab.addEventListener('click', function () {
                    var filter = tab.getAttribute('data-status-filter') || 'all';
                    if (typeof window.projMapFilter === 'function') {
                        window.projMapFilter(filter);
                    }
                });
            });
        })();
    </script>
@endpush
