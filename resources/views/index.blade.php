@extends('layouts.frontend.main')

@section('title', 'Home | EPIKEPC')
@section('page', 'home')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/index2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/board-slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/indonesia-map.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/company-journey.css') }}?v={{ @filemtime(public_path('frontend/css/company-journey.css')) ?: time() }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/certificate-gallery.css') }}?v={{ @filemtime(public_path('frontend/css/certificate-gallery.css')) ?: time() }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/instagram-widget.css') }}?v={{ @filemtime(public_path('frontend/css/instagram-widget.css')) ?: time() }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/leaflet.min.css') }}" />
@endpush

@section('content')
    <main>
        {{-- ===== HERO SLIDER ===== --}}
        @php
            $heroInitialBg = $services->first()?->image_url ?: asset('frontend/img/img-1.png');
        @endphp
        <section
            class="hero primary-bg"
            style="background-image: url('{{ $heroInitialBg }}'); background-size: cover; background-position: center;"
        >
            <span class="hero_overlay"></span>
            <div class="container container--slider d-flex flex-wrap justify-content-center">
                <div class="wrapper col-lg-11">
                    <ul class="hero_slider d-flex">
                        @forelse ($services as $index => $service)
                            @php $slideClass = 'hero_slider-slide--0' . (($index % 5) + 1); @endphp
                            <li class="hero_slider-slide {{ $slideClass }}" data-bg="{{ $service->image_url ?: asset('frontend/img/img-1.png') }}">
                                <div class="hero_slider-slide_content section_header">
                                    <span class="subtitle subtitle--extended">Our Services</span>
                                    <h2 class="title">{{ $service->title }}</h2>
                                    <p class="text">{{ Str::limit(strip_tags($service->subtitle ?: $service->description), 160) }}</p>
                                    <a class="btn" href="{{ route('frontend.detail-service', $service->id) }}">Service Details</a>
                                </div>
                            </li>
                        @empty
                            <li class="hero_slider-slide hero_slider-slide--01" data-bg="{{ asset('frontend/img/img-1.png') }}">
                                <div class="hero_slider-slide_content section_header">
                                    <span class="subtitle subtitle--extended">Our Services</span>
                                    <h2 class="title">Process Engineering</h2>
                                    <p class="text">Trusted engineering solutions for your infrastructure and construction projects in Indonesia.</p>
                                    <a class="btn" href="{{ route('frontend.services.index') }}">View Services</a>
                                </div>
                            </li>
                        @endforelse
                        {{-- Duplicate slides for infinite loop --}}
                        @foreach ($services as $index => $service)
                            @php $slideClass = 'hero_slider-slide--0' . (($index % 5) + 1); @endphp
                            <li class="hero_slider-slide {{ $slideClass }}" data-bg="{{ $service->image_url ?: asset('frontend/img/img-1.png') }}">
                                <div class="hero_slider-slide_content section_header">
                                    <span class="subtitle subtitle--extended">Our Services</span>
                                    <h2 class="title">{{ $service->title }}</h2>
                                    <p class="text">{{ Str::limit(strip_tags($service->subtitle ?: $service->description), 160) }}</p>
                                    <a class="btn" href="{{ route('frontend.detail-service', $service->id) }}">Service Details</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div
                    class="
                        hero_slider-nav hero_slider-nav--alt
                        col-lg-1
                        d-flex
                        align-items-center
                        justify-content-center
                        flex-lg-column
                        align-items-md-end
                    "
                >
                    @forelse ($services as $i => $s)
                        <button class="hero_slider-nav_dot {{ $i === 0 ? 'tns-nav-active' : '' }}"></button>
                    @empty
                        <button class="hero_slider-nav_dot tns-nav-active"></button>
                    @endforelse
                    @foreach ($services as $s)
                        <button class="hero_slider-nav_dot"></button>
                    @endforeach
                </div>
            </div>
            <div class="container-fluid container--thumbs">
                <ul class="hero_thumbs">
                    @forelse ($services as $i => $service)
                        <li class="hero_thumbs-thumb {{ $i === 0 ? 'is-current' : '' }}" data-bg="{{ $service->image_url ?: asset('frontend/img/img-1.png') }}">
                            <div class="hero_thumbs-thumb_inner d-flex flex-column justify-content-end">
                                <span class="overlay"></span>
                                <h4 class="title">{{ $service->title }}</h4>
                            </div>
                        </li>
                    @empty
                        <li class="hero_thumbs-thumb is-current" data-bg="{{ asset('frontend/img/img-1.png') }}">
                            <div class="hero_thumbs-thumb_inner d-flex flex-column justify-content-end">
                                <span class="overlay"></span>
                                <h4 class="title">Process Engineering</h4>
                            </div>
                        </li>
                    @endforelse
                    @foreach ($services as $i => $service)
                        <li class="hero_thumbs-thumb" data-bg="{{ $service->image_url ?: asset('frontend/img/img-1.png') }}">
                            <div class="hero_thumbs-thumb_inner d-flex flex-column justify-content-end">
                                <span class="overlay"></span>
                                <h4 class="title">{{ $service->title }}</h4>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        {{-- ===== PROJECT HIGHLIGHTS ===== --}}
        <section class="proj-highlights section" style="padding: 90px 0; background: #253C74;">
            <div class="container">

                <!-- Section Header -->
                <div class="row align-items-end mb-5" data-aos="fade-up">
                    <div class="col-12 col-md-7">
                        <span class="section_header" style="display:block;">
                            <span class="subtitle" style="color:#a0aec0;">Our Portfolio</span>
                        </span>
                        <h2 style="font-family: Archivo, sans-serif; font-size: clamp(1.875rem, 4vw, 2.875rem); font-weight: 700; color: #fff; line-height: 1.2; margin: 0;">
                            Project <span style="color:#FFdf08;">Highlights</span>
                        </h2>
                    </div>
                    <div class="col-12 col-md-5 mt-3 mt-md-0">
                        <p style="color: #a0aec0; font-size: 0.9375rem; line-height: 1.75; margin: 0; text-align: left; text-align: revert;">
                            A curated selection of our most impactful work across engineering, architecture, and infrastructure.
                        </p>
                    </div>
                </div>

                <!-- Filter Tabs -->
                <div class="proj-filter-wrap" style="overflow-x: auto; -webkit-overflow-scrolling: touch; padding-bottom: 4px; margin-bottom: 40px;" data-aos="fade-up" data-aos-delay="100">
                    <div class="proj-filter-tabs" style="display: flex; gap: 8px; min-width: max-content;">
                        <button class="proj-tab proj-tab--active" data-filter="all" onclick="projFilter(this,'all')" style="font-family: Archivo, sans-serif; font-size: 0.875rem; font-weight: 600; padding: 10px 22px; border: 1.5px solid #FFdf08; border-radius: 100px; background: #FFdf08; color: #000810; cursor: pointer; transition: all .25s; white-space: nowrap; letter-spacing: 0.02em;">All Projects</button>
                        <button class="proj-tab" data-filter="civil" onclick="projFilter(this,'civil')" style="font-family: Archivo, sans-serif; font-size: 0.875rem; font-weight: 600; padding: 10px 22px; border: 1.5px solid rgba(255,255,255,0.15); border-radius: 100px; background: transparent; color: #a0aec0; cursor: pointer; transition: all .25s; white-space: nowrap; letter-spacing: 0.02em;">Civil Engineering</button>
                        <button class="proj-tab" data-filter="architecture" onclick="projFilter(this,'architecture')" style="font-family: Archivo, sans-serif; font-size: 0.875rem; font-weight: 600; padding: 10px 22px; border: 1.5px solid rgba(255,255,255,0.15); border-radius: 100px; background: transparent; color: #a0aec0; cursor: pointer; transition: all .25s; white-space: nowrap; letter-spacing: 0.02em;">Architecture</button>
                        <button class="proj-tab" data-filter="infrastructure" onclick="projFilter(this,'infrastructure')" style="font-family: Archivo, sans-serif; font-size: 0.875rem; font-weight: 600; padding: 10px 22px; border: 1.5px solid rgba(255,255,255,0.15); border-radius: 100px; background: transparent; color: #a0aec0; cursor: pointer; transition: all .25s; white-space: nowrap; letter-spacing: 0.02em;">Infrastructure</button>
                        <button class="proj-tab" data-filter="specialty" onclick="projFilter(this,'specialty')" style="font-family: Archivo, sans-serif; font-size: 0.875rem; font-weight: 600; padding: 10px 22px; border: 1.5px solid rgba(255,255,255,0.15); border-radius: 100px; background: transparent; color: #a0aec0; cursor: pointer; transition: all .25s; white-space: nowrap; letter-spacing: 0.02em;">Specialty Services</button>
                    </div>
                </div>

                <!-- Indonesia Project Map -->
                <div class="proj-map" data-proj-map data-aos="fade-up" data-aos-delay="80">
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
                                <p class="proj-map__placeholder-text">Click a map marker to view project details. Use the filters above to narrow results by category.</p>
                            </div>
                            <div class="proj-map__detail" data-proj-map-detail></div>
                        </div>
                    </div>
                </div>

                <!-- Project Grid -->
                <div class="proj-grid row g-4" id="projGrid">
                    @forelse ($projects as $index => $project)
                        @php
                            $delays = [0, 60, 120];
                            $delay  = $delays[$index % 3];
                            $cat    = Str::slug($project->category ?? 'general');
                        @endphp
                        <div class="proj-card-col col-12 col-sm-6 col-lg-4" data-category="{{ $cat }}" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                            <div class="proj-card" style="position:relative; border-radius:12px; overflow:hidden; background:#111827; cursor:pointer; height:320px;">
                                <img src="{{ $project->image_url ?: asset('frontend/img/img-1.png') }}" alt="{{ $project->title }}" style="width:100%;height:100%;object-fit:cover;display:block;transition:transform .5s ease;">
                                <div class="proj-card__overlay" style="position:absolute;inset:0;background:linear-gradient(to top, rgba(2,72,193,0.92) 0%, rgba(0,8,16,0.3) 55%, transparent 100%);transition:opacity .3s;"></div>
                                @if ($project->category)
                                    <span class="proj-card__badge" style="position:absolute;top:16px;left:16px;background:rgba(255,198,49,0.95);color:#000810;font-family:Archivo,sans-serif;font-size:0.7rem;font-weight:700;padding:5px 12px;border-radius:100px;letter-spacing:0.06em;text-transform:uppercase;">{{ $project->category }}</span>
                                @endif
                                <div class="proj-card__info" style="position:absolute;bottom:0;left:0;right:0;padding:24px 22px;transform:translateY(0);transition:transform .35s ease;">
                                    <h4 style="font-family:Archivo,sans-serif;font-size:1.125rem;font-weight:700;color:#fff;margin:0 0 10px;line-height:1.3;">
                                        <a href="{{ route('frontend.projects.show', $project->slug) }}" style="color:inherit;text-decoration:none;">{{ $project->title }}</a>
                                    </h4>
                                    @if ($project->excerpt)
                                        <p class="proj-card__desc" style="color:rgba(255,255,255,0.75);font-size:0.8125rem;line-height:1.6;margin:0;max-height:0;overflow:hidden;transition:max-height .35s ease,opacity .3s;opacity:0;">{{ Str::limit(strip_tags($project->excerpt), 180) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center" style="padding: 60px 0;">
                            <p style="color:#a0aec0; font-size:1rem;">No projects to display yet.</p>
                        </div>
                    @endforelse
                </div><!-- /proj-grid -->

                <!-- Empty state -->
                <div id="projEmpty" style="display:none; text-align:center; padding: 60px 0;">
                    <p style="color:#a0aec0; font-size:1rem;">No projects found for this category.</p>
                </div>

                <!-- CTA -->
                <div class="text-center mt-5" data-aos="fade-up">
                    <a class="btn" href="{{ route('frontend.projects.index') }}" style="display:inline-block; background:#FFdf08; color:#000810; font-family:Archivo,sans-serif; font-weight:700; padding:16px 36px; border-radius:4px; text-decoration:none; font-size:0.9375rem; letter-spacing:0.02em; transition: background .2s;">View All Projects</a>
                </div>

            </div>

            <style>
                /* Card hover interactions */
                .proj-card:hover img {
                    transform: scale(1.06);
                }
                .proj-card:hover .proj-card__desc {
                    max-height: 100px !important;
                    opacity: 1 !important;
                }
                /* Tab hover */
                .proj-tab:not(.proj-tab--active):hover {
                    border-color: rgba(255,255,255,0.5) !important;
                    color: #fff !important;
                }
                /* Filter animation */
                .proj-card-col {
                    transition: opacity .35s ease, transform .35s ease;
                }
                .proj-card-col.proj-hidden {
                    display: none !important;
                }
                /* Scrollbar for filter strip (webkit) */
                .proj-filter-wrap::-webkit-scrollbar { height: 3px; }
                .proj-filter-wrap::-webkit-scrollbar-track { background: transparent; }
                .proj-filter-wrap::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 10px; }
                /* Keep map section within viewport on mobile */
                .proj-highlights .proj-map,
                .proj-highlights .proj-map__layout,
                .proj-highlights .proj-map__canvas {
                    max-width: 100%;
                    min-width: 0;
                }
                /* CTA button hover */
                .proj-highlights .btn:hover {
                    background: #ffd464 !important;
                }
            </style>

            <script>
                function projFilter(btn, filter) {
                    /* Update tab styles */
                    document.querySelectorAll('.proj-tab').forEach(function(t) {
                        t.classList.remove('proj-tab--active');
                        t.style.background = 'transparent';
                        t.style.borderColor = 'rgba(255,255,255,0.15)';
                        t.style.color = '#a0aec0';
                    });
                    btn.classList.add('proj-tab--active');
                    btn.style.background = '#FFdf08';
                    btn.style.borderColor = '#FFdf08';
                    btn.style.color = '#000810';

                    /* Filter cards */
                    var cards = document.querySelectorAll('#projGrid .proj-card-col');
                    var visible = 0;
                    cards.forEach(function(card) {
                        var cat = card.getAttribute('data-category');
                        if (filter === 'all' || cat === filter) {
                            card.classList.remove('proj-hidden');
                            visible++;
                        } else {
                            card.classList.add('proj-hidden');
                        }
                    });
                    document.getElementById('projEmpty').style.display = visible === 0 ? 'block' : 'none';

                    /* Sync map markers */
                    if (typeof window.projMapFilter === 'function') {
                        window.projMapFilter(filter);
                    }
                }
            </script>
        </section>

        @include('partials.frontend.certificate-gallery')

        {{-- ===== GALLERY ===== --}}
        <section class="gallery section">
            <div class="container">
                <div class="gallery_header section_header">
                    <span class="subtitle"> What we do </span>
                    <h2 class="title">
                        Our
                        <span class="highlight">Gallery</span>
                    </h2>
                </div>
                <ul class="gallery_grid d-grid">
                    @forelse($galleryItems as $index => $item)
                        <li class="gallery_grid-item" data-aos="fade-up" data-aos-once="true" data-order="{{ $index + 1 }}">
                            <a
                                class="gallery_grid-item_trigger"
                                href="{{ $item->image_url ?: asset('frontend/img/img-1.png') }}"
                                data-caption="{{ $item->title }}"
                                data-role="gallery-link"
                            >
                                <picture>
                                    <source
                                        data-srcset="{{ $item->image_url ?: asset('frontend/img/img-1.png') }}"
                                        srcset="{{ $item->image_url ?: asset('frontend/img/img-1.png') }}"
                                    />
                                    <img
                                        class="gallery_grid-item_img lazy"
                                        data-src="{{ $item->image_url ?: asset('frontend/img/img-1.png') }}"
                                        src="{{ $item->image_url ?: asset('frontend/img/img-1.png') }}"
                                        alt="{{ $item->title }}"
                                    />
                                </picture>
                                <div class="overlay d-flex flex-column justify-content-end">
                                    <h4 class="overlay_caption">{{ $item->title }}</h4>
                                    <span class="overlay_label">{{ $item->kategoriGaleri?->name ?? ($item->subtitle ?: 'Special projects') }}</span>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="gallery_grid-item" data-aos="fade-up" data-aos-once="true" data-order="1">
                            <a class="gallery_grid-item_trigger" href="{{ asset('frontend/img/img-1.png') }}" data-caption="Gallery" data-role="gallery-link">
                                <picture>
                                    <source data-srcset="{{ asset('frontend/img/img-1.png') }}" srcset="{{ asset('frontend/img/img-1.png') }}" />
                                    <img class="gallery_grid-item_img lazy" data-src="{{ asset('frontend/img/img-1.png') }}" src="{{ asset('frontend/img/img-1.png') }}" alt="Gallery fallback" />
                                </picture>
                                <div class="overlay d-flex flex-column justify-content-end">
                                    <h4 class="overlay_caption">Gallery</h4>
                                    <span class="overlay_label">Special projects</span>
                                </div>
                            </a>
                        </li>
                    @endforelse
                </ul>
            </div>
        </section>

        {{-- ===== COMPANY JOURNEY ===== --}}
        @if($companyJourney->is_active)
        <section class="c-journey section primary-bg" data-aos="fade-up">
            <div class="container">

                <!-- Header -->
                <div class="c-journey__header section_header" data-aos="fade-up">
                    <span class="subtitle">{{ $companyJourney->section_subtitle ?? 'Our Story' }}</span>
                    <h2 class="title">
                        {{ $companyJourney->section_title ?? 'Company' }}
                        <span class="highlight">{{ $companyJourney->section_title_highlight ?? 'Journey' }}</span>
                    </h2>
                    <p class="c-journey__header-desc">{{ $companyJourney->section_description }}</p>
                </div>

                <!-- Company Profile Video -->
                <div class="c-journey__video" data-aos="fade-up" data-aos-delay="80">
                    <div class="c-journey__video-inner" data-cp-video>
                        <iframe
                            class="c-journey__iframe"
                            data-cp-iframe
                            src=""
                            data-src="{{ $companyJourney->video_embed_url }}"
                            title="{{ $companyJourney->video_poster_title ?? 'EPIKEPC Company Profile Video' }}"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                        <!-- Poster overlay — click to play -->
                        <div class="c-journey__poster" data-cp-poster aria-label="Play company profile video"@if(!empty($companyJourney->poster_url)) style="background-image: url('{{ $companyJourney->poster_url }}'); background-size: cover; background-position: center;"@endif>
                            <div class="c-journey__poster-content">
                                <button class="c-journey__play-btn" data-cp-play aria-label="Play video">
                                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                        <path d="M8 5.14v14l11-7-11-7z"/>
                                    </svg>
                                </button>
                                <div class="c-journey__poster-labels">
                                    @if($companyJourney->video_poster_tag)
                                        <span class="c-journey__poster-tag">{{ $companyJourney->video_poster_tag }}</span>
                                    @endif
                                    @if($companyJourney->video_poster_title)
                                        <h3 class="c-journey__poster-title">{{ $companyJourney->video_poster_title }}</h3>
                                    @endif
                                    @if($companyJourney->video_established || $companyJourney->video_location)
                                        <div class="c-journey__poster-meta">
                                            @if($companyJourney->video_established)
                                                <span>{{ $companyJourney->video_established }}</span>
                                            @endif
                                            @if($companyJourney->video_location)
                                                <span>{{ $companyJourney->video_location }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <!-- Bottom label bar -->
                        @if($companyJourney->video_caption || $companyJourney->video_duration)
                            <div class="c-journey__video-hint" aria-hidden="true">
                                <div class="c-journey__video-label-row">
                                    <span class="c-journey__video-dot"></span>
                                    @if($companyJourney->video_caption)
                                        <span class="c-journey__video-desc">{{ $companyJourney->video_caption }}</span>
                                    @endif
                                </div>
                                @if($companyJourney->video_duration)
                                    <span class="c-journey__video-tag">{{ $companyJourney->video_duration }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Teameline -->
                @if($companyMilestones->isNotEmpty())
                <div class="c-journey__tl" data-cj-timeline data-aos="fade-up" data-aos-delay="120">

                    <!-- Teameline head -->
                    <div class="c-journey__tl-head">
                        <div>
                            <span class="c-journey__tl-eyebrow">{{ $companyJourney->timeline_subtitle ?? '' }}</span>
                            <h3 class="c-journey__tl-title">{{ $companyJourney->timeline_title ?? 'Project Journey' }}</h3>
                        </div>
                        <div class="c-journey__tl-controls">
                            <button class="c-journey__tl-btn" data-tl-prev aria-label="Previous milestones">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
                            </button>
                            <span class="c-journey__tl-pager" data-tl-pager aria-live="polite">1 / {{ $companyMilestones->count() }}</span>
                            <button class="c-journey__tl-btn" data-tl-next aria-label="Next milestones">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Scroll hint -->
                    <div class="c-journey__tl-hint" data-tl-hint aria-hidden="true">
                        <span class="c-journey__tl-hint-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </span>
                        <span>Slide to explore milestones</span>
                    </div>

                    <!-- Viewport -->
                    <div class="c-journey__tl-viewport" data-tl-viewport aria-label="Company journey timeline, drag or use arrow keys to navigate">
                        <div class="c-journey__tl-track" data-tl-track>

                            <!-- Teameline line -->
                            <div class="c-journey__tl-line" aria-hidden="true">
                                <div class="c-journey__tl-line-fill" data-tl-line-fill></div>
                            </div>

                            @foreach($companyMilestones as $milestone)
                            <div class="c-journey__item" data-tl-item>
                                <div class="c-journey__card">
                                    <span class="c-journey__card-badge">{{ $milestone->period_label }}</span>
                                    <h4 class="c-journey__card-title">{{ $milestone->title }}</h4>
                                    <p class="c-journey__card-text">{{ $milestone->description }}</p>
                                </div>
                                <div class="c-journey__stem" aria-hidden="true"></div>
                                <div class="c-journey__dot" aria-hidden="true"></div>
                                <span class="c-journey__year">{{ $milestone->period_label }}</span>
                            </div>
                            @endforeach

                        </div><!-- /.c-journey__tl-track -->
                    </div><!-- /.c-journey__tl-viewport -->

                    <!-- Progress bar -->
                    <div class="c-journey__tl-progress" aria-hidden="true">
                        <div class="c-journey__tl-progress-fill" data-tl-progress></div>
                    </div>

                </div><!-- /.c-journey__tl -->
                @endif

            </div><!-- /.container -->
        </section>
        @endif

        {{-- ===== INSTAGRAM ===== --}}
        @include('partials.frontend.instagram-widget')

        {{-- ===== CONTACT ===== --}}
        @php $contact = config('frontend_contact'); @endphp
        <section class="contact section">
            <div class="container d-lg-flex flex-wrap justify-content-between align-items-end">
                <div class="contact-wrapper col-lg-6 col-xxl-auto">
                    <div class="contact_header section_header">
                        <span class="subtitle">Contact us</span>
                        <h2 class="title">
                            <span class="highlight">Contacts</span>
                            information
                        </h2>
                        <p class="text">
                            Our team is ready to help you start your next project. Contact us for consultation and further information.
                        </p>
                    </div>
                    <ul class="contact-info">
                        <li class="contact-info_group">
                            <span class="name">Address</span>
                            <span class="content">{{ $contact['address'] ?? '' }}</span>
                        </li>
                        <li class="contact-info_group">
                            <span class="name">Email</span>
                            <span class="content d-inline-flex flex-column">
                                <a class="link" href="{{ $contact['email_href'] ?? '#' }}">{{ $contact['email'] ?? '' }}</a>
                            </span>
                        </li>
                        <li class="contact-info_group">
                            <span class="name">Phone</span>
                            <span class="content d-inline-flex flex-column">
                                <a class="link" href="{{ $contact['phone_href'] ?? '#' }}">{{ $contact['phone'] ?? '' }}</a>
                            </span>
                        </li>
                    </ul>
                    <ul class="socials d-flex align-items-center justify-content-sm-start">
                        <li class="socials_item">
                            <a class="socials_item-link" href="https://www.instagram.com/epikepc/" target="_blank" rel="noopener noreferrer">
                                <i class="icon-instagram"></i>
                            </a>
                        </li>
                        <li class="socials_item">
                            <a class="socials_item-link" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact['phone']) }}" target="_blank" rel="noopener noreferrer">
                                <i class="icon-whatsapp"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="contact_map col-12 col-lg-auto">
                    @if (!empty($contact['map_embed_url']))
                        <iframe src="{{ $contact['map_embed_url'] }}" style="width:100%;min-height:350px;border:0;border-radius:8px;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    @else
                        <div id="map"></div>
                    @endif
                </div>
            </div>
        </section>
    </main>

    {{-- Stub element so index_alt.min.js does not throw when optional sliders are absent --}}
    <ul class="feedback_slider" hidden aria-hidden="true" style="display:none!important">
        <li class="feedback_slider-slide"><div class="feedback_slider-slide_wrapper"></div></li>
    </ul>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/js/index_alt.min.js') }}"></script>
    <script src="{{ asset('frontend/js/board-slider.js') }}"></script>
    <script>
        window.PROJECT_MAP_DATA = @json($projectMap['markers'] ?? []);
        window.PROJECT_MAP_CONFIG = @json($projectMap['config'] ?? [
            'filterMode' => 'category',
            'leafletBasePath' => asset('frontend/img/leaflet'),
        ]);
    </script>
    <script src="{{ asset('frontend/js/leaflet.min.js') }}"></script>
    <script src="{{ asset('frontend/js/indonesia-map.js') }}"></script>
    <script src="{{ asset('frontend/js/company-journey.js') }}"></script>
    <script src="{{ asset('frontend/js/certificate-gallery.js') }}?v={{ @filemtime(public_path('frontend/js/certificate-gallery.js')) ?: time() }}"></script>
    <script src="{{ asset('frontend/js/instagram-widget.js') }}?v={{ @filemtime(public_path('frontend/js/instagram-widget.js')) ?: time() }}"></script>
@endpush
