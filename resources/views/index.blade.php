@extends('layouts.frontend.main')

@section('title', 'Home | EPIKEPC')
@section('page', 'home')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/index2.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/board-slider.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/indonesia-map.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/css/company-journey.css') }}" />
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

        {{-- ===== BOARD OF DIRECTORS ===== --}}
        <section class="board-directors section" aria-labelledby="board-directors-title">
            <div class="container">
                <div class="board-directors__header" data-aos="fade-up">
                    <span class="section_header">
                        <span class="subtitle">Leadership Vision</span>
                    </span>
                    <h2 class="title" id="board-directors-title">
                        Board of <span class="highlight">Directors</span>
                    </h2>
                    <p class="text">
                        Meet the leaders who guide our vision, uphold our values, and drive sustainable growth across every project we undertake.
                    </p>
                </div>

                <div class="board-directors__slider-wrap" data-board-slider data-aos="fade-up" data-aos-delay="100" tabindex="0" role="region" aria-label="Board of Directors portrait slider">
                    <div class="board-directors__viewport" data-board-viewport>
                        <div class="board-directors__track" data-board-track>
                            @if ($teamMembers->isNotEmpty())
                                @foreach ($teamMembers as $member)
                                    <div class="board-directors__slide" data-board-slide>
                                        <article class="board-directors__card">
                                            <div class="board-directors__card-media">
                                                <img src="{{ $member->image_url ?: asset('frontend/img/img-1.png') }}" alt="{{ $member->nama }}" width="400" height="533" loading="lazy" decoding="async" />
                                                <div class="board-directors__card-overlay">
                                                    <h3 class="board-directors__card-name">{{ $member->nama }}</h3>
                                                    <p class="board-directors__card-position">{{ $member->jabatan }}</p>
                                                </div>
                                            </div>
                                        </article>
                                    </div>
                                @endforeach
                            @else
                                {{-- Fallback: exact Axial template static slides --}}
                                <div class="board-directors__slide" data-board-slide>
                                    <article class="board-directors__card">
                                        <div class="board-directors__card-media">
                                            <img src="{{ asset('frontend/img/img-1.png') }}" alt="Jonathan R. Mitchell" width="400" height="533" loading="lazy" decoding="async" />
                                            <div class="board-directors__card-overlay">
                                                <h3 class="board-directors__card-name">Jonathan R. Mitchell</h3>
                                                <p class="board-directors__card-position">Chief Executive Officer</p>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                <div class="board-directors__slide" data-board-slide>
                                    <article class="board-directors__card">
                                        <div class="board-directors__card-media">
                                            <img src="{{ asset('frontend/img/img-1.png') }}" alt="Benjamin Miller" width="400" height="533" loading="lazy" decoding="async" />
                                            <div class="board-directors__card-overlay">
                                                <h3 class="board-directors__card-name">Benjamin Miller</h3>
                                                <p class="board-directors__card-position">Chief Engineering Officer</p>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                <div class="board-directors__slide" data-board-slide>
                                    <article class="board-directors__card">
                                        <div class="board-directors__card-media">
                                            <img src="{{ asset('frontend/img/img-1.png') }}" alt="Stephanie Ramirez" width="400" height="533" loading="lazy" decoding="async" />
                                            <div class="board-directors__card-overlay">
                                                <h3 class="board-directors__card-name">Stephanie Ramirez</h3>
                                                <p class="board-directors__card-position">Board Director</p>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                <div class="board-directors__slide" data-board-slide>
                                    <article class="board-directors__card">
                                        <div class="board-directors__card-media">
                                            <img src="{{ asset('frontend/img/img-1.png') }}" alt="David Chen" width="400" height="533" loading="lazy" decoding="async" />
                                            <div class="board-directors__card-overlay">
                                                <h3 class="board-directors__card-name">David Chen</h3>
                                                <p class="board-directors__card-position">Chief Financial Officer</p>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                <div class="board-directors__slide" data-board-slide>
                                    <article class="board-directors__card">
                                        <div class="board-directors__card-media">
                                            <img src="{{ asset('frontend/img/img-1.png') }}" alt="Sarah Thompson" width="400" height="533" loading="lazy" decoding="async" />
                                            <div class="board-directors__card-overlay">
                                                <h3 class="board-directors__card-name">Sarah Thompson</h3>
                                                <p class="board-directors__card-position">Independent Director</p>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                                <div class="board-directors__slide" data-board-slide>
                                    <article class="board-directors__card">
                                        <div class="board-directors__card-media">
                                            <img src="{{ asset('frontend/img/img-1.png') }}" alt="Michael Okafor" width="400" height="533" loading="lazy" decoding="async" />
                                            <div class="board-directors__card-overlay">
                                                <h3 class="board-directors__card-name">Michael Okafor</h3>
                                                <p class="board-directors__card-position">Board Director</p>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="board-directors__controls">
                        <button class="board-directors__arrow board-directors__arrow--prev" type="button" data-board-prev aria-label="Previous directors">
                            <i class="icon icon-arrow_left" aria-hidden="true"></i>
                        </button>
                        <div class="board-directors__dots" data-board-dots role="tablist" aria-label="Slider pagination"></div>
                        <button class="board-directors__arrow board-directors__arrow--next" type="button" data-board-next aria-label="Next directors">
                            <i class="icon icon-arrow_right" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
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
                            <div id="indonesia-leaflet-map" style="width:100%;height:100%;min-height:320px;border-radius:16px;"></div>
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
                            <span class="c-journey__tl-eyebrow">{{ $companyJourney->timeline_subtitle ?? 'Company History' }}</span>
                            <h3 class="c-journey__tl-title">{{ $companyJourney->timeline_title ?? 'Our Milestones' }}</h3>
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
                                    <span class="c-journey__card-badge">{{ $milestone->year }}</span>
                                    <h4 class="c-journey__card-title">{{ $milestone->title }}</h4>
                                    <p class="c-journey__card-text">{{ $milestone->description }}</p>
                                </div>
                                <div class="c-journey__stem" aria-hidden="true"></div>
                                <div class="c-journey__dot" aria-hidden="true"></div>
                                <span class="c-journey__year">{{ $milestone->year }}</span>
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

        {{-- ===== NEWS & MEDIA ===== --}}
        <section class="news-media section" style="padding: 90px 0 90px 0;">

            <div class="container">
                <!-- Section Header -->
                <div class="row align-items-end mb-5">
                    <div class="col-12 col-md-7" data-aos="fade-up">
                        <span class="section_header" style="display:block;">
                            <span class="subtitle">Stay Informed</span>
                        </span>
                        <h2 style="font-family: Archivo, sans-serif; font-size: clamp(1.875rem, 4vw, 2.875rem); font-weight: 700; color: #000810; line-height: 1.2; margin: 0;">
                            News &amp; <span class="highlight">Media</span>
                        </h2>
                    </div>
                    <div class="col-12 col-md-5 mt-3 mt-md-0" data-aos="fade-up" data-aos-delay="100">
                        <p style="color: #666; font-size: 0.9375rem; line-height: 1.75; margin: 0;">
                            Latest updates, project showcases, and industry insights from our team around the world.
                        </p>
                    </div>
                </div>

                <!-- VIDEO SLIDER -->
                <div class="nm-slider" data-aos="fade-up" data-aos-delay="120">
                    <div class="nm-track" id="nmTrack">

                        <!-- Slide 1 -->
                        <div class="nm-slide nm-slide--active">
                            <div class="nm-video-box">
                                <img src="{{ asset('frontend/img/img-1.png') }}" class="nm-thumb" alt="Grand Meridian Tower Construction">
                                <button class="nm-play" data-src="https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1" onclick="nmPlay(this)">
                                    <svg width="20" height="23" viewBox="0 0 20 23" fill="none"><path d="M1.5 1.5l17 9.5-17 9.5V1.5z" fill="#000810" stroke="#000810" stroke-width="1.5" stroke-linejoin="round"/></svg>
                                </button>
                                <iframe class="nm-iframe" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                                <div class="nm-info-bar">
                                    <span class="nm-badge nm-badge--blue">Project Showcase</span>
                                    <h3 class="nm-video-title">Grand Meridian Tower — Construction Teame-lapse</h3>
                                    <p class="nm-video-meta">Jakarta &middot; 2023</p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 2 -->
                        <div class="nm-slide">
                            <div class="nm-video-box">
                                <img src="{{ asset('frontend/img/img-1.png') }}" class="nm-thumb" alt="Engineering Summit 2024">
                                <button class="nm-play" data-src="https://www.youtube.com/embed/ysz5S6PUM-U?autoplay=1" onclick="nmPlay(this)">
                                    <svg width="20" height="23" viewBox="0 0 20 23" fill="none"><path d="M1.5 1.5l17 9.5-17 9.5V1.5z" fill="#000810" stroke="#000810" stroke-width="1.5" stroke-linejoin="round"/></svg>
                                </button>
                                <iframe class="nm-iframe" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                                <div class="nm-info-bar">
                                    <span class="nm-badge nm-badge--yellow">Company News</span>
                                    <h3 class="nm-video-title">Annual Engineering Summit 2024 Highlights</h3>
                                    <p class="nm-video-meta">Bali &middot; June 2024</p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 3 -->
                        <div class="nm-slide">
                            <div class="nm-video-box">
                                <img src="{{ asset('frontend/img/img-1.png') }}" class="nm-thumb" alt="Harbor Bridge Expansion">
                                <button class="nm-play" data-src="https://www.youtube.com/embed/ZbZSe6N_BXs?autoplay=1" onclick="nmPlay(this)">
                                    <svg width="20" height="23" viewBox="0 0 20 23" fill="none"><path d="M1.5 1.5l17 9.5-17 9.5V1.5z" fill="#000810" stroke="#000810" stroke-width="1.5" stroke-linejoin="round"/></svg>
                                </button>
                                <iframe class="nm-iframe" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                                <div class="nm-info-bar">
                                    <span class="nm-badge nm-badge--blue">Infrastructure</span>
                                    <h3 class="nm-video-title">Harbor Bridge Expansion — Behind the Scenes</h3>
                                    <p class="nm-video-meta">Surabaya &middot; 2022</p>
                                </div>
                            </div>
                        </div>

                        <!-- Slide 4 -->
                        <div class="nm-slide">
                            <div class="nm-video-box">
                                <img src="{{ asset('frontend/img/img-1.png') }}" class="nm-thumb" alt="Green Tech Campus">
                                <button class="nm-play" data-src="https://www.youtube.com/embed/LXb3EKWsInQ?autoplay=1" onclick="nmPlay(this)">
                                    <svg width="20" height="23" viewBox="0 0 20 23" fill="none"><path d="M1.5 1.5l17 9.5-17 9.5V1.5z" fill="#000810" stroke="#000810" stroke-width="1.5" stroke-linejoin="round"/></svg>
                                </button>
                                <iframe class="nm-iframe" allowfullscreen allow="autoplay; encrypted-media"></iframe>
                                <div class="nm-info-bar">
                                    <span class="nm-badge nm-badge--yellow">Sustainability</span>
                                    <h3 class="nm-video-title">Green Tech Campus — LEED Platinum Journey</h3>
                                    <p class="nm-video-meta">Tangerang &middot; 2024</p>
                                </div>
                            </div>
                        </div>

                    </div><!-- /nm-track -->

                    <!-- Slider Controls -->
                    <div class="nm-controls">
                        <div class="nm-arrows">
                            <button class="nm-arrow nm-arrow--prev" onclick="nmShift(-1)" aria-label="Previous video">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M11 4l-5 5 5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                            <button class="nm-arrow nm-arrow--next" onclick="nmShift(1)" aria-label="Next video">
                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M7 4l5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </button>
                        </div>
                        <div class="nm-dots" id="nmDots"></div>
                        <span class="nm-counter" id="nmCounter">1 / 4</span>
                    </div>
                </div>
                <!-- /VIDEO SLIDER -->

                <!-- NEWS CARDS -->
                <div class="row g-4 mt-5">
                    @forelse ($news as $index => $item)
                        @php
                            $delays = [0, 80, 160];
                            $delay  = $delays[$index % 3];
                        @endphp
                        <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $delay }}">
                            <article class="nm-news-card">
                                <div class="nm-news-card__thumb">
                                    <img src="{{ $item->thumbnail_url ?: asset('frontend/img/img-1.png') }}" alt="{{ $item->title }}">
                                    <span class="nm-news-card__cat">News</span>
                                </div>
                                <div class="nm-news-card__body">
                                    <p class="nm-news-card__date">
                                        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" style="margin-right:5px;vertical-align:-2px;"><rect x="1" y="3" width="14" height="12" rx="2" stroke="#253C74" stroke-width="1.5"/><path d="M5 1v4M11 1v4M1 7h14" stroke="#253C74" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        {{ $item->published_at ? \Carbon\Carbon::parse($item->published_at)->format('F j, Y') : '' }}
                                    </p>
                                    <h4 class="nm-news-card__title">{{ $item->title }}</h4>
                                    <p class="nm-news-card__excerpt">{{ Str::limit(strip_tags($item->excerpt), 160) }}</p>
                                    <a href="{{ route('frontend.news.index') }}" class="nm-news-card__link">
                                        Read More
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @empty
                        <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="0">
                            <article class="nm-news-card">
                                <div class="nm-news-card__thumb">
                                    <img src="{{ asset('frontend/img/img-1.png') }}" alt="Company News">
                                    <span class="nm-news-card__cat">Company News</span>
                                </div>
                                <div class="nm-news-card__body">
                                    <p class="nm-news-card__date">
                                        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" style="margin-right:5px;vertical-align:-2px;"><rect x="1" y="3" width="14" height="12" rx="2" stroke="#253C74" stroke-width="1.5"/><path d="M5 1v4M11 1v4M1 7h14" stroke="#253C74" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        June 18, 2026
                                    </p>
                                    <h4 class="nm-news-card__title">We Are Expanding — New Regional Office Opens in Singapore</h4>
                                    <p class="nm-news-card__excerpt">Our Southeast Asia expansion continues with the inauguration of our Singapore hub, supporting clients across the ASEAN region.</p>
                                    <a href="{{ route('frontend.news.index') }}" class="nm-news-card__link">
                                        Read More
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                </div>
                            </article>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="80">
                            <article class="nm-news-card">
                                <div class="nm-news-card__thumb">
                                    <img src="{{ asset('frontend/img/img-1.png') }}" alt="ISO Certification">
                                    <span class="nm-news-card__cat nm-news-card__cat--yellow">Achievement</span>
                                </div>
                                <div class="nm-news-card__body">
                                    <p class="nm-news-card__date">
                                        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" style="margin-right:5px;vertical-align:-2px;"><rect x="1" y="3" width="14" height="12" rx="2" stroke="#253C74" stroke-width="1.5"/><path d="M5 1v4M11 1v4M1 7h14" stroke="#253C74" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        May 30, 2026
                                    </p>
                                    <h4 class="nm-news-card__title">EPIKEPC Achieves ISO 9001:2015 Recertification</h4>
                                    <p class="nm-news-card__excerpt">Following a rigorous audit process, we have successfully renewed our ISO 9001:2015 certification — reaffirming our commitment to quality.</p>
                                    <a href="{{ route('frontend.news.index') }}" class="nm-news-card__link">
                                        Read More
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                </div>
                            </article>
                        </div>
                        <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up" data-aos-delay="160">
                            <article class="nm-news-card">
                                <div class="nm-news-card__thumb">
                                    <img src="{{ asset('frontend/img/img-1.png') }}" alt="Sustainability Report">
                                    <span class="nm-news-card__cat">Sustainability</span>
                                </div>
                                <div class="nm-news-card__body">
                                    <p class="nm-news-card__date">
                                        <svg width="13" height="13" viewBox="0 0 16 16" fill="none" style="margin-right:5px;vertical-align:-2px;"><rect x="1" y="3" width="14" height="12" rx="2" stroke="#253C74" stroke-width="1.5"/><path d="M5 1v4M11 1v4M1 7h14" stroke="#253C74" stroke-width="1.5" stroke-linecap="round"/></svg>
                                        May 12, 2026
                                    </p>
                                    <h4 class="nm-news-card__title">Our 2025 Sustainability Report: Net Zero by 2035</h4>
                                    <p class="nm-news-card__excerpt">We published our annual sustainability report outlining concrete steps toward carbon neutrality, including a 40% reduction in operational emissions achieved in 2025.</p>
                                    <a href="{{ route('frontend.news.index') }}" class="nm-news-card__link">
                                        Read More
                                        <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M3 8h10M9 4l4 4-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </a>
                                </div>
                            </article>
                        </div>
                    @endforelse
                </div>
                <!-- /NEWS CARDS -->

                <!-- CTA -->
                <div class="text-center mt-5" data-aos="fade-up">
                    <a href="{{ route('frontend.news.index') }}" style="display:inline-block; font-family:Archivo,sans-serif; font-weight:700; font-size:0.9375rem; padding:16px 40px; background:#253C74; color:#fff; border-radius:4px; text-decoration:none; letter-spacing:0.02em; transition: background .2s;">View All News</a>
                </div>

            </div><!-- /container -->

            <style>
                /* VIDEO SLIDER */
                .nm-slider { position: relative; }

                .nm-track { position: relative; }

                .nm-slide { display: none; }
                .nm-slide--active { display: block; }

                .nm-video-box {
                    position: relative;
                    aspect-ratio: 16 / 9;
                    background: #000810;
                    border-radius: 14px;
                    overflow: hidden;
                    box-shadow: 0 20px 60px rgba(2,72,193,0.18);
                }

                .nm-thumb {
                    width: 100%; height: 100%;
                    object-fit: cover; display: block;
                    transition: transform .5s ease, opacity .3s;
                }
                .nm-video-box:hover .nm-thumb { transform: scale(1.03); }

                .nm-play {
                    position: absolute; top: 50%; left: 50%;
                    transform: translate(-50%,-50%);
                    width: 76px; height: 76px;
                    border-radius: 50%;
                    background: rgba(255,198,49,0.96);
                    border: none; cursor: pointer;
                    display: flex; align-items: center; justify-content: center;
                    transition: transform .25s, background .2s;
                    z-index: 3;
                    box-shadow: 0 6px 24px rgba(0,0,0,0.3);
                }
                .nm-play:hover { transform: translate(-50%,-50%) scale(1.12); background: #ffd464; }
                .nm-play svg { margin-left: 3px; }

                .nm-iframe {
                    display: none;
                    position: absolute; inset: 0;
                    width: 100%; height: 100%;
                    border: none; z-index: 2;
                }

                .nm-info-bar {
                    position: absolute; bottom: 0; left: 0; right: 0;
                    padding: 28px 28px 24px;
                    background: linear-gradient(to top, rgba(0,8,16,0.88) 0%, rgba(0,8,16,0.4) 60%, transparent 100%);
                    transition: opacity .3s;
                    z-index: 1;
                }

                .nm-badge {
                    display: inline-block;
                    font-family: Archivo, sans-serif;
                    font-size: 0.7rem; font-weight: 700;
                    padding: 4px 12px; border-radius: 100px;
                    margin-bottom: 10px;
                    text-transform: uppercase; letter-spacing: 0.06em;
                }
                .nm-badge--blue { background: #253C74; color: #fff; }
                .nm-badge--yellow { background: #FFdf08; color: #000810; }

                .nm-video-title {
                    font-family: Archivo, sans-serif;
                    font-size: clamp(1rem, 2.5vw, 1.5rem);
                    font-weight: 700; color: #fff;
                    margin: 0 0 6px; line-height: 1.3;
                }
                .nm-video-meta {
                    font-size: 0.8125rem; color: rgba(255,255,255,0.6);
                    margin: 0;
                }

                /* Controls */
                .nm-controls {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 16px;
                    margin-top: 22px;
                    flex-wrap: wrap;
                }

                .nm-arrows { display: flex; gap: 10px; }

                .nm-arrow {
                    width: 44px; height: 44px;
                    border-radius: 50%;
                    border: 1.5px solid rgba(2,72,193,0.25);
                    background: #fff;
                    color: #253C74;
                    display: flex; align-items: center; justify-content: center;
                    cursor: pointer;
                    transition: background .2s, border-color .2s, color .2s;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
                }
                .nm-arrow:hover { background: #253C74; border-color: #253C74; color: #fff; }

                .nm-dots { display: flex; gap: 8px; align-items: center; }

                .nm-dot {
                    width: 8px; height: 8px;
                    border-radius: 50%;
                    background: #c8c8c8;
                    border: none; cursor: pointer;
                    padding: 0;
                    transition: background .25s, transform .25s, width .25s;
                }
                .nm-dot--active {
                    background: #253C74;
                    width: 24px;
                    border-radius: 100px;
                    transform: none;
                }

                .nm-counter {
                    font-family: Archivo, sans-serif;
                    font-size: 0.875rem;
                    font-weight: 600;
                    color: #666;
                    min-width: 40px;
                    text-align: right;
                }

                /* NEWS CARDS */
                .nm-news-card {
                    background: #fff;
                    border-radius: 12px;
                    overflow: hidden;
                    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
                    height: 100%;
                    display: flex; flex-direction: column;
                    transition: box-shadow .3s, transform .3s;
                }
                .nm-news-card:hover {
                    box-shadow: 0 12px 40px rgba(2,72,193,0.15);
                    transform: translateY(-4px);
                }

                .nm-news-card__thumb {
                    position: relative;
                    overflow: hidden;
                    height: 200px;
                }
                .nm-news-card__thumb img {
                    width: 100%; height: 100%;
                    object-fit: cover; display: block;
                    transition: transform .5s ease;
                }
                .nm-news-card:hover .nm-news-card__thumb img { transform: scale(1.06); }

                .nm-news-card__cat {
                    position: absolute; top: 14px; left: 14px;
                    background: #253C74; color: #fff;
                    font-family: Archivo, sans-serif;
                    font-size: 0.68rem; font-weight: 700;
                    padding: 4px 10px; border-radius: 100px;
                    text-transform: uppercase; letter-spacing: 0.06em;
                }
                .nm-news-card__cat--yellow { background: #FFdf08; color: #000810; }

                .nm-news-card__body {
                    padding: 22px 22px 24px;
                    display: flex; flex-direction: column;
                    flex: 1;
                }

                .nm-news-card__date {
                    font-size: 0.8rem; color: #253C74;
                    font-weight: 500; margin: 0 0 10px;
                }

                .nm-news-card__title {
                    font-family: Archivo, sans-serif;
                    font-size: 1rem; font-weight: 700;
                    color: #000810; line-height: 1.4;
                    margin: 0 0 12px;
                }

                .nm-news-card__excerpt {
                    font-size: 0.875rem; color: #666;
                    line-height: 1.7; margin: 0 0 18px;
                    flex: 1;
                }

                .nm-news-card__link {
                    display: inline-flex; align-items: center; gap: 6px;
                    font-family: Archivo, sans-serif;
                    font-size: 0.875rem; font-weight: 700;
                    color: #253C74; text-decoration: none;
                    transition: gap .2s, color .2s;
                    margin-top: auto;
                }
                .nm-news-card__link:hover { gap: 10px; color: #013494; }

                /* View All News button hover */
                .news-media a[href]:last-of-type:hover { background: #013494 !important; }

                /* Responsive */
                @media (max-width: 575.98px) {
                    .nm-controls { justify-content: center; }
                    .nm-counter { display: none; }
                }
            </style>

            <script>
                (function () {
                    var current = 0;
                    var slides = document.querySelectorAll('#nmTrack .nm-slide');
                    var total = slides.length;
                    var dotsWrap = document.getElementById('nmDots');
                    var counter = document.getElementById('nmCounter');

                    /* Build dots */
                    for (var i = 0; i < total; i++) {
                        var d = document.createElement('button');
                        d.className = 'nm-dot' + (i === 0 ? ' nm-dot--active' : '');
                        d.setAttribute('aria-label', 'Go to slide ' + (i + 1));
                        d.setAttribute('data-idx', i);
                        d.addEventListener('click', (function(idx){ return function(){ nmGo(idx); }; })(i));
                        dotsWrap.appendChild(d);
                    }

                    function nmGo(idx) {
                        /* Stop video on current slide */
                        var curIframe = slides[current].querySelector('.nm-iframe');
                        var curThumb  = slides[current].querySelector('.nm-thumb');
                        var curPlay   = slides[current].querySelector('.nm-play');
                        curIframe.src = '';
                        curIframe.style.display = 'none';
                        curThumb.style.opacity = '1';
                        curPlay.style.display = 'flex';

                        slides[current].classList.remove('nm-slide--active');
                        dotsWrap.children[current].classList.remove('nm-dot--active');

                        current = (idx + total) % total;

                        slides[current].classList.add('nm-slide--active');
                        dotsWrap.children[current].classList.add('nm-dot--active');
                        counter.textContent = (current + 1) + ' / ' + total;
                    }

                    /* Expose to global for arrow buttons */
                    window.nmShift = function(dir) { nmGo(current + dir); };

                    /* Play video on click */
                    window.nmPlay = function(btn) {
                        var box    = btn.closest('.nm-video-box');
                        var iframe = box.querySelector('.nm-iframe');
                        var thumb  = box.querySelector('.nm-thumb');
                        iframe.src = btn.getAttribute('data-src');
                        iframe.style.display = 'block';
                        thumb.style.opacity  = '0';
                        btn.style.display    = 'none';
                    };

                    /* Swipe support */
                    var startX = 0;
                    var track = document.getElementById('nmTrack');
                    track.addEventListener('touchstart', function(e){ startX = e.touches[0].clientX; }, {passive:true});
                    track.addEventListener('touchend', function(e){
                        var diff = startX - e.changedTouches[0].clientX;
                        if (Math.abs(diff) > 40) nmGo(current + (diff > 0 ? 1 : -1));
                    }, {passive:true});

                    /* Auto-advance every 6 s (pauses on interaction) */
                    var autoTeamer = setInterval(function(){ nmGo(current + 1); }, 6000);
                    document.querySelector('.nm-slider').addEventListener('click', function(){
                        clearInterval(autoTeamer);
                    });
                })();
            </script>

        </section>

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
                            <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer">
                                <i class="icon-facebook"></i>
                            </a>
                        </li>
                        <li class="socials_item">
                            <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer">
                                <i class="icon-instagram"></i>
                            </a>
                        </li>
                        <li class="socials_item">
                            <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer">
                                <i class="icon-twitter"></i>
                            </a>
                        </li>
                        <li class="socials_item">
                            <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer">
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
@endsection

@push('scripts')
    <script src="{{ asset('frontend/js/index_alt.min.js') }}"></script>
    <script src="{{ asset('frontend/js/board-slider.js') }}"></script>
    <script src="{{ asset('frontend/js/leaflet.min.js') }}"></script>
    <script src="{{ asset('frontend/js/indonesia-map.js') }}"></script>
    <script src="{{ asset('frontend/js/company-journey.js') }}"></script>
@endpush
