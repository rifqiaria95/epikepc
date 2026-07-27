@extends('layouts.frontend.main')

@section('title', 'About | EPIKEPC')
@section('page', 'about')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/about.min.css') }}" />
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Building communities',
        'title'    => 'About',
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'About'],
        ],
    ])
@endsection

@section('content')
    <main>
        {{-- ===== HERO ===== --}}
        <section class="hero section">
            <div class="container d-flex flex-wrap flex-xl-nowrap align-items-xl-center justify-content-between">
                <div class="hero_header section_header col-xl-auto">
                    <span class="subtitle" data-aos="fade-down">Who we are</span>
                    <h2 class="title" data-aos="fade-right">
                        {{ $about->title ?? 'Bringing' }}
                        <span class="highlight">Your Ideas</span>
                        and Innovations to Life
                    </h2>
                    <p class="text" data-aos="fade-up" data-aos-delay="20">
                        @php
                            $aboutDescription = $about->description
                                ?? 'Our civil and structural team is committed to providing sustainable, creative & efficient engineering solutions for our communities';
                            $aboutDescription = strip_tags(html_entity_decode($aboutDescription, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        @endphp
                        {{ $aboutDescription }}
                    </p>
                    <ul class="hero_header-list">
                        <li class="hero_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="30">
                            <i class="icon-arrow_right icon"></i>
                            Making lives easier
                        </li>
                        <li class="hero_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="30">
                            <i class="icon-arrow_right icon"></i>
                            Get every solution right here
                        </li>
                        <li class="hero_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="40">
                            <i class="icon-arrow_right icon"></i>
                            Innovation and creativity
                        </li>
                        <li class="hero_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="40">
                            <i class="icon-arrow_right icon"></i>
                            Fine engineering only with us
                        </li>
                    </ul>
                    <a class="btn" href="{{ route('frontend.contact.index') }}" data-aos="fade-up">Consult now</a>
                </div>
                <div class="hero_img" data-aos="zoom-in" data-aos-duration="700">
                    <picture>
                        <source data-srcset="{{ $about?->image_url ?: asset('frontend/img/placeholder.jpg') }}" srcset="{{ $about?->image_url ?: asset('frontend/img/placeholder.jpg') }}" type="image/webp" />
                        <img
                            class="hero_img-img lazy"
                            data-src="{{ $about?->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                            src="{{ $about?->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                            alt="media"
                        />
                    </picture>
                </div>
            </div>
        </section>

        {{-- ===== QUOTE ===== --}}
        <section class="quote primary-bg section">
            <div class="container">
                <div class="quote_header section_header">
                    <svg
                        class="quote_header-icon"
                        width="160"
                        height="160"
                        viewBox="0 0 160 160"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <mask id="mask0" maskUnits="userSpaceOnUse" x="0" y="0" width="160" height="160">
                            <path
                                fill-rule="evenodd"
                                clip-rule="evenodd"
                                d="M90.7095 160H69.3158C68.7996 160 68.299 159.825 67.8942 159.504C67.49 159.184 67.2065 158.736 67.0896 158.233L63.476 142.704C57.8216 141.218 52.3954 138.971 47.3457 136.025L33.8281 144.457C33.3898 144.731 32.8717 144.848 32.3581 144.79C31.8447 144.731 31.3662 144.5 31.0007 144.135L15.8672 128.994C15.5025 128.629 15.272 128.152 15.213 127.639C15.1541 127.127 15.2703 126.609 15.5426 126.171L23.9723 112.658C21.0277 107.607 18.7817 102.18 17.2958 96.5255L1.77142 92.9368C1.26798 92.8205 0.818802 92.5376 0.497106 92.1328C0.175415 91.7286 0.000186552 91.2274 0 90.7106V69.2892C0.000186552 68.7724 0.175415 68.2712 0.497106 67.8664C0.818802 67.4622 1.26798 67.1793 1.77142 67.063L17.2958 63.4743C18.7827 57.8211 21.0295 52.3956 23.9746 47.3462L15.5426 33.8285C15.2688 33.3902 15.1516 32.872 15.2101 32.3585C15.2687 31.8451 15.4995 31.3666 15.8649 31.0011L31.003 15.8628C31.3685 15.4983 31.8468 15.2682 32.3598 15.2101C32.8727 15.152 33.3903 15.2693 33.8281 15.5428L47.3411 23.9725C52.3913 21.0276 57.8173 18.7808 63.4711 17.2937L67.0846 1.76461C67.2027 1.26165 67.4875 0.813452 67.8924 0.493013C68.2972 0.172575 68.799 -0.00120015 69.3158 6.23909e-06H90.7095C91.2263 0.000192793 91.7275 0.175423 92.1317 0.497118C92.5365 0.818818 92.8195 1.268 92.9357 1.77145L96.5244 17.296C102.178 18.783 107.603 21.0298 112.652 23.9748L126.17 15.5428C126.608 15.269 127.126 15.1517 127.64 15.2103C128.153 15.2688 128.632 15.4997 128.997 15.8651L144.135 31.0034C144.5 31.369 144.73 31.8472 144.788 32.3601C144.846 32.8732 144.729 33.3907 144.455 33.8285L136.026 47.3416C138.971 52.391 141.217 57.8167 142.704 63.4693L158.229 67.058C158.732 67.1743 159.182 67.4579 159.503 67.8621C159.825 68.2663 160 68.7681 160 69.2842V90.7063C160 91.223 159.825 91.7242 159.503 92.1284C159.182 92.5326 158.732 92.8162 158.229 92.9325L142.704 96.5211C141.217 102.174 138.971 107.599 136.026 112.649L144.455 126.171C144.729 126.609 144.846 127.128 144.788 127.642C144.729 128.155 144.498 128.633 144.133 128.998L128.995 144.137C128.629 144.502 128.151 144.732 127.638 144.79C127.125 144.848 126.608 144.731 126.17 144.457L112.657 136.027C107.607 138.972 102.182 141.219 96.5287 142.706L92.9407 158.231C92.8238 158.735 92.5396 159.184 92.1342 159.505C91.7287 159.827 91.2269 160.001 90.7095 160Z"
                                fill="white"
                            />
                        </mask>
                        <g mask="url(#mask0)">
                            <rect width="160" height="160" fill="#344F6B" />
                        </g>
                        <circle cx="80" cy="80" r="44.5" stroke="#344F6B" />
                    </svg>

                    <span class="subtitle">Who we are</span>
                    <h2 class="title">
                        Pulvinar elementum integer enim neque volutpat ac. Amet dictum sit amet justo donec enim diam vulputate ut.
                        Egestas sed sed risus pretium quam. Viverra accumsan in nisl nisi scelerisque eu
                    </h2>
                    <span class="author">Benjamin Miller</span>
                </div>
            </div>
        </section>

        {{-- ===== VISION & MISSION ===== --}}
        <section class="services section">
            <div class="container">
                <div class="services_header section_header">
                    <span class="subtitle">Company Direction</span>
                    <h2 class="title">
                        Vision &
                        <span class="highlight">Mission</span>
                    </h2>
                </div>
                <div class="services_slider-slide d-flex flex-wrap align-items-start align-items-xl-center">
                    <div class="img-wrapper col-md-5">
                        <picture>
                            <source
                                data-srcset="{{ $about?->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                srcset="{{ $about?->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                type="image/webp"
                            />
                            <img
                                class="lazy"
                                data-src="{{ $about?->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                src="{{ $about?->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                alt="Vision and mission"
                            />
                        </picture>
                    </div>
                    <div class="text-wrapper col-md-6">
                        <h3 class="title">Vision</h3>
                        <p class="text">{{ $visionMission['vision'] }}</p>

                        <h3 class="title mt-4">Mission</h3>
                        <ul class="list">
                            @foreach ($visionMission['missions'] as $mission)
                                <li class="hero_header-list_item d-flex align-items-center">
                                    <i class="icon-arrow_right icon"></i>
                                    {{ $mission }}
                                </li>
                            @endforeach
                        </ul>

                        <a class="btn" href="{{ route('frontend.services.index') }}">Explore Services</a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== GALLERY ===== --}}
        @if (!empty($gallery) && count($gallery) > 0)
            <section class="gallery">
                <div class="container-fluid p-0">
                    <ul class="gallery_list d-flex flex-wrap">
                        @foreach ($gallery->take(4) as $item)
                            <li class="gallery_list-item col-12 col-sm-6 col-xl-3">
                                <a
                                    class="gallery_list-item_trigger"
                                    href="{{ $item->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    data-caption="{{ $item->title }}"
                                    data-role="gallery-link"
                                >
                                    <div class="img-wrapper">
                                        <picture>
                                            <source
                                                data-srcset="{{ $item->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                                srcset="{{ $item->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                                type="image/webp"
                                            />
                                            <img
                                                class="lazy"
                                                data-src="{{ $item->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                                src="{{ $item->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                                alt="{{ $item->title }}"
                                            />
                                        </picture>
                                    </div>
                                    <div class="text-wrapper d-flex flex-column justify-content-end">
                                        <span class="subtitle">Our gallery</span>
                                        <h4 class="title">{{ $item->title }}</h4>
                                        <span class="label">{{ $item->subtitle ?? 'Special Projects' }}</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </section>
        @endif

        {{-- ===== FEATURES / WHY US ===== --}}
        <section class="features pb-100 pt-100" style="padding-bottom: 100px; padding-top: 100px;">
            <div class="container">
                <div class="row g-0">
                    <div class="features_header section_header col-12 col-md-12 col-lg-6 col-xl-4">
                        <span class="subtitle">Why Choose Us</span>
                        <h2 class="title" data-aos="fade-right" data-aos-duration="500">
                            <span class="highlight">Designing </span>
                            Future with Excellence
                        </h2>
                        <ul class="features_header-list">
                            <li class="features_header-list_item d-flex align-items-center" data-aos="fade-up">
                                <i class="icon-check icon"></i>
                                Building the future with ideas
                            </li>
                            <li class="features_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="50">
                                <i class="icon-check icon"></i>
                                Designing future with excellence
                            </li>
                            <li class="features_header-list_item d-flex align-items-center" data-aos="fade-up" data-aos-delay="100">
                                <i class="icon-check icon"></i>
                                Discovering possibility in concrete
                            </li>
                        </ul>
                    </div>
                    <div class="features_card features_card--alt col-12 col-md-6 col-xl-4" data-aos="fade-up" data-order="1">
                        <div class="wrapper d-flex flex-column align-items-start justify-content-between">
                            <svg class="features_card-icon" width="60" height="62" viewBox="0 0 60 62" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M59.391 29.7654C59.7584 29.9203 59.9994 30.2951 59.9995 30.7118C59.9994 31.1285 59.7584 31.5032 59.391 31.6581L41.0732 39.3729L59.391 47.0877C59.7588 47.2423 60 47.6171 60 48.0341C60 48.451 59.7588 48.8259 59.391 48.9804L30.3593 61.2079C30.1286 61.3047 29.8714 61.3047 29.6407 61.2079L0.608983 48.9804C0.241228 48.8259 0 48.451 0 48.0341C0 47.6171 0.241228 47.2423 0.608983 47.0877L18.9268 39.3729L0.608983 31.6581C0.241228 31.5036 0 31.1287 0 30.7118C0 30.2949 0.241228 29.92 0.608983 29.7654L18.9268 22.0506L0.608983 14.3359C0.241228 14.1813 0 13.8064 0 13.3895C0 12.9726 0.241228 12.5977 0.608983 12.4432L29.6407 0.215664C29.8714 0.118867 30.1286 0.118867 30.3593 0.215664L59.391 12.4432C59.7588 12.5977 60 12.9726 60 13.3895C60 13.8064 59.7588 14.1813 59.391 14.3359L41.0732 22.0506L59.391 29.7654ZM30 2.25995L3.57386 13.3895L30 24.5191L56.4262 13.3895L30 2.25995ZM56.4262 48.0341L30 59.1636L3.57386 48.0341L21.5324 40.4709L29.6407 43.8856C29.8714 43.9824 30.1286 43.9824 30.3593 43.8856L38.4676 40.4709L56.4262 48.0341ZM3.57386 30.7118L30 41.8414L56.4262 30.7118L38.4676 23.1486L30.3593 26.5634C30.1286 26.6602 29.8714 26.6602 29.6407 26.5634L21.5324 23.1486L3.57386 30.7118Z" fill="currentColor" />
                            </svg>
                            <h3 class="features_card-title">We Develop Unique Projects</h3>
                            <p class="features_card-description">
                                Senectus et netus et malesuada. Nunc pulvinar sapien et ligula ullamcorper malesuada proin
                            </p>
                        </div>
                    </div>
                    <div class="features_card features_card--alt col-12 col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="50" data-order="2">
                        <div class="wrapper d-flex flex-column align-items-start justify-content-between">
                            <svg class="features_card-icon" width="60" height="62" viewBox="0 0 60 62" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M53.2974 33.0918L58.9607 37.4956C60.0184 38.3305 60.306 39.7985 59.6512 40.9931L53.7311 51.177C53.0894 52.3462 51.626 52.867 50.3501 52.3743L43.6496 49.6988C42.312 50.6485 40.99 51.4119 39.6447 52.017L38.6255 59.0887C38.4613 60.4035 37.2906 61.4145 35.9068 61.4145H24.0976C22.7139 61.4145 21.5433 60.4035 21.3764 59.0632L20.3597 52.017C18.9709 51.3941 17.6256 50.618 16.3471 49.7014L9.66458 52.3693C8.33988 52.8466 6.89714 52.3156 6.25789 51.1515L0.368612 41.0187C-0.301436 39.7958 -0.0138256 38.3279 1.03625 37.5009L6.70471 33.0919C6.62 32.2289 6.57631 31.481 6.57631 30.7789C6.57631 30.0768 6.61987 29.3288 6.70977 28.4659L1.04648 24.0645C-0.0317561 23.217 -0.316719 21.7005 0.378962 20.5312L6.27594 10.3832C6.91771 9.21398 8.37599 8.68803 9.657 9.18586L16.3575 11.8613C17.6951 10.9116 19.0171 10.1483 20.3624 9.5432L21.3815 2.46887C21.5458 1.15404 22.7164 0.143066 24.1002 0.143066H35.9095C37.2933 0.143066 38.4639 1.15404 38.6307 2.49448L39.6473 9.5432C41.0363 10.1636 42.3789 10.9396 43.6574 11.8587L50.34 9.19089C51.657 8.70837 53.1022 9.24199 53.7466 10.4087L59.6359 20.5414C60.3085 21.7642 60.0185 23.2322 58.9659 24.0594L53.2974 28.4658C53.3693 29.1449 53.4309 29.9388 53.4309 30.7788C53.4309 31.6187 53.3719 32.4126 53.2974 33.0918Z" fill="currentColor" />
                            </svg>
                            <h3 class="features_card-title">We Value Convenience and Functionality</h3>
                            <p class="features_card-description">
                                Magnis dis parturient montes nascetur ridiculus mus mauris vitae ultricies
                            </p>
                        </div>
                    </div>
                    <div class="features_card features_card--alt col-12 col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="100" data-order="3">
                        <div class="wrapper d-flex flex-column align-items-start justify-content-between">
                            <svg class="features_card-icon" width="60" height="62" viewBox="0 0 60 62" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M58.7498 58.957H1.25004C0.560038 58.957 0 59.5298 0 60.2355C0 60.9413 0.560038 61.5141 1.25004 61.5141H58.75C59.44 61.5141 60 60.9413 60 60.2355C59.9999 59.5298 59.4398 58.957 58.7498 58.957Z" fill="currentColor" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M3.75004 43.6143H11.25C11.94 43.6143 12.5001 44.187 12.5001 44.8927V60.2356C12.5001 60.9413 11.94 61.5142 11.25 61.5142H3.75004C3.06004 61.5142 2.5 60.9413 2.5 60.2357V44.8929C2.5 44.1871 3.06004 43.6143 3.75004 43.6143ZM5.00007 58.9571H9.99999V46.1714H5.00007V58.9571Z" fill="currentColor" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.75 30.8286H26.25C26.94 30.8286 27.5001 31.4015 27.5001 32.1072V60.2358C27.5001 60.9415 26.94 61.5144 26.25 61.5144H18.75C18.06 61.5144 17.5 60.9415 17.5 60.2358V32.1072C17.5 31.4015 18.06 30.8286 18.75 30.8286ZM20 58.9572H25V33.3857H20V58.9572Z" fill="currentColor" />
                            </svg>
                            <h3 class="features_card-title">Experience Allows Us to Implement New Ideas</h3>
                            <p class="features_card-description">
                                Viverra nibh cras pulvinar mattis nunc sed blandit libero volutpat. Enim diam vulputate ut pharetra
                            </p>
                        </div>
                    </div>
                    <div class="features_card features_card--alt col-12 col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="150" data-order="4">
                        <div class="wrapper d-flex flex-column align-items-start justify-content-between">
                            <svg class="features_card-icon" width="60" height="62" viewBox="0 0 60 62" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M26.25 61.5144H33.75C35.75 61.5144 37.5 59.7244 37.5 57.6787V51.7973C37.5 49.2401 38.75 46.4273 41 44.6373C45.25 41.313 47.5 36.1987 47.5 30.8287C47.5 25.9701 45.5 21.3673 42.25 18.043C39 14.7187 34.5 12.9287 29.75 12.9287C20.5 13.1844 12.75 20.8559 12.5 30.573C12.5 36.1987 14.75 41.313 19.25 44.893C21.25 46.683 22.5 48.9844 22.5 51.5416V56.9116C22.5 59.9801 24.5 61.5144 26.25 61.5144Z" fill="currentColor" />
                                <path d="M40 32.1075C39.25 32.1075 38.75 31.5961 38.75 30.8289C38.75 25.9703 34.75 21.8789 30 21.8789C29.25 21.8789 28.75 21.3675 28.75 20.6003C28.75 19.8332 29.25 19.3218 30 19.3218C36.25 19.3218 41.25 24.4361 41.25 30.8289C41.25 31.5961 40.75 32.1075 40 32.1075Z" fill="currentColor" />
                            </svg>
                            <h3 class="features_card-title">We Offer Innovative Technologies</h3>
                            <p class="features_card-description">
                                Consectetur adipiscing elit pellentesque habitant. Arcu felis bibendum ut tristique
                            </p>
                        </div>
                    </div>
                    <div class="features_card features_card--alt col-12 col-md-6 col-xl-4" data-aos="fade-up" data-aos-delay="200" data-order="5">
                        <div class="wrapper d-flex flex-column align-items-start justify-content-between">
                            <svg class="features_card-icon" width="60" height="62" viewBox="0 0 60 62" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13 55.8886C12.5 55.3772 12.5 54.61 13 54.0986C14 53.0757 14.5 52.0529 14.5 50.7743C14.5 49.4957 14 48.2172 13 47.45C12.5 46.9386 12.5 46.1715 13 45.66L18 39.0115C18.5 38.2443 18.75 37.2215 18.25 36.1986C18.25 35.9429 18.25 35.9429 18 35.9429C18 35.9429 17.75 35.9429 17.5 36.1986L8.25 45.4043C7.75 45.9157 7 45.9157 6.5 45.4043C6 44.8929 6 44.1257 6.5 43.6143L15.75 34.4086C16.5 33.6415 17.5 33.3857 18.5 33.3857C19.5 33.6415 20.25 34.1529 20.75 34.92C21.75 36.71 21.5 38.7557 20.25 40.29L15.75 46.1715C16.75 47.45 17.25 48.9843 17.25 50.5186C17.25 52.5643 16.5 54.3543 15 55.8886C14.75 56.4 14.5 56.4 14 56.4C13.75 56.4 13.25 56.1443 13 55.8886Z" fill="currentColor" />
                                <path d="M1.25 61.5143C0.5 61.5143 0 61.0028 0 60.2357V47.7057C0 43.3585 1.75 39.2671 4.75 35.9428L9.5 31.0843C10 30.5728 10.75 30.5728 11.25 31.0843C11.75 31.5957 11.75 32.3628 11.25 32.8743L6.5 37.7328C4 40.29 2.5 43.87 2.5 47.7057V58.9571H20V56.6557C20 56.1443 20.25 55.8885 20.5 55.6328C22.5 54.3543 23.5 52.3085 23.75 50.0071C23.75 49.24 24.25 48.7285 25 48.7285C25.75 48.7285 26.25 49.24 26.25 50.0071C26.25 52.82 24.75 55.6328 22.5 57.4228V60.2357C22.5 61.0028 22 61.5143 21.25 61.5143H1.25Z" fill="currentColor" />
                            </svg>
                            <h3 class="features_card-title">We Focus on Long-term Relationships</h3>
                            <p class="features_card-description">
                                Purus in massa tempor nec feugiat. Euismod lacinia at quis risus sed vulputate odio
                            </p>
                        </div>
                    </div>
                </div>
                <div class="features_video col-12" data-aos="zoom-in" data-aos-duration="600" data-aos-once="true">
                    <picture>
                        <source data-srcset="{{ asset('frontend/img/placeholder.jpg') }}" srcset="{{ asset('frontend/img/placeholder.jpg') }}" />
                        <img
                            class="features_video-thumb lazy"
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
        </section>
    </main>
@endsection

@push('scripts')
    <script src="{{ asset('frontend/js/about.min.js') }}"></script>
@endpush
