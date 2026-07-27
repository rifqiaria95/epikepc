@extends('layouts.frontend.main')

@section('title', ($service->title ?? 'Service Details') . ' | EPIKEPC')
@section('page', 'services')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/single-service.min.css') }}" />
    <style>
        /* Keep Consult Now full-width on desktop (same as tablet) */
        .tabs_services-content .content .text-wrapper .main {
            width: 100%;
        }

        .tabs_services-content .content .text-wrapper .main_btn {
            width: 100%;
            max-width: none;
            margin: 20px 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        @media screen and (min-width: 991.98px) {
            .tabs_services-content .content .text-wrapper .main_btn {
                width: 100%;
                max-width: none;
                margin: 20px 0 0;
            }
        }

        .epc-services {
            --epc-pad-y: clamp(4rem, 8vw, 7.5rem);
            --epc-bg-image: none;
            position: relative;
            padding: var(--epc-pad-y) 0;
            color: #fff;
            min-height: clamp(520px, 72vh, 760px);
            display: flex;
            align-items: center;
        }

        /* Clip window for classic fixed-background parallax */
        .epc-services__media {
            position: absolute;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }

        .epc-services__media-bg {
            position: absolute;
            inset: -30% 0;
            height: auto;
            min-height: 160%;
            background-image: var(--epc-bg-image);
            background-position: center center;
            background-size: cover;
            background-repeat: no-repeat;
            will-change: transform;
            backface-visibility: hidden;
            transform: translate3d(0, 0, 0);
        }

        .epc-services__overlay {
            position: absolute;
            inset: 0;
            z-index: 1;
            background:
                linear-gradient(115deg, rgba(0, 8, 16, 0.88) 0%, rgba(37, 60, 116, 0.72) 48%, rgba(0, 8, 16, 0.55) 100%),
                radial-gradient(circle at 85% 20%, rgba(255, 223, 8, 0.18), transparent 40%);
            pointer-events: none;
        }

        .epc-services .container {
            position: relative;
            z-index: 2;
            width: 100%;
        }

        .epc-services__layout {
            display: grid;
            grid-template-columns: minmax(0, 0.9fr) minmax(0, 1.1fr);
            gap: clamp(1.5rem, 4vw, 3.5rem);
            align-items: start;
        }

        .epc-services__eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 14px;
            padding: 6px 12px;
            border-radius: 999px;
            border: 1px solid rgba(255, 223, 8, 0.35);
            background: rgba(255, 223, 8, 0.12);
            color: #FFdf08;
            font-family: Archivo, sans-serif;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .epc-services__title {
            font-family: Archivo, sans-serif;
            font-size: clamp(2.25rem, 5vw, 4rem);
            font-weight: 700;
            letter-spacing: 0.02em;
            line-height: 1.05;
            text-transform: uppercase;
            margin: 0 0 16px;
            color: #fff;
        }

        .epc-services__subtitle {
            font-size: clamp(0.95rem, 1.5vw, 1.0625rem);
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.82);
            margin: 0 0 28px;
            max-width: 34rem;
        }

        .epc-services__meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .epc-services__meta-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.8125rem;
            font-weight: 600;
            backdrop-filter: blur(8px);
        }

        .epc-services__meta-chip span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #FFdf08;
            flex-shrink: 0;
        }

        .epc-services__panel {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 24px;
            padding: clamp(1rem, 2.5vw, 1.5rem);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.28);
        }

        .epc-services__grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .epc-services__item {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: center;
            gap: 12px;
            min-height: 64px;
            padding: 12px 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.92);
            color: #000810;
            text-align: left;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.12);
            transition: transform .25s ease, box-shadow .25s ease, background .25s ease;
        }

        .epc-services__item:hover {
            transform: translateY(-3px);
            background: #fff;
            box-shadow: 0 16px 36px rgba(37, 60, 116, 0.22);
        }

        .epc-services__index {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #253C74;
            color: #fff;
            font-family: Archivo, sans-serif;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            flex-shrink: 0;
        }

        .epc-services__label {
            font-family: Archivo, sans-serif;
            font-size: clamp(0.78rem, 1.2vw, 0.875rem);
            font-weight: 650;
            line-height: 1.35;
            margin: 0;
        }

        @media (max-width: 991.98px) {
            .epc-services__layout {
                grid-template-columns: 1fr;
                gap: 1.75rem;
            }

            .epc-services__panel {
                border-radius: 20px;
            }
        }

        @media (max-width: 767.98px) {
            .epc-services {
                min-height: auto;
                align-items: stretch;
            }

            .epc-services__grid {
                grid-template-columns: 1fr;
            }

            .epc-services__item {
                min-height: 56px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .epc-services__item {
                transition: none !important;
                transform: none !important;
            }

            .epc-services__media-bg {
                transform: none !important;
                inset: 0;
                min-height: 100%;
            }
        }
    </style>
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Building communities',
        'title'    => $service->title,
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Services', 'url' => route('frontend.services.index')],
            ['label' => $service->title],
        ],
    ])
