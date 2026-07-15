@extends('layouts.frontend.main')

@section('title', 'Services | EPIKEPC')
@section('page', 'services')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/services.min.css') }}" />
@endpush

@section('header_extension')
    <div class="header_extension">
        <div class="container">
            <div class="section_header">
                <span class="subtitle subtitle--extended">Building communities</span>
                <h1 class="title">Services</h1>
                <ul class="breadcrumbs d-flex align-items-center">
                    <li class="breadcrumbs_item">
                        <a href="{{ route('frontend.about.index') }}">Home</a>
                    </li>
                    <li class="breadcrumbs_item breadcrumbs_item--current">
                        <span>Services</span>
                    </li>
                </ul>
            </div>
        </div>
        <picture>
            <source data-srcset="{{ asset('frontend/img/placeholder.jpg') }}" srcset="{{ asset('frontend/img/placeholder.jpg') }}" type="image/webp" data-role="deco" />
            <img class="lazy plan" data-src="{{ asset('frontend/img/placeholder.jpg') }}" src="{{ asset('frontend/img/placeholder.jpg') }}" alt="media" data-role="deco" />
        </picture>
    </div>
@endsection

@section('content')
    <main>
        {{-- ===== SERVICES LIST ===== --}}
        <section class="services section">
            <div class="container">
                <div class="services_header section_header">
                    <span class="subtitle">What we do</span>
                    <h2 class="title" data-aos="fade-right" data-aos-duration="500">Services</h2>
                </div>
                <ul class="services_list row g-0">
                    @forelse ($services as $index => $service)
                        <li class="services_list-item col-12 col-md-6 col-xxl-4" data-aos="fade-up" data-order="{{ $index + 1 }}">
                            <div class="wrapper d-flex flex-column align-items-start justify-content-between">
                                <span class="number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <h4 class="title">{{ $service->title }}</h4>
                                <p class="description">{{ Str::limit(strip_tags($service->description), 150) }}</p>
                                <a class="link link-arrow" href="{{ route('frontend.detail-service', $service->id) }}">
                                    Details
                                    <i class="icon-arrow_right"></i>
                                </a>
                            </div>
                        </li>
                    @empty
                        <li class="services_list-item col-12">
                            <div class="wrapper">
                                <p>No services available.</p>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </section>

        {{-- ===== NUMBERS ===== --}}
        <section class="numbers primary-bg section">
            <div class="container">
                <div class="row g-3 g-lg-4 justify-content-between align-items-end">
                    <div class="numbers_header section_header col-lg-6 col-xl-5">
                        <span class="subtitle">What we do</span>
                        <h2 class="title">
                            Building The Future on a
                            <span class="highlight">Foundation of Excellence</span>
                        </h2>
                        <p class="text">
                            Risus commodo viverra maecenas accumsan lacus vel. Semper viverra nam libero justo laoreet sit amet cursus.
                            Commodo ullamcorper a lacus vestibulum sed arcu non odio euismod. Adipiscing elit duis tristique
                            sollicitudin nibh sit
                        </p>
                    </div>
                    <ul class="numbers_list d-flex flex-wrap justify-content-center col-lg-6 col-xl-6">
                        <li class="numbers_list-item d-flex flex-column align-items-start col-12 col-sm-6" data-order="1">
                            <h2 class="countNum number" data-suffix="%" data-value="99">0</h2>
                            <span class="label">Building Control <br />Approval Rate</span>
                        </li>
                        <li class="numbers_list-item d-flex flex-column align-items-start col-12 col-sm-6" data-order="2">
                            <h2 class="countNum number" data-suffix="+" data-value="320">0</h2>
                            <span class="label">Active Projects in <br />Construction Management </span>
                        </li>
                        <li class="numbers_list-item d-flex flex-column align-items-start col-12 col-sm-6" data-order="3">
                            <h2 class="countNum number" data-suffix="+" data-value="200">0</h2>
                            <span class="label">Completed Projects <br />Every Year</span>
                        </li>
                        <li class="numbers_list-item d-flex flex-column align-items-start col-12 col-sm-6" data-order="4">
                            <h2 class="countNum number" data-separator="." data-value="4253">0</h2>
                            <span class="label">Million euros Turnover <br />in 2020</span>
                        </li>
                    </ul>
                    <div class="numbers_video col-12" data-aos="zoom-in" data-aos-duration="600" data-aos-once="true">
                        <picture>
                            <source data-srcset="{{ asset('frontend/img/placeholder.jpg') }}" srcset="{{ asset('frontend/img/placeholder.jpg') }}" />
                            <img
                                class="numbers_video-thumb lazy"
                                data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                                src="{{ asset('frontend/img/placeholder.jpg') }}"
                                alt="thumbnail"
                            />
                        </picture>
                        <a class="btn-play d-inline-flex align-items-center justify-content-center" href="#">
                            <i class="icon-play"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== ADVANTAGES ===== --}}
        <section class="advantages section-nopb">
            <div class="container d-flex flex-wrap flex-xl-nowrap align-items-end align-items-xl-center justify-content-center">
                <div class="advantages_header section_header col-xl-auto">
                    <span class="subtitle" data-aos="fade-down">The choice is your time is ours</span>
                    <h2 class="title" data-aos="fade-right">
                        Solving Problems With the Help
                        <span class="highlight">of Experts</span>
                    </h2>
                    <p class="text" data-aos="fade-up" data-aos-delay="50">
                        Nibh praesent tristique magna sit amet purus gravida quis. Bibendum arcu vitae elementum curabitur vitae nulla
                        facilisi. Quam vulputate dignissim suspendisse in
                    </p>
                    <ul class="advantages_header-list">
                        <li class="advantages_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="100">
                            <svg
                                class="advantages-icon"
                                width="40"
                                height="40"
                                viewBox="0 0 40 40"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <g clip-path="url(#clip01)">
                                    <path
                                        d="M36.4062 6.5625L21.4062 0.3125C20.9375 0.15625 20.4688 0.078125 20 0.078125C19.4531 0.078125 18.9844 0.15625 18.5156 0.3125L3.51562 6.5625C2.10938 7.1875 1.25 8.51562 1.25 10C1.25 25.5469 10.1562 36.25 18.5156 39.7656C19.4531 40.1562 20.4688 40.1562 21.4062 39.7656C28.125 36.9531 38.75 27.3438 38.75 10C38.75 8.51562 37.8125 7.1875 36.4062 6.5625ZM20.4688 37.4219C20.1562 37.5781 19.7656 37.5781 19.4531 37.4219C11.875 34.375 3.75 23.75 3.75 10C3.75 9.53125 3.98438 9.0625 4.45312 8.90625L19.4531 2.65625C19.7656 2.5 20.1562 2.5 20.4688 2.65625L35.4688 8.90625C35.9375 9.0625 36.25 9.53125 36.1719 10C36.25 23.75 28.125 34.375 20.4688 37.4219ZM31.0938 12.0312C30.7031 11.7188 30.1562 11.7188 29.7656 12.0312L17.0312 24.6875L11.6406 19.2969C11.25 18.9062 10.625 18.9062 10.3125 19.2969L9.60938 19.9219C9.21875 20.3125 9.21875 20.8594 9.60938 21.25L16.3281 28.0469C16.7188 28.3594 17.2656 28.3594 17.6562 28.0469L31.7188 14.0625C32.1094 13.6719 32.1094 13.125 31.7188 12.7344L31.0938 12.0312Z"
                                        fill="#FFdf08"
                                    />
                                </g>
                                <defs>
                                    <clipPath id="clip01">
                                        <rect width="40" height="40" fill="white" />
                                    </clipPath>
                                </defs>
                            </svg>
                            <span class="label"> Successfully Completed Projects </span>
                        </li>
                        <li class="advantages_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="150">
                            <svg
                                class="advantages-icon"
                                width="41"
                                height="40"
                                viewBox="0 0 41 40"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <g clip-path="url(#clip02)">
                                    <path
                                        d="M39.4688 28.8281L29.8594 19.2188L33.7656 15.3125L39.0781 10C40.5625 8.51562 40.5625 6.17188 39.0781 4.6875L35.5625 1.17188C34.8594 0.390625 33.9219 0 32.9062 0C31.9688 0 31.0312 0.390625 30.25 1.17188L24.9375 6.48438L21.0312 10.3906L11.4219 0.78125C10.9531 0.3125 10.3281 0 9.70312 0C9.07812 0 8.45312 0.3125 7.98438 0.78125L0.953125 7.73438C-0.0625 8.75 -0.0625 10.3125 0.953125 11.25L10.5625 20.8594L1.73438 29.6875L0.25 38.125C0.015625 39.2969 1.10938 40.2344 2.125 40L10.5625 38.5156L19.3906 29.6875L29 39.2969C29.3906 39.7656 30.0156 40 30.7188 40C31.3438 40 31.9688 39.7656 32.4375 39.2969L39.4688 32.2656C40.4844 31.3281 40.4844 29.7656 39.4688 28.8281Z"
                                        fill="#FFdf08"
                                    />
                                </g>
                                <defs>
                                    <clipPath id="clip02">
                                        <rect width="40" height="40" fill="white" transform="translate(0.25)" />
                                    </clipPath>
                                </defs>
                            </svg>
                            <span class="label"> Simplicity In Design With Practically In Mind </span>
                        </li>
                        <li class="advantages_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="200">
                            <svg
                                class="advantages-icon"
                                width="41"
                                height="40"
                                viewBox="0 0 41 40"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <g clip-path="url(#clip04)">
                                    <path
                                        d="M37.75 27.5V25C37.75 17.1094 32.4375 10.4688 25.25 8.35938V7.5C25.25 6.17188 24.0781 5 22.75 5H17.75C16.3438 5 15.25 6.17188 15.25 7.5V8.35938C7.98438 10.4688 2.75 17.1094 2.75 25V27.5C1.34375 27.5 0.25 28.6719 0.25 30V32.5C0.25 33.9062 1.34375 35 2.75 35H37.75C39.0781 35 40.25 33.9062 40.25 32.5V30C40.25 28.6719 39.0781 27.5 37.75 27.5ZM5.25 25C5.25 18.9062 9 13.6719 14.3125 11.4062L16.3438 19.5312C16.4219 19.8438 16.6562 20 17.0469 20C17.3594 20 17.6719 19.6875 17.75 19.2969V7.5H22.75V19.2969C22.75 19.6875 23.0625 20 23.375 20C23.7656 20 24 19.8438 24.0781 19.5312L26.1094 11.4062C31.4219 13.6719 35.25 18.9062 35.25 25V27.5H5.25V25ZM37.75 32.5H2.75V30H37.75V32.5Z"
                                        fill="#FFdf08"
                                    />
                                </g>
                                <defs>
                                    <clipPath id="clip04">
                                        <rect width="40" height="40" fill="white" transform="translate(0.25)" />
                                    </clipPath>
                                </defs>
                            </svg>
                            <span class="label"> High Level of Construction Control Approval </span>
                        </li>
                    </ul>
                </div>
                <div class="advantages_img col-xl-auto" data-aos="zoom-in" data-aos-duration="600">
                    <picture>
                        <source
                            data-srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                            srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                            type="image/webp"
                        />
                        <img
                            class="advantages_img-img"
                            data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                            src="{{ asset('frontend/img/placeholder.jpg') }}"
                            alt="media"
                        />
                    </picture>
                </div>
            </div>
        </section>

        {{-- ===== SKILLS ===== --}}
        <section class="skills section">
            <div class="container d-flex flex-wrap flex-xl-nowrap align-items-center justify-content-md-center justify-content-xl-start">
                <div class="wrapper wrapper--skills col-xl-auto">
                    <div class="skills_header section_header">
                        <span class="subtitle" data-aos="fade-down">We build only quality things</span>
                        <h2 class="title" data-aos="fade-right">
                            Trusted Quality for Over
                            <span class="highlight">20 Years</span>
                        </h2>
                        <p class="text" data-aos="fade-up" data-aos-delay="50">
                            Convallis a cras semper auctor neque vitae. Lectus proin nibh nisl condimentum fermentum odio eu. Augue
                            neque gravida in fermentum et sollicitudin
                        </p>
                    </div>
                    <ul class="skills_list">
                        <li class="skills_list-item d-flex flex-column flex-sm-row flex-wrap align-items-sm-center justify-content-between">
                            <span class="label" data-aos="zoom-in" data-aos-duration="400">Industry</span>
                            <span class="progressLine" id="industry" data-value="90" data-fill="#FFdf08"></span>
                        </li>
                        <li class="skills_list-item d-flex flex-column flex-sm-row flex-wrap align-items-sm-center justify-content-between">
                            <span class="label" data-aos="zoom-in" data-aos-duration="400">Engineering</span>
                            <span class="progressLine" id="engineering" data-value="96" data-fill="#FFdf08"></span>
                        </li>
                        <li class="skills_list-item d-flex flex-column flex-sm-row flex-wrap align-items-sm-center justify-content-between">
                            <span class="label" data-aos="zoom-in" data-aos-duration="400">Factory</span>
                            <span class="progressLine" id="factory" data-value="89" data-fill="#FFdf08"></span>
                        </li>
                        <li class="skills_list-item d-flex flex-column flex-sm-row flex-wrap align-items-sm-center justify-content-between">
                            <span class="label" data-aos="zoom-in" data-aos-duration="400">Construction</span>
                            <span class="progressLine" id="construction" data-value="99" data-fill="#FFdf08"></span>
                        </li>
                    </ul>
                </div>
                <div class="skills_img col-xl-auto" data-aos="zoom-in" data-aos-duration="600">
                    <picture>
                        <source
                            data-srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                            srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                            type="image/webp"
                        />
                        <img
                            class="skills_img-img"
                            data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                            src="{{ asset('frontend/img/placeholder.jpg') }}"
                            alt="media"
                        />
                    </picture>
                </div>
            </div>
        </section>

        {{-- ===== GALLERY ===== --}}
        <section class="gallery">
            <div class="container-fluid p-0">
                <ul class="gallery_list d-flex flex-wrap">
                    <li class="gallery_list-item col-12 col-sm-6 col-xl-3">
                        <a
                            class="gallery_list-item_trigger"
                            href="{{ asset('frontend/img/placeholder.jpg') }}"
                            data-caption="Fresh Concept Construction Renovation"
                            data-role="gallery-link"
                        >
                            <div class="img-wrapper">
                                <picture>
                                    <source
                                        data-srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                        srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="lazy"
                                        data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                                        src="{{ asset('frontend/img/placeholder.jpg') }}"
                                        alt="Fresh Concept Construction Renovation"
                                    />
                                </picture>
                            </div>
                            <div class="text-wrapper d-flex flex-column justify-content-end">
                                <span class="subtitle">Our gallery</span>
                                <h4 class="title">Fresh Concept Construction Renovation</h4>
                                <span class="label">Special Projects</span>
                            </div>
                        </a>
                    </li>
                    <li class="gallery_list-item col-12 col-sm-6 col-xl-3">
                        <a
                            class="gallery_list-item_trigger"
                            href="{{ asset('frontend/img/placeholder.jpg') }}"
                            data-caption="Fresh Concept Construction Renovation"
                            data-role="gallery-link"
                        >
                            <div class="img-wrapper">
                                <picture>
                                    <source
                                        data-srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                        srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="lazy"
                                        data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                                        src="{{ asset('frontend/img/placeholder.jpg') }}"
                                        alt="Fresh Concept Construction Renovation"
                                    />
                                </picture>
                            </div>
                            <div class="text-wrapper d-flex flex-column justify-content-end">
                                <span class="subtitle">Our gallery</span>
                                <h4 class="title">Fresh Concept Construction Renovation</h4>
                                <span class="label">Special Projects</span>
                            </div>
                        </a>
                    </li>
                    <li class="gallery_list-item col-12 col-sm-6 col-xl-3">
                        <a
                            class="gallery_list-item_trigger"
                            href="{{ asset('frontend/img/placeholder.jpg') }}"
                            data-caption="Fresh Concept Construction Renovation"
                            data-role="gallery-link"
                        >
                            <div class="img-wrapper">
                                <picture>
                                    <source
                                        data-srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                        srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="lazy"
                                        data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                                        src="{{ asset('frontend/img/placeholder.jpg') }}"
                                        alt="Fresh Concept Construction Renovation"
                                    />
                                </picture>
                            </div>
                            <div class="text-wrapper d-flex flex-column justify-content-end">
                                <span class="subtitle">Our gallery</span>
                                <h4 class="title">Fresh Concept Construction Renovation</h4>
                                <span class="label">Special Projects</span>
                            </div>
                        </a>
                    </li>
                    <li class="gallery_list-item col-12 col-sm-6 col-xl-3">
                        <a
                            class="gallery_list-item_trigger"
                            href="{{ asset('frontend/img/placeholder.jpg') }}"
                            data-caption="Fresh Concept Construction Renovation"
                            data-role="gallery-link"
                        >
                            <div class="img-wrapper">
                                <picture>
                                    <source
                                        data-srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                        srcset="{{ asset('frontend/img/placeholder.jpg') }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="lazy"
                                        data-src="{{ asset('frontend/img/placeholder.jpg') }}"
                                        src="{{ asset('frontend/img/placeholder.jpg') }}"
                                        alt="Fresh Concept Construction Renovation"
                                    />
                                </picture>
                            </div>
                            <div class="text-wrapper d-flex flex-column justify-content-end">
                                <span class="subtitle">Our gallery</span>
                                <h4 class="title">Fresh Concept Construction Renovation</h4>
                                <span class="label">Special Projects</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </section>

        {{-- ===== CONTACT ===== --}}
        <section class="contact section">
            <div class="container d-flex flex-wrap align-items-end justify-content-lg-between justify-content-xl-start">
                <div class="contact_form col-lg-6">
                    <div class="contact_form-header section_header">
                        <span class="subtitle">Contact us</span>
                        <h2 class="title">
                            Do You Have any
                            <span class="highlight">Questions?</span>
                        </h2>
                    </div>
                    <form
                        action="#"
                        class="contact_form-form contact-form d-flex flex-wrap justify-content-between"
                        method="POST"
                        name="feedbackForm"
                        data-type="feedback"
                    >
                        <input
                            class="contact-form_field contact-form_field--half field required"
                            name="feedbackName"
                            id="feedbackName"
                            type="text"
                            placeholder="Full name"
                        />
                        <input
                            class="contact-form_field contact-form_field--half field required"
                            data-type="tel"
                            type="text"
                            name="feedbackTel"
                            id="feedbackTel"
                            placeholder="Phone"
                        />
                        <input
                            class="contact-form_field field required"
                            data-type="email"
                            type="text"
                            name="feedbackEmail"
                            id="feedbackEmail"
                            placeholder="Email Address"
                        />
                        <textarea
                            class="contact-form_field field required"
                            data-type="message"
                            name="feedbackMessage"
                            id="feedbackMessage"
                            placeholder="Message"
                        ></textarea>
                        <button type="submit" class="contact-form_btn btn">Send message</button>
                    </form>
                </div>
                <div class="contact_info">
                    <h3 class="contact_info-header">Are You Going to Implement Project?</h3>
                    <ul class="contact-info">
                        <li class="contact-info_group">
                            <span class="name">Address</span>
                            <span class="content">2047 Cyrus Viaduct East Jadynchester</span>
                        </li>
                        <li class="contact-info_group">
                            <span class="name">Email</span>
                            <span class="content d-inline-flex flex-column">
                                <a class="link" href="mailto:info@epikepc.com">info@epikepc.com</a>
                                <a class="link" href="mailto:support@construct.com">support@construct.com</a>
                            </span>
                        </li>
                        <li class="contact-info_group">
                            <span class="name">Phone</span>
                            <span class="content d-inline-flex flex-column">
                                <a class="link" href="tel:+13136453395">1 - 313 - 645 - 3395</a>
                                <a class="link" href="tel:+14699702609">1 - 469 - 970 - 2609</a>
                            </span>
                        </li>
                    </ul>
                    <ul class="socials d-flex align-items-center justify-content-start">
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
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/js/services.min.js') }}"></script>
@endpush
