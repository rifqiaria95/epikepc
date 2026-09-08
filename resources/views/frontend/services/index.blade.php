@extends('layouts.frontend.main')

@section('title', 'Services | EPIKEPC')
@section('page', 'services')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/services.min.css') }}" />
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'What we deliver',
        'title'    => 'Services',
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Services'],
        ],
    ])
@endsection

@section('content')
    <main>
        {{-- ===== SERVICES LIST ===== --}}
        <section class="services section">
            <div class="container">
                    <div class="services_header section_header">
                    <span class="subtitle">What we do</span>
                    <h2 class="title" data-aos="fade-right" data-aos-duration="500">
                        Integrated Services for
                        <span class="highlight">Energy Infrastructure</span>
                    </h2>
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
                        <span class="subtitle">Proven delivery</span>
                        <h2 class="title">
                            Building Energy Infrastructure on a
                            <span class="highlight">Foundation of Excellence</span>
                        </h2>
                        <p class="text">
                            PT Energi Persada Inti Konstruksi (EPIK) delivers EPC and O&amp;M services for oil &amp; gas
                            infrastructure across Indonesia — with disciplined HSE, schedule reliability, and ISO-certified quality systems.
                        </p>
                    </div>
                    <ul class="numbers_list d-flex flex-wrap justify-content-center col-lg-6 col-xl-6">
                        <li class="numbers_list-item d-flex flex-column align-items-start col-12 col-sm-6" data-order="1">
                            <h2 class="countNum number" data-suffix="+" data-value="30">0</h2>
                            <span class="label">Major EPC &amp; O&amp;M <br />Projects Delivered</span>
                        </li>
                        <li class="numbers_list-item d-flex flex-column align-items-start col-12 col-sm-6" data-order="2">
                            <h2 class="countNum number" data-suffix="" data-value="3">0</h2>
                            <span class="label">ISO Certifications <br />9001 · 14001 · 45001</span>
                        </li>
                        <li class="numbers_list-item d-flex flex-column align-items-start col-12 col-sm-6" data-order="3">
                            <h2 class="countNum number" data-suffix="+" data-value="10">0</h2>
                            <span class="label">Provinces Covered by <br />Project Delivery</span>
                        </li>
                        <li class="numbers_list-item d-flex flex-column align-items-start col-12 col-sm-6" data-order="4">
                            <h2 class="countNum number" data-suffix="+" data-value="15">0</h2>
                            <span class="label">Years Supporting <br />National Energy Projects</span>
                        </li>
                    </ul>
                    <div class="numbers_video col-12" data-aos="zoom-in" data-aos-duration="600" data-aos-once="true">
                        <picture>
                            <source data-srcset="{{ $featureImage }}" srcset="{{ $featureImage }}" />
                            <img
                                class="numbers_video-thumb lazy"
                                data-src="{{ $featureImage }}"
                                src="{{ $featureImage }}"
                                alt="EPIK project delivery"
                            />
                        </picture>
                        <a class="btn-play d-inline-flex align-items-center justify-content-center" href="{{ route('frontend.contact.index') }}" aria-label="Discuss your project with EPIK">
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
                    <span class="subtitle" data-aos="fade-down">Why partners choose EPIK</span>
                    <h2 class="title" data-aos="fade-right">
                        Solving Complex Energy Challenges with
                        <span class="highlight">Expert Execution</span>
                    </h2>
                    <p class="text" data-aos="fade-up" data-aos-delay="50">
                        From pipeline networks and metering stations to HDD crossings and LNG facilities, our teams combine
                        engineering depth with field discipline trusted by PGN, Pertamina, and national energy stakeholders.
                    </p>
                    <ul class="advantages_header-list">
                        <li class="advantages_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="100">
                            <svg class="advantages-icon" width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip01)">
                                    <path d="M36.4062 6.5625L21.4062 0.3125C20.9375 0.15625 20.4688 0.078125 20 0.078125C19.4531 0.078125 18.9844 0.15625 18.5156 0.3125L3.51562 6.5625C2.10938 7.1875 1.25 8.51562 1.25 10C1.25 25.5469 10.1562 36.25 18.5156 39.7656C19.4531 40.1562 20.4688 40.1562 21.4062 39.7656C28.125 36.9531 38.75 27.3438 38.75 10C38.75 8.51562 37.8125 7.1875 36.4062 6.5625ZM20.4688 37.4219C20.1562 37.5781 19.7656 37.5781 19.4531 37.4219C11.875 34.375 3.75 23.75 3.75 10C3.75 9.53125 3.98438 9.0625 4.45312 8.90625L19.4531 2.65625C19.7656 2.5 20.1562 2.5 20.4688 2.65625L35.4688 8.90625C35.9375 9.0625 36.25 9.53125 36.1719 10C36.25 23.75 28.125 34.375 20.4688 37.4219ZM31.0938 12.0312C30.7031 11.7188 30.1562 11.7188 29.7656 12.0312L17.0312 24.6875L11.6406 19.2969C11.25 18.9062 10.625 18.9062 10.3125 19.2969L9.60938 19.9219C9.21875 20.3125 9.21875 20.8594 9.60938 21.25L16.3281 28.0469C16.7188 28.3594 17.2656 28.3594 17.6562 28.0469L31.7188 14.0625C32.1094 13.6719 32.1094 13.125 31.7188 12.7344L31.0938 12.0312Z" fill="#FFdf08"/>
                                </g>
                                <defs><clipPath id="clip01"><rect width="40" height="40" fill="white"/></clipPath></defs>
                            </svg>
                            <span class="label">ISO-certified quality, environment, and safety systems</span>
                        </li>
                        <li class="advantages_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="150">
                            <svg class="advantages-icon" width="41" height="40" viewBox="0 0 41 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip02)">
                                    <path d="M39.4688 28.8281L29.8594 19.2188L33.7656 15.3125L39.0781 10C40.5625 8.51562 40.5625 6.17188 39.0781 4.6875L35.5625 1.17188C34.8594 0.390625 33.9219 0 32.9062 0C31.9688 0 31.0312 0.390625 30.25 1.17188L24.9375 6.48438L21.0312 10.3906L11.4219 0.78125C10.9531 0.3125 10.3281 0 9.70312 0C9.07812 0 8.45312 0.3125 7.98438 0.78125L0.953125 7.73438C-0.0625 8.75 -0.0625 10.3125 0.953125 11.25L10.5625 20.8594L1.73438 29.6875L0.25 38.125C0.015625 39.2969 1.10938 40.2344 2.125 40L10.5625 38.5156L19.3906 29.6875L29 39.2969C29.3906 39.7656 30.0156 40 30.7188 40C31.3438 40 31.9688 39.7656 32.4375 39.2969L39.4688 32.2656C40.4844 31.3281 40.4844 29.7656 39.4688 28.8281Z" fill="#FFdf08"/>
                                </g>
                                <defs><clipPath id="clip02"><rect width="40" height="40" fill="white" transform="translate(0.25)"/></clipPath></defs>
                            </svg>
                            <span class="label">End-to-end EPCIC capability from design to commissioning</span>
                        </li>
                        <li class="advantages_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="200">
                            <svg class="advantages-icon" width="41" height="40" viewBox="0 0 41 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <g clip-path="url(#clip04)">
                                    <path d="M37.75 27.5V25C37.75 17.1094 32.4375 10.4688 25.25 8.35938V7.5C25.25 6.17188 24.0781 5 22.75 5H17.75C16.3438 5 15.25 6.17188 15.25 7.5V8.35938C7.98438 10.4688 2.75 17.1094 2.75 25V27.5C1.34375 27.5 0.25 28.6719 0.25 30V32.5C0.25 33.9062 1.34375 35 2.75 35H37.75C39.0781 35 40.25 33.9062 40.25 32.5V30C40.25 28.6719 39.0781 27.5 37.75 27.5ZM5.25 25C5.25 18.9062 9 13.6719 14.3125 11.4062L16.3438 19.5312C16.4219 19.8438 16.6562 20 17.0469 20C17.3594 20 17.6719 19.6875 17.75 19.2969V7.5H22.75V19.2969C22.75 19.6875 23.0625 20 23.375 20C23.7656 20 24 19.8438 24.0781 19.5312L26.1094 11.4062C31.4219 13.6719 35.25 18.9062 35.25 25V27.5H5.25V25ZM37.75 32.5H2.75V30H37.75V32.5Z" fill="#FFdf08"/>
                                </g>
                                <defs><clipPath id="clip04"><rect width="40" height="40" fill="white" transform="translate(0.25)"/></clipPath></defs>
                            </svg>
                            <span class="label">Field-proven delivery for PGN, Pertamina, and ESDM programs</span>
                        </li>
                    </ul>
                </div>
                <div class="advantages_img col-xl-auto" data-aos="zoom-in" data-aos-duration="600">
                    <picture>
                        <source data-srcset="{{ $featureImage }}" srcset="{{ $featureImage }}" type="image/webp" />
                        <img class="advantages_img-img" data-src="{{ $featureImage }}" src="{{ $featureImage }}" alt="EPIK engineering excellence" />
                    </picture>
                </div>
            </div>
        </section>

        {{-- ===== SKILLS ===== --}}
        <section class="skills section">
            <div class="container d-flex flex-wrap flex-xl-nowrap align-items-center justify-content-md-center justify-content-xl-start">
                <div class="wrapper wrapper--skills col-xl-auto">
                    <div class="skills_header section_header">
                        <span class="subtitle" data-aos="fade-down">Capabilities that keep projects on track</span>
                        <h2 class="title" data-aos="fade-right">
                            Trusted Delivery Across
                            <span class="highlight">Energy Infrastructure</span>
                        </h2>
                        <p class="text" data-aos="fade-up" data-aos-delay="50">
                            Our multidisciplinary teams bring together engineering rigor, procurement reliability, and construction
                            excellence — so every EPIK project protects schedule, quality, and operational readiness.
                        </p>
                    </div>
                    <ul class="skills_list">
                        <li class="skills_list-item d-flex flex-column flex-sm-row flex-wrap align-items-sm-center justify-content-between">
                            <span class="label" data-aos="zoom-in" data-aos-duration="400">Engineering &amp; Design</span>
                            <span class="progressLine" id="industry" data-value="94" data-fill="#FFdf08"></span>
                        </li>
                        <li class="skills_list-item d-flex flex-column flex-sm-row flex-wrap align-items-sm-center justify-content-between">
                            <span class="label" data-aos="zoom-in" data-aos-duration="400">Procurement</span>
                            <span class="progressLine" id="engineering" data-value="92" data-fill="#FFdf08"></span>
                        </li>
                        <li class="skills_list-item d-flex flex-column flex-sm-row flex-wrap align-items-sm-center justify-content-between">
                            <span class="label" data-aos="zoom-in" data-aos-duration="400">Construction</span>
                            <span class="progressLine" id="factory" data-value="96" data-fill="#FFdf08"></span>
                        </li>
                        <li class="skills_list-item d-flex flex-column flex-sm-row flex-wrap align-items-sm-center justify-content-between">
                            <span class="label" data-aos="zoom-in" data-aos-duration="400">HSE &amp; Quality</span>
                            <span class="progressLine" id="construction" data-value="98" data-fill="#FFdf08"></span>
                        </li>
                    </ul>
                </div>
                <div class="skills_img col-xl-auto" data-aos="zoom-in" data-aos-duration="600">
                    <picture>
                        <source data-srcset="{{ $skillsImage }}" srcset="{{ $skillsImage }}" type="image/webp" />
                        <img class="skills_img-img" data-src="{{ $skillsImage }}" src="{{ $skillsImage }}" alt="EPIK construction capability" />
                    </picture>
                </div>
            </div>
        </section>

        {{-- ===== GALLERY ===== --}}
        <section class="gallery">
            <div class="container-fluid p-0">
                <ul class="gallery_list d-flex flex-wrap">
                    @forelse ($galleryItems as $item)
                        <li class="gallery_list-item col-12 col-sm-6 col-xl-3">
                            <a
                                class="gallery_list-item_trigger"
                                href="{{ $item['url'] }}"
                                data-caption="{{ $item['caption'] }}"
                                data-role="gallery-link"
                            >
                                <div class="img-wrapper">
                                    <picture>
                                        <source data-srcset="{{ $item['url'] }}" srcset="{{ $item['url'] }}" type="image/webp" />
                                        <img
                                            class="lazy"
                                            data-src="{{ $item['url'] }}"
                                            src="{{ $item['url'] }}"
                                            alt="{{ $item['alt'] }}"
                                        />
                                    </picture>
                                </div>
                                <div class="text-wrapper d-flex flex-column justify-content-end">
                                    <span class="subtitle">Our gallery</span>
                                    <h4 class="title">{{ $item['title'] }}</h4>
                                    <span class="label">{{ $item['label'] }}</span>
                                </div>
                            </a>
                        </li>
                    @empty
                        <li class="gallery_list-item col-12 text-center py-5">
                            <p class="mb-0" style="color:#666;">Service gallery images will appear here once services have uploaded photos.</p>
                        </li>
                    @endforelse
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
                            Ready to Discuss Your
                            <span class="highlight">Next Project?</span>
                        </h2>
                    </div>
                    <form
                        action="{{ route('frontend.contact.store') }}"
                        class="contact_form-form contact-form d-flex flex-wrap justify-content-between"
                        method="POST"
                        name="feedbackForm"
                        data-type="feedback"
                    >
                        @csrf
                        <input type="hidden" name="subject" value="Services inquiry" />
                        <input
                            class="contact-form_field contact-form_field--half field required"
                            name="name"
                            id="feedbackName"
                            type="text"
                            placeholder="Full name"
                            required
                        />
                        <input
                            class="contact-form_field contact-form_field--half field required"
                            data-type="tel"
                            type="text"
                            name="phone"
                            id="feedbackTel"
                            placeholder="Phone"
                        />
                        <input
                            class="contact-form_field field required"
                            data-type="email"
                            type="email"
                            name="email"
                            id="feedbackEmail"
                            placeholder="Email Address"
                            required
                        />
                        <textarea
                            class="contact-form_field field required"
                            data-type="message"
                            name="message"
                            id="feedbackMessage"
                            placeholder="Tell us about your project scope"
                            required
                        ></textarea>
                        <button type="submit" class="contact-form_btn btn">Send message</button>
                    </form>
                </div>
                <div class="contact_info">
                    <h3 class="contact_info-header">{{ $contact['tagline'] ?? 'Connecting Energy, Building for the Future' }}</h3>
                    <ul class="contact-info">
                        <li class="contact-info_group">
                            <span class="name">Address</span>
                            <span class="content">{{ $contact['address'] ?? '' }}</span>
                        </li>
                        <li class="contact-info_group">
                            <span class="name">Email</span>
                            <span class="content d-inline-flex flex-column">
                                <a class="link" href="{{ $contact['email_href'] ?? '#' }}">{{ $contact['email'] ?? '' }}</a>
                                @if (!empty($contact['marketing_email']))
                                    <a class="link" href="mailto:{{ $contact['marketing_email'] }}">{{ $contact['marketing_email'] }}</a>
                                @endif
                            </span>
                        </li>
                        <li class="contact-info_group">
                            <span class="name">Phone</span>
                            <span class="content d-inline-flex flex-column">
                                <a class="link" href="{{ $contact['phone_href'] ?? '#' }}">{{ $contact['phone'] ?? '' }}</a>
                            </span>
                        </li>
                    </ul>
                    <ul class="socials d-flex align-items-center justify-content-start">
                        <li class="socials_item">
                            <a class="socials_item-link" href="https://www.instagram.com/epikepc/" target="_blank" rel="noopener noreferrer">
                                <i class="icon-instagram"></i>
                            </a>
                        </li>
                        <li class="socials_item">
                            <a class="socials_item-link" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact['phone'] ?? '') }}" target="_blank" rel="noopener noreferrer">
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