@endsection

@section('content')
    @php
        $subServices = method_exists($service, 'getSubServices')
            ? $service->getSubServices()
            : ($service->subServices ?? collect());
        $subServices = $subServices ?? collect();
    @endphp
    <!-- SINGLE SERVICE CONTENT START -->
    <main>
        <section class="tabs section">
            <div class="container">
                <div class="tabs_header section_header d-flex flex-wrap flex-lg-nowrap align-items-lg-end justify-content-xl-between">
                    <div class="tabs_header-wrapper">
                        <span class="subtitle" data-aos="fade-down">{{ $service->title }}</span>
                        <h2 class="title" data-aos="fade-right">
                            We Provide
                            <span class="highlight">{{ $service->title }}</span>
                        </h2>
                    </div>
                    <p class="text" data-aos="fade-left">
                        {{ Str::limit(strip_tags($service->subtitle ?: $service->description), 200) }}
                    </p>
                </div>
                <div class="tabs_services d-md-flex">
                    @if ($subServices && count($subServices) > 0)
                        <div class="tabs_services-triggers d-md-flex flex-column">
                            @foreach ($subServices as $i => $sub)
                                <h4 class="tabs_services-triggers_trigger d-flex align-items-center {{ $i === 0 ? 'active' : '' }}"
                                    data-id="{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}">
                                    {{ $sub['title'] ?? ($sub->title ?? 'Sub-services ' . ($i + 1)) }}
                                </h4>
                            @endforeach
                        </div>
                        <div class="tabs_services-content">
                            @foreach ($subServices as $i => $sub)
                                @php $subId = str_pad($i + 1, 2, '0', STR_PAD_LEFT); @endphp
                                <div class="content {{ $i === 0 ? 'active' : '' }}" id="{{ $subId }}">
                                    <div class="img-wrapper">
                                        <picture>
                                            <source
                                                data-srcset="{{ ($sub['image_url'] ?? $sub->image_url ?? null) ?: ($service->detail_image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                                srcset="{{ ($sub['image_url'] ?? $sub->image_url ?? null) ?: ($service->detail_image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                                type="image/webp"
                                            />
                                            <img
                                                class="lazy"
                                                data-src="{{ ($sub['image_url'] ?? $sub->image_url ?? null) ?: ($service->detail_image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                                src="{{ ($sub['image_url'] ?? $sub->image_url ?? null) ?: ($service->detail_image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                                alt="{{ $sub['title'] ?? $sub->title ?? 'Sub-services' }}"
                                            />
                                        </picture>
                                    </div>
                                    <div class="text-wrapper d-flex flex-column">
                                        <div class="main d-sm-flex flex-md-column flex-lg-row align-items-center justify-content-between">
                                            <a class="main_btn btn" href="{{ route('frontend.contact.index') }}">Consultation</a>
                                        </div>
                                        <div class="description">
                                            <p class="text">{{ $sub['description'] ?? ($sub->description ?? '') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="tabs_services-content" style="width:100%">
                            <div class="content active" id="01">
                                <div class="img-wrapper">
                                    <picture>
                                        <source
                                            data-srcset="{{ $service->detail_image_url ?: ($service->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                            srcset="{{ $service->detail_image_url ?: ($service->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                            type="image/webp"
                                        />
                                        <img
                                            class="lazy"
                                            data-src="{{ $service->detail_image_url ?: ($service->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                            src="{{ $service->detail_image_url ?: ($service->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                            alt="{{ $service->title }}"
                                        />
                                    </picture>
                                </div>
                                <div class="text-wrapper d-flex flex-column">
                                    <div class="main d-sm-flex flex-md-column flex-lg-row align-items-center justify-content-between">
                                        <a class="main_btn btn" href="{{ route('frontend.contact.index') }}">Consult Now</a>
                                    </div>
                                    <div class="description">
                                        {!! $service->description !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
        <section class="process section primary-bg">
            <div class="container d-flex flex-wrap justify-content-between align-items-end">
                <div class="process_header section_header">
                    <span class="subtitle">Better process</span>
                    <h2 class="title">
                        The Process of Working
                        <span class="highlight"> with Us </span>
                    </h2>
                </div>
                <p class="process_text">
                    Dapibus ultrices in iaculis nunc sed augue lacus viverra vitae. Vehicula ipsum a arcu cursus vitae congue mauris.
                    Enim facilisis gravida neque convallis a cras
                </p>
            </div>
            <div class="container-fluid process_fluid p-0">
                <div class="container">
                    <ul class="process_steps progress-tracker progress-tracker--vertical">
                        <li class="process_steps-step progress-step">
                            <div class="progress-marker">
                                <span class="progress-marker_spot"></span>
                                <span class="progress-marker_spot--underlay"></span>
                            </div>
                            <div class="process_steps-step_wrapper">
                                <h4 class="title">Leave a request on the website</h4>
                                <p class="description">
                                    In arcu cursus euismod quis viverra nibh cras pulvinar mattis. Cras adipiscing enim eu turpis
                                </p>
                            </div>
                        </li>
                        <li class="process_steps-step progress-step">
                            <div class="progress-marker">
                                <span class="progress-marker_spot"></span>
                                <span class="progress-marker_spot--underlay"></span>
                            </div>
                            <div class="process_steps-step_wrapper">
                                <h4 class="title">Сalculation of the cost of the service</h4>
                                <p class="description">
                                    Habitant morbi tristique senectus et netus et malesuada fames. Cursus sit amet dictum
                                </p>
                            </div>
                        </li>
                        <li class="process_steps-step progress-step">
                            <div class="progress-marker">
                                <span class="progress-marker_spot"></span>
                                <span class="progress-marker_spot--underlay"></span>
                            </div>
                            <div class="process_steps-step_wrapper">
                                <h4 class="title">Signing of a contract</h4>
                                <p class="description">Etiam dignissim diam quis enim lobortis scelerisque fermentum dui faucibus</p>
                            </div>
                        </li>
                        <li class="process_steps-step progress-step">
                            <div class="progress-marker">
                                <span class="progress-marker_spot"></span>
                                <span class="progress-marker_spot--underlay"></span>
                            </div>
                            <div class="process_steps-step_wrapper">
                                <h4 class="title">Execution of works</h4>
                                <p class="description">
                                    Ridiculus mus mauris vitae ultricies. Imperdiet proin fermentum leo vel orci porta
                                </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
        <section
            class="epc-services section-nopb"
            data-epc-parallax
            style="--epc-bg-image: url('{{ $service->detail_image_url ?? $service->image_url ?? asset('frontend/img/img-1.png') }}')"
        >
            <div class="epc-services__media" aria-hidden="true">
                <div class="epc-services__media-bg" data-epc-parallax-bg></div>
            </div>
            <div class="epc-services__overlay" aria-hidden="true"></div>

            <div class="container">
                <div class="epc-services__layout">
                    <div class="epc-services__intro">
                        <span class="epc-services__eyebrow">Integrated EPC Scope</span>
                        <h2 class="epc-services__title">EPC Services</h2>
                        <p class="epc-services__subtitle">
                            PT EPIK provides EPC Service which includes end-to-end engineering, procurement, and construction capabilities for oil &amp; gas infrastructure.
                        </p>
                        <div class="epc-services__meta">
                            <div class="epc-services__meta-chip"><span></span>{{ count($epcServiceItems) }} Service Scopes</div>
                            <div class="epc-services__meta-chip"><span></span>Oil &amp; Gas Focused</div>
                        </div>
                    </div>

                    <div class="epc-services__panel">
                        <ul class="epc-services__grid">
                            @foreach ($epcServiceItems as $index => $item)
                                <li class="epc-services__item">
                                    <span class="epc-services__index">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <p class="epc-services__label">{{ $item }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <section class="reviews section-nopb">
            <div class="container">
                <div class="reviews_header section_header">
                    <span class="subtitle">Testimonials</span>
                    <h2 class="title">
                        What
                        <span class="highlight">Our Clients</span>
                        Say
                    </h2>
                </div>
                <div class="wrapper--slider">
                    <ul class="reviews_slider">
                        @forelse ($testimonials as $testimonial)
                            <li class="reviews_slider-slide">
                                <div class="reviews_slider-slide_wrapper d-flex flex-column justify-content-between align-items-start">
                                    <ul class="stars d-flex align-items-center">
                                        @for ($i = 0; $i < 5; $i++)
                                            <li class="stars_star"><i class="icon-star"></i></li>
                                        @endfor
                                    </ul>
                                    <p class="text">{{ $testimonial->testimoni }}</p>
                                    <div class="author d-flex align-items-center">
                                        <picture>
                                            <source
                                                data-srcset="{{ $testimonial->gambar_url }}"
                                                srcset="{{ $testimonial->gambar_url }}"
                                                type="image/webp"
                                            />
                                            <img
                                                class="avatar lazy"
                                                data-src="{{ $testimonial->gambar_url }}"
                                                src="{{ $testimonial->gambar_url }}"
                                                alt="{{ $testimonial->nama }}"
                                            />
                                        </picture>
                                        <div class="wrapper">
                                            <span class="name">{{ $testimonial->nama }}</span>
                                            <span class="profession">{{ $testimonial->instansi }}</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="reviews_slider-slide">
                                <div class="reviews_slider-slide_wrapper d-flex flex-column justify-content-between align-items-start">
                                    <ul class="stars d-flex align-items-center">
                                        <li class="stars_star"><i class="icon-star"></i></li>
                                        <li class="stars_star"><i class="icon-star"></i></li>
                                        <li class="stars_star"><i class="icon-star"></i></li>
                                        <li class="stars_star"><i class="icon-star"></i></li>
                                        <li class="stars_star"><i class="icon-star"></i></li>
                                    </ul>
                                    <p class="text">
                                        EPIK consistently delivers pipeline and gas infrastructure projects with strong discipline in safety, quality, and schedule.
                                    </p>
                                    <div class="author d-flex align-items-center">
                                        <picture>
                                            <source data-srcset="{{ asset('frontend/img/placeholder.jpg') }}" srcset="{{ asset('frontend/img/placeholder.jpg') }}" type="image/webp" />
                                            <img class="avatar lazy" data-src="{{ asset('frontend/img/placeholder.jpg') }}" src="{{ asset('frontend/img/placeholder.jpg') }}" alt="Client" />
                                        </picture>
                                        <div class="wrapper">
                                            <span class="name">Project Team PGN</span>
                                            <span class="profession">PT PGN</span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                    <div class="tns-arrows reviews_slider-controls">
                        <a class="tns-arrows_arrow tns-arrows_arrow--prev" href="#">
                            <i class="icon-arrow_left icon"></i>
                        </a>
                        <a class="tns-arrows_arrow tns-arrows_arrow--next" href="#">
                            <i class="icon-arrow_right icon"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <section class="faq section">
            <div
                class="container d-lg-flex flex-wrap flex-xl-nowrap justify-content-start align-items-start justify-content-xl-between"
            >
                <div class="faq_header section_header col-lg-12 col-xl-auto">
                    <span class="subtitle">Dealing with your worries</span>
                    <h2 class="title">
                        If Your Question Is Not Here
                        <span class="highlight">Contact Us</span>
                    </h2>
                    <p class="text">
                        Looking for clarity on EPC scope, project delivery, or how EPIK supports oil &amp; gas infrastructure works?
                        Browse common questions below, or reach our team for a project consultation.
                    </p>
                    <div class="wrapper">
                        <a class="btn" href="{{ route('frontend.contact.index') }}">Contact Us</a>
                    </div>
                </div>
                <div class="accordion faq_accordion col-12 col-lg-12 col-xl-auto">
                    @foreach ($serviceFaqs as $index => $faq)
                        @php
                            $collapseId = 'serviceFaq' . $index;
                            $isFirst = $index === 0;
                        @endphp
                        <div class="faq_accordion accordion-wrapper {{ $isFirst ? 'expanded' : '' }}">
                            <button
                                class="faq_accordion-trigger accordion-trigger"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#{{ $collapseId }}"
                                aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                aria-controls="{{ $collapseId }}"
                            >
                                <span class="question">{{ $faq['question'] }}</span>
                                <span class="faq_accordion-trigger_icon accordion-trigger_icon {{ $isFirst ? 'icon-minus' : 'icon-plus' }}"></span>
                            </button>
                            <div id="{{ $collapseId }}" class="faq_accordion-content accordion-content collapse {{ $isFirst ? 'show' : '' }}">
                                <p class="text">{{ $faq['answer'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>
    <!-- SINGLE SERVICE CONTENT END -->
@endsection

@push('scripts')
    <script src="{{ asset('frontend/js/singleservice.min.js') }}"></script>
    <script>
        (function () {
            var section = document.querySelector('[data-epc-parallax]');
            if (!section) return;

            var bg = section.querySelector('[data-epc-parallax-bg]');
            if (!bg) return;

            var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            var ticking = false;

            function travelAmount() {
                return window.innerWidth < 768 ? 120 : 220;
            }

            function updateParallax() {
                if (reduceMotion.matches) {
                    bg.style.transform = 'translate3d(0, 0, 0)';
                    ticking = false;
                    return;
                }

                var rect = section.getBoundingClientRect();
                var viewH = window.innerHeight || document.documentElement.clientHeight;
                var progress = (viewH - rect.top) / (viewH + rect.height);
                var clamped = Math.max(0, Math.min(1, progress));
                var offset = (clamped - 0.5) * travelAmount() * 2;

                bg.style.transform = 'translate3d(0, ' + offset.toFixed(2) + 'px, 0)';
                ticking = false;
            }

            function requestTick() {
                if (ticking) return;
                ticking = true;
                window.requestAnimationFrame(updateParallax);
            }

            updateParallax();
            window.addEventListener('scroll', requestTick, { passive: true });
            window.addEventListener('resize', requestTick, { passive: true });

            if (reduceMotion.addEventListener) {
                reduceMotion.addEventListener('change', requestTick);
            } else if (reduceMotion.addListener) {
                reduceMotion.addListener(requestTick);
            }
        })();
    </script>
@endpush

