@extends('layouts.frontend.main')

@section('title', 'FAQ | EPIKEPC')
@section('page', 'faq')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/faq.min.css') }}" />
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'General Questions',
        'title'    => 'FAQ',
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'FAQ'],
        ],
    ])
@endsection

@section('content')
    <!-- FAQ CONTENT START -->
    <main>
        <section class="faq section">
            <div
                class="container d-lg-flex flex-wrap flex-xl-nowrap justify-content-start align-items-start justify-content-xl-between"
            >
                <div class="faq_header section_header col-lg-12 col-xl-auto">
                    <span class="subtitle">Need Answers?</span>
                    <h2 class="title">
                        Frequently
                        <span class="highlight">Asked Questions</span>
                    </h2>
                    <p class="text">
                        Can't find the answer you're looking for? Contact our team and we'll be happy to help.
                    </p>
                    <div class="wrapper">
                        <a class="btn" href="{{ route('frontend.contact.index') }}">Contact Us</a>
                    </div>
                </div>
                <div class="accordion faq_accordion col-12 col-lg-12 col-xl-auto">
                    @foreach ($faqs as $index => $faq)
                        @php
                            $collapseId = 'faqCollapse' . $index;
                            $isFirst    = $index === 0;
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
    <!-- FAQ CONTENT END -->
@endsection

@push('scripts')
    <script src="{{ asset('frontend/js/faq.min.js') }}"></script>
@endpush
