@extends('layouts.frontend.main')

@section('title', 'Contact | EPIKEPC')
@section('page', 'contacts')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/contacts.min.css') }}" />
    <style>
        /* Contact form fallback styles (ensure fields visible) */
        .feedback-form .wrapper {
            gap: 20px;
            margin-bottom: 20px;
        }

        .feedback-form .field {
            width: 100%;
            display: block;
            border: 1px solid #d9dee8;
            background: #fff;
            color: #000810;
            padding: 14px 16px;
            font-size: 16px;
            line-height: 1.4;
        }

        .feedback-form .field::placeholder {
            color: #8a8f98;
            opacity: 1;
        }

        .feedback-form .field:focus {
            border-color: #ffdf08;
        }

        .feedback-form textarea.field {
            min-height: 140px;
        }

        .feedback-form .btn--submit {
            margin-top: 8px;
            width: 100%;
        }
    </style>
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Contact Us',
        'title'    => 'Contact',
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Contact'],
        ],
    ])
@endsection

@section('content')
        <!-- CONTACTS CONTENT START  -->
        <main>
            <section class="info section-nopb">
                <div class="container">
                    <div class="info_data row g-0">
                        <div class="info_header section_header col-md-6 col-xxl-3" data-order="1">
                            <span class="subtitle" data-aos="fade-right">Contact Us</span>
                            <h2 class="title" data-aos="fade-right" data-aos-delay="30">
                                <span class="highlight">Informasi</span>
                                Contact
                            </h2>
                        </div>
                        <div class="info_data-card col-md-6 col-xxl-3" data-aos="fade-left" data-order="1">
                            <div class="wrapper d-flex flex-column justify-content-between">
                                <i class="icon-location icon"></i>
                                <h4 class="title">Address</h4>
                                <span class="content">{{ $contact['address'] }}</span>
                            </div>
                        </div>
                        <div class="info_data-card col-md-6 col-xxl-3" data-aos="fade-left" data-aos-delay="20" data-order="2">
                            <div class="wrapper d-flex flex-column justify-content-between">
                                <i class="icon-inbox icon"></i>
                                <h4 class="title">Email</h4>
                                <span class="content d-flex flex-column">
                                    <a href="{{ $contact['email_href'] }}" class="link">{{ $contact['email'] }}</a>
                                </span>
                            </div>
                        </div>
                        <div class="info_data-card col-md-6 col-xxl-3" data-aos="fade-left" data-aos-delay="40" data-order="3">
                            <div class="wrapper d-flex flex-column justify-content-between">
                                <i class="icon-call icon"></i>
                                <h4 class="title">Phone</h4>
                                <span class="content d-flex flex-column">
                                    <a href="{{ $contact['phone_href'] }}" class="link">{{ $contact['phone'] }}</a>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="info_map">
                        <iframe
                            src="{{ $contact['map_embed_url'] }}"
                            style="width:100%;height:100%;min-height:450px;border:0;"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>
            </section>
            <section class="partners section">
                <div class="container">
                    <div class="row g-0">
                        <div class="partners_header section_header col-md-6 col-lg-4">
                            <span class="subtitle">Our Services</span>
                            <h2 class="title">
                                <span class="highlight">Best</span>
                                Solutions
                            </h2>
                        </div>
                        @if (!empty($services))
                            @foreach ($services as $index => $service)
                            <div class="partners_card col-md-6 col-lg-4" data-aos="fade-up" data-order="{{ $index + 1 }}">
                                <div class="wrapper d-flex justify-content-center align-items-center">
                                    <h4 class="title" style="text-align:center;">{{ $service }}</h4>
                                </div>
                            </div>
                            @endforeach
                        @else
                        <div class="partners_card col-md-6 col-lg-4" data-aos="fade-up" data-order="1">
                            <div class="wrapper d-flex justify-content-center align-items-center">
                                <svg width="142" height="123" viewBox="0 0 142 123" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M34.0193 94.1L36.6322 98H40L36.9613 93.5C37.8516 93.1 38.5355 92.5267 39.0129 91.7801C39.5032 91.0201 39.7484 90.12 39.7484 89.08C39.7484 88.0401 39.5097 87.14 39.0322 86.38C38.5549 85.62 37.8709 85.0334 36.9806 84.6201C36.1032 84.2068 35.0645 84 33.8645 84H28V98H31.1355V94.1H33.8645H34.0193ZM35.3239 87.6558C35.7746 88.0792 36 88.694 36 89.5C36 90.2924 35.7746 90.9071 35.3239 91.3442C34.8731 91.7814 34.2147 92 33.3488 92H31V87H33.3488C34.2147 87 34.8731 87.2186 35.3239 87.6558Z"
                                        fill="#000810"
                                    />
                                    <path
                                        d="M54 95.4V98H43V84H53.7362V86.6H46.2676V89.64H52.8635V92.1601H46.2676V95.4H54Z"
                                        fill="#000810"
                                    />
                                    <path
                                        d="M68.1102 98L68.0913 89.6L64.1749 96.52H62.7871L58.8898 89.78V98H56V84H58.5476L63.5285 92.7001L68.4335 84H70.9619L71 98H68.1102Z"
                                        fill="#000810"
                                    />
                                    <path
                                        d="M85 95.4V98H74V84H84.7362V86.6H77.2676V89.64H83.8635V92.1601H77.2676V95.4H85Z"
                                        fill="#000810"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M94.3238 84H88V98H94.3238C95.8351 98 97.1674 97.7133 98.3208 97.14C99.4875 96.5533 100.389 95.7333 101.026 94.68C101.675 93.6267 102 92.4 102 91C102 89.6 101.675 88.3734 101.026 87.3201C100.389 86.2667 99.4875 85.4534 98.3208 84.8801C97.1674 84.2934 95.8351 84 94.3238 84ZM97.6667 94.7973C96.7919 95.5991 95.6138 96 94.1322 96H91V87H94.1322C95.6138 87 96.7919 87.4078 97.6667 88.2235C98.5556 89.0254 99 90.1175 99 91.5C99 92.8825 98.5556 93.9816 97.6667 94.7973Z"
                                        fill="#000810"
                                    />
                                    <path
                                        d="M109.599 93.04V98H106.381V93L101 84H104.415L108.129 90.22L111.842 84H115L109.599 93.04Z"
                                        fill="#000810"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M30.8316 65.7103H23V65.7212V69H33.5157V18.2821H49.1834V69H59.7972V14.9951H51.8675V3.28975H77.4825V30.4575L69.5504 25.9983V69H80.1666V35.5615L85.7577 38.7038V69H96.3715V41.0645L88.4418 36.6052V25.5693L107.48 14.6727V68.9892H118V65.7103H110.169V9.51951L85.7622 23.4873V35.1107L80.171 31.9685V0H49.1834V14.9951H30.8316V65.7103ZM88 65V40L93 42.8959V65H88ZM57 18H52V65H57V18ZM72 31L77 33.9816V66H72V31Z"
                                        fill="#000810"
                                    />
                                </svg>
                            </div>
                        </div>
                        <div class="partners_card col-md-6 col-lg-4" data-aos="fade-up" data-order="2">
                            <div class="wrapper d-flex justify-content-center align-items-center">
                                <svg width="94" height="123" viewBox="0 0 94 123" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M11.3898 117.714H20.4981L22.2356 122H27L18.228 102H13.744L5 122H9.65224L11.3898 117.714ZM16 107L19 115H13L16 107Z"
                                        fill="#000810"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M38.029 116.429L41.9484 122H47L42.4419 115.572C43.7774 115 44.8032 114.181 45.5193 113.115C46.2548 112.029 46.6225 110.743 46.6225 109.257C46.6225 107.771 46.2644 106.486 45.5484 105.4C44.8322 104.314 43.8064 103.476 42.4709 102.886C41.1548 102.295 39.5968 102 37.7968 102H29V122H33.7033V116.429H37.7968H38.029ZM40.9182 106.918C41.6394 107.511 42 108.372 42 109.5C42 110.609 41.6394 111.47 40.9182 112.082C40.197 112.694 39.1436 113 37.758 113H34V106H37.758C39.1436 106 40.197 106.306 40.9182 106.918Z"
                                        fill="#000810"
                                    />
                                    <path
                                        d="M58.9999 123C56.1609 123 53.9463 122.184 52.3565 120.552C50.7854 118.92 50 116.59 50 113.561V102H54.5993V113.385C54.5993 117.081 56.0757 118.929 59.0283 118.929C60.4669 118.929 61.5647 118.487 62.3217 117.602C63.0789 116.698 63.4574 115.292 63.4574 113.385V102H68V113.561C68 116.59 67.205 118.92 65.6151 120.552C64.0442 122.184 61.8391 123 58.9999 123Z"
                                        fill="#000810"
                                    />
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M84.9951 102.886C83.7254 102.295 82.2223 102 80.4859 102H72V122H76.537V116.486H80.4859C82.2223 116.486 83.7254 116.201 84.9951 115.629C86.2833 115.038 87.2729 114.2 87.9637 113.115C88.6546 112.01 89 110.724 89 109.257C89 107.771 88.6546 106.486 87.9637 105.4C87.2729 104.314 86.2833 103.476 84.9951 102.886ZM83.9182 112.107C83.197 112.702 82.1435 113 80.758 113H77V106H80.758C82.1435 106 83.197 106.308 83.9182 106.922C84.6394 107.517 85 108.382 85 109.515C85 110.628 84.6394 111.493 83.9182 112.107Z"
                                        fill="#000810"
                                    />
                                    <path d="M23.25 83L0 41.5L23.25 0H69.75L94 41.8808L69.75 83H23.25Z" fill="#000810" />
                                    <path d="M25 1L47.3333 41H92L69.6667 1H25Z" fill="#000810" stroke="#A9A9A9" stroke-width="2" />
                                </svg>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </section>
            <section class="section" style="padding: 80px 0;">
                <div class="container">
                    <div class="row g-0">
                        <div class="col-12 col-lg-6 col-xl-5" data-aos="fade-right">
                            <div class="section_header" style="margin-bottom: 40px;">
                                <span class="subtitle">Send a Message</span>
                                <h2 class="title">Discuss Your <span class="highlight">Projects</span></h2>
                                <p class="text">Fill out the form below and our team will contact you within 24 hours.</p>
                            </div>
                        </div>
                        <div class="col-12 col-lg-6 col-xl-6 offset-xl-1" data-aos="fade-left">
                            @if (session('success'))
                                <div class="alert" style="background: rgba(72,199,116,0.1); border: 1px solid #48c774; color: #48c774; padding: 15px 20px; border-radius: 4px; margin-bottom: 20px;">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <form
                                class="feedback-form d-flex flex-column"
                                name="feedbackForm"
                                method="POST"
                                action="{{ route('frontend.contact.store') }}"
                                data-type="feedback"
                            >
                                @csrf
                                <div class="wrapper d-flex flex-wrap flex-sm-nowrap justify-content-between">
                                    <input
                                        class="field required"
                                        data-type="name"
                                        type="text"
                                        name="name"
                                        id="feedbackName"
                                        placeholder="Your Name *"
                                        required
                                        value="{{ old('name') }}"
                                    />
                                    <input
                                        class="field required"
                                        name="email"
                                        id="feedbackEmail"
                                        data-type="email"
                                        type="email"
                                        placeholder="Email *"
                                        required
                                        value="{{ old('email') }}"
                                    />
                                </div>
                                <div class="wrapper d-flex flex-wrap flex-sm-nowrap justify-content-between">
                                    <input
                                        class="field"
                                        type="tel"
                                        name="phone"
                                        id="feedbackPhone"
                                        placeholder="Phone Number"
                                        value="{{ old('phone') }}"
                                    />
                                    <input
                                        class="field"
                                        type="text"
                                        name="subject"
                                        id="feedbackSubject"
                                        placeholder="Subjek"
                                        value="{{ old('subject') }}"
                                    />
                                </div>
                                <textarea
                                    class="field required"
                                    data-type="message"
                                    name="message"
                                    id="feedbackMessage"
                                    placeholder="Your Message *"
                                    required
                                    rows="5"
                                >{{ old('message') }}</textarea>
                                <button class="btn btn--submit btn--static" type="submit">Send a Message</button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <!-- CONTACTS CONTENT END  -->
@endsection
