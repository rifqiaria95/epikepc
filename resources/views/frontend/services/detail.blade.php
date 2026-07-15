@extends('layouts.frontend.main')

@section('title', ($service->title ?? 'Service Details') . ' | EPIKEPC')
@section('page', 'services')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/single-service.min.css') }}" />
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Services',
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
        <section class="services section-nopb">
            <div class="container">
                <ul class="services_list row g-0">
                    <li class="services_list-item col-12 col-md-6 col-xl-4" data-aos="fade-up">
                        <div class="services_header section_header">
                            <span class="subtitle" data-aos="fade-down">What we do</span>
                            <h2 class="title">
                                Other
                                <span class="highlight">Services</span>
                            </h2>
                            <p class="text" data-aos="fade-in" data-aos-duration="500" data-aos-delay="150">
                                Elementum sagittis vitae et leo duis ut diam. In nibh mauris cursus mattis molestie
                            </p>
                            <ul class="services_header-list">
                                <li class="services_header-list_item d-flex align-items-center" data-aos="fade-up">
                                    <i class="icon-check icon"></i>
                                    Discovering possibility in concrete
                                </li>
                                <li class="services_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="100">
                                    <i class="icon-check icon"></i>
                                    Sed id semper risus in hendrerit
                                </li>
                                <li class="services_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="150">
                                    <i class="icon-check icon"></i>
                                    Nulla pellentesque dignissim
                                </li>
                            </ul>
                        </div>
                    </li>
                    @foreach ($sidebarServices as $index => $related)
                        <li class="services_list-item col-12 col-md-6 col-xl-4" data-aos="fade-up">
                            <div class="wrapper d-flex flex-column align-items-start justify-content-between">
                                <span class="number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <h4 class="title">{{ $related->title }}</h4>
                                <p class="description">{{ Str::limit(strip_tags($related->description), 100) }}</p>
                                <a class="link link-arrow" href="{{ route('frontend.detail-service', $related->id) }}">
                                    Details
                                    <i class="icon-arrow_right"></i>
                                </a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>
        <section class="reviews section-nopb">
            <div class="container">
                <div class="reviews_header section_header">
                    <span class="subtitle"> Our reviews </span>
                    <h2 class="title">
                        What
                        <span class="highlight">Our Clients</span>
                        Say
                    </h2>
                </div>
                <div class="wrapper--slider">
                    <ul class="reviews_slider">
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
                                    Curabitur vitae nunc sed velit dignissim sodales ut. Maecenas ultricies mi eget mauris pharetra et.
                                    Et ligula ullamcorper malesuada proin pellentesque diam volutpat commodo
                                </p>
                                <div class="author d-flex align-items-center">
                                    <picture>
                                        <source
                                            data-srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                            srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                            type="image/webp"
                                        />
                                        <img
                                            class="avatar lazy"
                                            data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                                            src="{{ asset('frontend/img/placeholder.jpg') }}"
                                            alt="Vera Robinson"
                                        />
                                    </picture>
                                    <div class="wrapper">
                                        <span class="name">Vera Robinson</span>
                                        <span class="profession">Psychologist</span>
                                    </div>
                                </div>
                            </div>
                        </li>
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
                                    Commodo quis imperdiet massa tincidunt nunc. Volutpat odio facilisis mauris sit amet. Mauris commodo
                                    quis imperdiet massa tincidunt
                                </p>
                                <div class="author d-flex align-items-center">
                                    <picture>
                                        <source
                                            data-srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                            srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                            type="image/webp"
                                        />
                                        <img
                                            class="avatar lazy"
                                            data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                                            src="{{ asset('frontend/img/placeholder.jpg') }}"
                                            alt="Benjamin Norris"
                                        />
                                    </picture>
                                    <div class="wrapper">
                                        <span class="name">Benjamin Norris</span>
                                        <span class="profession">Business analyst</span>
                                    </div>
                                </div>
                            </div>
                        </li>
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
                                    Curabitur vitae nunc sed velit dignissim sodales ut. Maecenas ultricies mi eget mauris pharetra et.
                                    Et ligula ullamcorper malesuada proin pellentesque diam volutpat commodo
                                </p>
                                <div class="author d-flex align-items-center">
                                    <picture>
                                        <source
                                            data-srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                            srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                            type="image/webp"
                                        />
                                        <img
                                            class="avatar lazy"
                                            data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                                            src="{{ asset('frontend/img/placeholder.jpg') }}"
                                            alt="Lisa Smith"
                                        />
                                    </picture>
                                    <div class="wrapper">
                                        <span class="name">Lisa Smith</span>
                                        <span class="profession">Manager</span>
                                    </div>
                                </div>
                            </div>
                        </li>
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
                                    Commodo quis imperdiet massa tincidunt nunc. Volutpat odio facilisis mauris sit amet. Mauris commodo
                                    quis imperdiet massa tincidunt
                                </p>
                                <div class="author d-flex align-items-center">
                                    <picture>
                                        <source
                                            data-srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                            srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                            type="image/webp"
                                        />
                                        <img
                                            class="avatar lazy"
                                            data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                                            src="{{ asset('frontend/img/placeholder.jpg') }}"
                                            alt="John Doe"
                                        />
                                    </picture>
                                    <div class="wrapper">
                                        <span class="name">John Doe</span>
                                        <span class="profession">Analyst</span>
                                    </div>
                                </div>
                            </div>
                        </li>
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
                        Porttitor rhoncus dolor purus non enim praesent elementum facilisis. Nisi scelerisque eu ultrices vitae auctor
                        eu augue ut lectus
                    </p>
                    <div class="wrapper">
                        <a class="btn" href="{{ route('frontend.contact.index') }}">Contact Us</a>
                    </div>
                </div>
                <div class="accordion faq_accordion col-12 col-lg-12 col-xl-auto">
                    <div class="faq_accordion accordion-wrapper">
                        <button
                            class="faq_accordion-trigger accordion-trigger"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseOne"
                            aria-expanded="false"
                            aria-controls="collapseOne"
                        >
                            <span class="question">What is a Structural Engineer?</span>
                            <span class="faq_accordion-trigger_icon accordion-trigger_icon icon-plus"></span>
                        </button>
                        <div id="collapseOne" class="faq_accordion-content accordion-content collapse">
                            <p class="text">
                                Condimentum id venenatis a condimentum vitae sapien pellentesque habitant. Non quam lacus suspendisse
                                faucibus interdum posuere lorem. Ut diam quam nulla porttitor massa id neque aliquam vestibulum. Mattis
                                rhoncus urna neque viverra justo nec ultrices dui sapien
                            </p>
                        </div>
                    </div>
                    <div class="faq_accordion accordion-wrapper expanded">
                        <button
                            class="faq_accordion-trigger accordion-trigger"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo"
                            aria-expanded="true"
                            aria-controls="collapseTwo"
                        >
                            <span class="question">What are the Service Provided by Company?</span>
                            <span class="faq_accordion-trigger_icon accordion-trigger_icon icon-minus"></span>
                        </button>
                        <div id="collapseTwo" class="faq_accordion-content accordion-content collapse show">
                            <p class="text">
                                Condimentum id venenatis a condimentum vitae sapien pellentesque habitant. Non quam lacus suspendisse
                                faucibus interdum posuere lorem. Ut diam quam nulla porttitor massa id neque aliquam vestibulum. Mattis
                                rhoncus urna neque viverra justo nec ultrices dui sapien. Ut diam quam nulla porttitor massa id neque
                                aliquam vestibulum. Mattis rhoncus urna neque viverra justo nec ultrices dui sapien
                            </p>
                        </div>
                    </div>
                    <div class="faq_accordion accordion-wrapper">
                        <button
                            class="faq_accordion-trigger accordion-trigger"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseThree"
                            aria-expanded="false"
                            aria-controls="collapseThree"
                        >
                            <span class="question">Where is Company Located?</span>
                            <span class="faq_accordion-trigger_icon accordion-trigger_icon icon-plus"></span>
                        </button>
                        <div id="collapseThree" class="faq_accordion-content accordion-content collapse">
                            <p class="text">
                                Condimentum id venenatis a condimentum vitae sapien pellentesque habitant. Non quam lacus suspendisse
                                faucibus interdum posuere lorem. Ut diam quam nulla porttitor.
                            </p>
                        </div>
                    </div>
                    <div class="faq_accordion accordion-wrapper">
                        <button
                            class="faq_accordion-trigger accordion-trigger"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapseFour"
                            aria-expanded="false"
                            aria-controls="collapseFour"
                        >
                            <span class="question">How Long Does It Take to Build a House?</span>
                            <span class="faq_accordion-trigger_icon accordion-trigger_icon icon-plus"></span>
                        </button>
                        <div id="collapseFour" class="faq_accordion-content accordion-content collapse">
                            <p class="text">
                                Condimentum id venenatis a condimentum vitae sapien pellentesque habitant. Non quam lacus suspendisse
                                faucibus interdum posuere lorem. Ut diam quam nulla porttitor massa id neque aliquam vestibulum. Mattis
                                rhoncus urna neque viverra justo nec ultrices dui sapien. Ut diam quam nulla porttitor massa
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <!-- SINGLE SERVICE CONTENT END -->
@endsection

@push('scripts')
    <script src="{{ asset('frontend/js/singleservice.min.js') }}"></script>
@endpush
