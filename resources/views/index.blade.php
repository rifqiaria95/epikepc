@extends('layouts.frontend.main')
@section('content')
    <!-- Hero Section -->
    <section id="home" class="hero-section" style="background-image: url('frontend/img/core-img/grid3.png');">
        <!-- Divider -->
        <div class="divider"></div>

        <div class="container">
            <!-- Hero Content -->
            <div class="hero-content">
                <div class="row g-5">
                    <div class="col-12 col-md-6">
                        <h2 class="mb-0 wow fadeInUp text-white" data-wow-duration="1000ms" data-wow-delay="400ms">Best IT <br> & Digital
                            <span>Solution</span> <br> For Your Business
                        </h2>
                    </div>
                    <div class="col-12 col-md-6 col-xl-5 offset-xl-1 col-xxl-4 offset-xxl-1">
                        <p class="text-white wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="600ms">At Kainnova, we
                            are dedicated transforming your digital aspirations into reality. With a passion for innovation
                            and
                            a commitment to excellence At Kainnova, we are dedicated to transforming your digital
                            aspirations.
                        </p>
                        <a class="btn border-2 btn-outline-light wow fadeInUp" data-wow-duration="1000ms"
                        data-wow-delay="800ms" href="{{ route('about.index') }}">Explore More <i
                        class="ti ti-arrow-up-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </section>

    <!-- CTA Wrapper -->
    <div class="cta-wrap jarallax" data-jarallax="" data-speed="0.6"
        style="background-image: url('frontend/img/bg-img/bg.png');">
        <!-- Divider -->
        <div class="divider"></div>

        <!-- Divider -->
        <div class="divider"></div>
        <div class="divider"></div>
        <div class="divider"></div>
    </div>

    <!-- About Section -->
    <section id="about" class="about-section">
        <!-- Right Shape -->
        <div class="right-shape">
            <img src="{{ url('/frontend/img/core-img/shape.png') }}" alt="">
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <div class="container">
            <div class="row g-5 align-items-center">
                <div class="col-12 col-lg-6">
                    <!-- About Content -->
                    <div class="about-content ps-md-4">
                        <div class="section-heading">
                            <span class="sub-title">{{ $about->title ?? 'About Us' }}</span>
                            <h2 class="mb-4">{{ $about->subtitle ?? 'We Are About to Witness Something Great' }}</h2>
                            <p class="mb-5">{{ isset($about->description) ? strip_tags($about->description) : 'Empower your business with our cutting-edge IT services and unmatched support, tailored for transformative growth and harness innovation and achieve your goals with our dedicated expertise.' }}</p>

                            <!-- About List -->
                            <ul class="about-list ps-0 d-flex flex-column gap-3 list-unstyled mb-5">
                                <li class="d-flex align-items-center gap-2">
                                    <div class="icon">
                                        <i class="ti ti-arrow-right"></i>
                                    </div>
                                    <h5 class="mb-0">Created 40+ unique sections with responsiveness.</h5>
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <div class="icon">
                                        <i class="ti ti-arrow-right"></i>
                                    </div>
                                    <h5 class="mb-0">You will able to build a new site with an ease.</h5>
                                </li>
                                <li class="d-flex align-items-center gap-2">
                                    <div class="icon">
                                        <i class="ti ti-arrow-right"></i>
                                    </div>
                                    <h5 class="mb-0">Booster is made for stay ahead from the compitition.</h5>
                                </li>
                            </ul>

                            <!-- Button -->
                            <a class="btn btn-primary" href="about-us.html">Read More <i
                                    class="ti ti-arrow-up-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <!-- About Video -->
                    <div class="about-video-content wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="500ms">
                        <img src="{{ $about->image_url ?? asset('frontend/img/bg-img/shape1.jpg') }}" alt="">

                        <!-- Play Video -->
                        <div class="play-video-btn video-btn" data-video="{{ $about->video ?? 'https://youtu.be/4GUFkrHvZdE' }}">
                            <div class="icon">
                                <i class="ti ti-player-play-filled"></i>
                            </div>
                        </div>
                    </div>

                    <!-- About Images -->
                    <div class="about-images d-flex px-5 mt-5 wow fadeInUp" data-wow-duration="1000ms"
                        data-wow-delay="800ms">
                        <div style="width: 400px; height: 400px; border-radius: 50%; overflow: hidden; flex-shrink: 0;">
                            <img class="w-100 h-100" src="{{ url('/frontend/img/bg-img/techno.jpg') }}" alt="" style="object-fit: cover;">
                        </div>
                        <div>
                            <svg class="rotatingImage" xmlns="http://www.w3.org/2000/svg" width="70" height="70"
                                viewBox="0 0 70 70" fill="none">
                                <path
                                    d="M35 0L46.1369 23.8631L70 35L46.1369 46.1369L35 70L23.8631 46.1369L0 35L23.8631 23.8631L35 0Z"
                                    fill="#222222" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </section>

    <!-- Popup Video Container -->
    <div id="videoPopup" class="video-popup-iframe">
        <div class="video-content">
            <span class="close-btn" id="videoCloseButton"><i class="ti ti-x"></i></span>
            <div class="ratio ratio-16x9">
                <iframe id="videoFrame" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <!-- Service Section -->
    <section id="services" class="service-section bg-secondary">
        <!-- Background Shape -->
        <div class="bg-shape">
            <img src="{{ url('/frontend/img/core-img/shape2.png') }}" alt="">
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <div class="container">
            <div class="row align-items-end g-4 g-xl-5">
                <div class="col-12 col-md-6">
                    <div class="section-heading">
                        <span class="sub-title">Premium Services</span>
                        <h2 class="mb-0">Our Latest Services</h2>
                    </div>
                </div>

                <div class="col-12 col-md-5 offset-md-1">
                    <div class="section-heading">
                        <p class="mb-0">We empower businesses with innovative digital solutions designed to boost performance, strengthen brand presence, and drive measurable growth.</p>
                    </div>
                </div>
            </div>

            <div class="divider-sm"></div>

            <div class="row g-4 g-xl-5">
                <!-- Service Nav -->
                <div class="col-12 col-md-5 col-lg-4">
                    <div class="service-nav nav flex-column" id="v-pills-tab" role="tablist"
                        aria-orientation="vertical">
                        <div class="service-nav-item active" id="v-pills-home-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-home" role="tab" aria-controls="v-pills-home"
                            aria-selected="true">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                    viewBox="0 0 36 36" fill="none">
                                    <g clip-path="url(#clip0_1_448)">
                                        <mask id="mask0_1_448" style="mask-type:luminance" maskUnits="userSpaceOnUse"
                                            x="0" y="0" width="36" height="36">
                                            <path d="M36 0H0V36H36V0Z" fill="white" />
                                            <path
                                                d="M31.9681 21.8425C29.7827 26.5987 24.9899 29.6719 19.7579 29.6719C18.6641 29.6719 17.5845 29.5398 16.5389 29.2827L14.2617 31.5298C16.0121 32.159 17.8678 32.4844 19.7579 32.4844C22.9257 32.4844 25.9959 31.5716 28.6365 29.8449C31.2081 28.1633 33.2439 25.8021 34.5237 23.0168L31.9681 21.8425Z"
                                                fill="white" />
                                            <path
                                                d="M6.75717 19.6212C6.47374 18.5268 6.32812 17.393 6.32812 16.2416H3.51562C3.51562 18.1722 3.85327 20.0625 4.50682 21.8418L6.75717 19.6212Z"
                                                fill="white" />
                                        </mask>
                                        <g mask="url(#mask0_1_448)">
                                            <mask id="mask1_1_448" style="mask-type:luminance" maskUnits="userSpaceOnUse"
                                                x="0" y="0" width="36" height="36">
                                                <path d="M0 3.8147e-06H36V36H0V3.8147e-06Z" fill="white" />
                                            </mask>
                                            <g mask="url(#mask1_1_448)">
                                                <path
                                                    d="M26.8535 13.0595L23.1837 16.7115C22.6656 17.2299 21.9858 17.4892 21.3065 17.4892C20.6272 17.4892 19.9475 17.2299 19.4293 16.7115C18.3922 15.6746 18.3922 13.9937 19.4293 12.9568L23.0812 9.28716C22.0889 8.82499 21.0043 8.55949 19.8374 8.55949C15.5608 8.55949 12.0937 12.0265 12.0937 16.3032C12.0937 17.4701 12.3591 18.5724 12.8214 19.5647L2.18408 30.0614C1.14697 31.0983 1.14697 32.7793 2.18408 33.8162C2.70221 34.3345 3.382 34.5938 4.06121 34.5938C4.74057 34.5938 5.42028 34.3345 5.93842 33.8162L16.5759 23.3193C17.5682 23.7815 18.6706 24.0469 19.8374 24.0469C24.1142 24.0469 27.5812 20.58 27.5812 16.3032C27.5812 15.1364 27.3157 14.0518 26.8535 13.0595Z"
                                                    stroke="#601FEB" stroke-width="2.2" stroke-miterlimit="10" />
                                                <path
                                                    d="M31.9681 21.8425C29.7827 26.5987 24.9899 29.6719 19.7579 29.6719C18.6641 29.6719 17.5845 29.5398 16.5389 29.2827L14.2617 31.5298C16.0121 32.159 17.8678 32.4844 19.7579 32.4844C22.9257 32.4844 25.9959 31.5716 28.6365 29.8449C31.2081 28.1633 33.2439 25.8021 34.5237 23.0168L31.9681 21.8425Z"
                                                    fill="#601FEB" />
                                                <path
                                                    d="M6.75717 19.6212C6.47374 18.5268 6.32812 17.393 6.32812 16.2416H3.51562C3.51562 18.1722 3.85327 20.0625 4.50682 21.8418L6.75717 19.6212Z"
                                                    fill="#601FEB" />
                                                <path
                                                    d="M6.1748 10.2656C8.47374 5.04837 13.6904 1.40625 19.7578 1.40625C27.9514 1.40625 34.5937 8.04853 34.5937 16.2422"
                                                    stroke="#601FEB" stroke-width="2.2" stroke-miterlimit="10" />
                                                <path d="M10.4766 10.4766H4.85156V4.85156" stroke="#601FEB"
                                                    stroke-width="2.2" stroke-miterlimit="10" />
                                                <path d="M28.9688 22.0078H34.5937V27.6328" stroke="#601FEB"
                                                    stroke-width="2.2" stroke-miterlimit="10" />
                                            </g>
                                        </g>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_1_448">
                                            <rect width="36" height="36" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </div>
                            <h4 class="mb-0">IT Management Service</h4>
                        </div>

                        <div class="service-nav-item" id="v-pills-profile-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-profile" role="tab" aria-controls="v-pills-profile"
                            aria-selected="false">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                    viewBox="0 0 36 36" fill="none">
                                    <g clip-path="url(#clip0_1_478)">
                                        <path
                                            d="M15.1875 34.5938C15.1875 31.1464 13.2398 27.9949 10.1564 26.4532L9.5625 26.1562L18 13.3594L26.4375 26.1562L25.8436 26.4532C22.7602 27.9949 20.8125 31.1464 20.8125 34.5938"
                                            stroke="#2B4DFF" stroke-width="2.2" stroke-miterlimit="10" />
                                        <path d="M10.9688 34.5938H25.0312" stroke="#2B4DFF" stroke-width="2.2"
                                            stroke-miterlimit="10" />
                                        <path d="M2.53125 4.21875H33.4688" stroke="#2B4DFF" stroke-width="2.2"
                                            stroke-miterlimit="10" />
                                        <path d="M18 24.75V13.3594" stroke="#2B4DFF" stroke-width="2.2"
                                            stroke-miterlimit="10" />
                                        <path d="M18 8.4375V0" stroke="#2B4DFF" stroke-width="2.2"
                                            stroke-miterlimit="10" />
                                        <path d="M0 18.2812H8.15625" stroke="#2B4DFF" stroke-width="2.2"
                                            stroke-miterlimit="10" />
                                        <path d="M27.8438 18.2812H36" stroke="#2B4DFF" stroke-width="2.2"
                                            stroke-miterlimit="10" />
                                        <path
                                            d="M3.9375 18.2812C3.9375 10.5147 10.2335 4.21875 18 4.21875C25.7665 4.21875 32.0625 10.5147 32.0625 18.2812"
                                            stroke="#2B4DFF" stroke-width="2.2" stroke-miterlimit="10" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_1_478">
                                            <rect width="36" height="36" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </div>
                            <h4 class="mb-0">UI/UX & Branding Identity</h4>
                        </div>

                        <div class="service-nav-item" id="v-pills-contact-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-contact" role="tab" aria-controls="v-pills-contact"
                            aria-selected="false">
                            <div class="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                    viewBox="0 0 36 36" fill="none">
                                    <g clip-path="url(#clip0_1_492)">
                                        <mask id="mask0_1_492" style="mask-type:luminance" maskUnits="userSpaceOnUse"
                                            x="0" y="0" width="36" height="36">
                                            <path d="M0 3.8147e-06H36V36H0V3.8147e-06Z" fill="white" />
                                        </mask>
                                        <g mask="url(#mask0_1_492)">
                                            <path
                                                d="M19.4062 20.8125C19.4062 21.5892 18.7767 22.2188 18 22.2188C17.2233 22.2188 16.5938 21.5892 16.5938 20.8125C16.5938 20.0358 17.2233 19.4063 18 19.4063C18.7767 19.4063 19.4062 20.0358 19.4062 20.8125Z"
                                                fill="#601FEB" />
                                            <path d="M25.0312 26.4375H10.9688V15.1875H25.0312V26.4375Z" stroke="#601FEB"
                                                stroke-width="2.2" stroke-miterlimit="10" />
                                            <path
                                                d="M13.7812 15.1875V10.9688C13.7812 8.6388 15.6701 6.75 18 6.75C20.3299 6.75 22.2188 8.6388 22.2188 10.9688V15.1875"
                                                stroke="#601FEB" stroke-width="2.2" stroke-miterlimit="10" />
                                            <path
                                                d="M12.3628 2.27398C14.0457 1.7112 15.8466 1.40625 17.7188 1.40625C27.0383 1.40625 34.5938 8.96119 34.5938 18.2813C34.5938 22.6033 32.969 26.5458 30.2969 29.5312H36"
                                                stroke="#601FEB" stroke-width="2.2" stroke-miterlimit="10" />
                                            <path
                                                d="M23.9726 33.6099C22.1945 34.2468 20.2784 34.5938 18.2812 34.5938C8.96168 34.5938 1.40625 27.0388 1.40625 17.7188C1.40625 13.3968 3.0311 9.45422 5.70312 6.46875H0"
                                                stroke="#601FEB" stroke-width="2.2" stroke-miterlimit="10" />
                                        </g>
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_1_492">
                                            <rect width="36" height="36" fill="white" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </div>
                            <h4 class="mb-0">Digital Services</h4>
                        </div>
                    </div>
                </div>

                <!-- Service Tab Content -->
                <div class="col-12 col-md-7 col-lg-8">
                    <div class="tab-content ps-lg-4" id="v-pills-tabContent">
                        <!-- Service Tab -->
                        <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel"
                            aria-labelledby="v-pills-home-tab">
                            <!-- Service Tab Content -->
                            <div class="service-tab-content">
                                <img src="frontend/img/bg-img/dashboard.png" alt="">

                                <!-- Service Tab Card -->
                                <div class="service-tab-card">
                                    <h4 class="mb-2">Services Analysis</h4>
                                    <p>We provide end-to-end IT management solutions to keep your systems running seamlessly. From infrastructure monitoring to data security and cloud management — we make sure your technology works for you, not against you.</p>
                                    <a href="{{ route('frontend.services.index') }}" class="btn btn-primary">View All</a>
                                </div>
                            </div>
                        </div>

                        <!-- Service Tab -->
                        <div class="tab-pane fade" id="v-pills-profile" role="tabpanel"
                            aria-labelledby="v-pills-profile-tab">
                            <!-- Service Tab Content -->
                            <div class="service-tab-content">
                                <img src="{{ url('/frontend/img/bg-img/Brand Identity Design.jpg') }}" alt="">

                                <!-- Service Tab Card -->
                                <div class="service-tab-card">
                                    <h4 class="mb-2">Branding Identity</h4>
                                    <p>Your brand deserves to stand out. Our design team blends creativity with strategy to craft user-centered designs and a strong visual identity that speaks your brand’s story.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Service Tab -->
                        <div class="tab-pane fade" id="v-pills-contact" role="tabpanel"
                            aria-labelledby="v-pills-contact-tab">
                            <!-- Service Tab Content -->
                            <div class="service-tab-content">
                                <img src="{{ url('/frontend/img/bg-img/digital service.jpg') }}" alt="">

                                <!-- Service Tab Card -->
                                <div class="service-tab-card">
                                    <h4 class="mb-2">Digital Services</h4>
                                    <p>We help you grow in the digital world through smart marketing strategies and tailored online solutions — from SEO and content to automation and analytics.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider(s) -->
        <div class="divider"></div>
        <div class="divider"></div>
    </section>

    <!-- Process Section -->
    <section class="process-section">
        <!-- Divider -->
        <div class="divider"></div>

        <div class="container">
            <!-- Section Heading -->
            <div class="row justify-content-center">
                <div class="col-12 col-md-5 col-xl-7">
                    <div class="section-heading text-center">
                        <span class="sub-title">Working Process</span>
                        <h2 class="mb-0">Our Approach to Development</h2>
                    </div>
                </div>
            </div>

            <div class="divider-sm"></div>

            <!-- Process Wrap -->
            <div class="process-wrap">
                <div class="row g-4 g-lg-5">

                    <!-- Process Card -->
                    <div class="col-12 col-md-4">
                        <div class="process-card wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="400ms">
                            <div class="process-thumb">
                                <img src="{{ url('/frontend/img/bg-img/working1.jpg') }}" alt="">
                                <div class="number">1</div>
                            </div>
                            <div class="process-text">
                                <h5 class="process-title mb-3">Define Requirements</h5>
                                <p class="mb-0">We begin by deeply understanding your business goals and challenges. Our team translates your needs into clear, actionable project objectives — ensuring every solution we build truly fits your vision.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Process Card -->
                    <div class="col-12 col-md-4">
                        <div class="process-card wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="600ms">
                            <div class="process-thumb">
                                <img src="{{ url('/frontend/img/bg-img/working2.jpg') }}" alt="">
                                <div class="number">2</div>
                            </div>
                            <div class="process-text">
                                <h5 class="process-title mb-3">Final Solution</h5>
                                <p class="mb-0">We bring ideas to life through creative design and interactive prototypes. This stage allows you to visualize the final product early, refine details, and ensure an exceptional user experience before development begins.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Process Card -->
                    <div class="col-12 col-md-4">
                        <div class="process-card wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="800ms">
                            <div class="process-thumb">
                                <img src="{{ url('/frontend/img/bg-img/working3.jpg') }}" alt="">
                                <div class="number">3</div>
                            </div>
                            <div class="process-text">
                                <h5 class="process-title mb-3">Design & Prototyping</h5>
                                <p class="mb-0">We deliver complete, high-quality solutions tailored to your business needs. Every project is tested, optimized, and ready to perform — helping your organization grow with confidence and efficiency.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Showcase Section -->
    <section class="showcase-section softora-container bg-dark">
        <!-- Divider -->
        <div class="divider"></div>

        <div class="container">
            <div class="row softora-filter g-4 g-md-5">

                <!-- Showcase Card -->
                <div class="col-12 col-sm-6 filter-item showcase-item">
                    <div class="showcase-card wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="400ms">
                        <div class="showcase-thumb" style="box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.1);">
                            <img src="{{ url('/frontend/img/bg-img/responsive.jpg') }}" alt="">
                            <a href="portfolio-details.html" class="btn"><i class="ti ti-arrow-up-right"></i></a>
                        </div>
                        <div class="showcase-content">
                            <p>Branding / Technology / Marketing</p>
                            <h2>Building Responsive Websites for Better User Accessibility</h2>
                        </div>
                    </div>
                </div>

                <!-- Showcase Card -->
                <div class="col-12 col-sm-6 filter-item showcase-item">
                    <div class="showcase-card wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="showcase-thumb" style="box-shadow: 0 2px 10px 0 rgba(0, 0, 0, 0.1);">
                            <img src="{{ url('/frontend/img/bg-img/web-revamp.jpg') }}" alt="">
                            <a href="portfolio-details.html" class="btn"><i class="ti ti-arrow-up-right"></i></a>
                        </div>
                        <div class="showcase-content">
                            <p>Branding / Technology / Marketing</p>
                            <h2>Web Revamp and Company Profile Redesign</h2>
                        </div>
                    </div>
                </div>

                <!-- Showcase Card -->
                <div class="col-12 col-sm-6 filter-item showcase-item">
                    <div class="showcase-card wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="400ms">
                        <div class="showcase-thumb">
                            <img src="frontend/img/bg-img/design.jpg" alt="">
                            <a href="portfolio-details.html" class="btn"><i class="ti ti-arrow-up-right"></i></a>
                        </div>
                        <div class="showcase-content">
                            <p>Branding / Technology / Marketing</p>
                            <h2>Transforming Ideas into Stunning Visual Identities</h2>
                        </div>
                    </div>
                </div>

                <!-- Showcase Card -->
                <div class="col-12 col-sm-6 filter-item showcase-item">
                    <div class="showcase-card wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="600ms">
                        <div class="showcase-thumb">
                            <img src="frontend/img/bg-img/web-portal.jpg" alt="">
                            <a href="portfolio-details.html" class="btn"><i class="ti ti-arrow-up-right"></i></a>
                        </div>
                        <div class="showcase-content">
                            <p>Branding / Technology / Marketing</p>
                            <h2>Innovative Web Portal Creation for an Enhanced UX Experience</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </section>

    <!-- Testimonial Section -->
    <section class="testimonial-section bg-secondary">
        <!-- Divider -->
        <div class="divider"></div>

        <div class="container">
            <div class="row g-5">

                <div class="col-12 col-lg-12">
                    <!-- Section Heading -->
                    <div class="section-heading">
                        <span class="sub-title">Testimonial</span>
                        <h2 class="mb-0">What our Clients<br>Say About us</h2>
                    </div>

                    <div class="divider-sm"></div>

                    <!-- Testimonial Slider -->
                    <div class="testimonial-slide">
                        <div class="swiper testimonial-swiper">
                            <div class="swiper-wrapper">

                                @forelse($testimonials as $testimonial)
                                <!-- Testimonial Slide -->
                                <div class="swiper-slide">
                                    <div class="testimonial-card">
                                        <div class="testimonial-thumbnail">
                                            <img src="{{ $testimonial->gambar_url }}" alt="{{ $testimonial->nama }}" 
                                                 onerror="this.onerror=null; this.src='{{ asset('frontend/img/bg-img/4.jpg') }}';">
                                        </div>
                                        <div class="testimonial-info">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="40"
                                                viewBox="0 0 60 40" fill="none">
                                                <path
                                                    d="M58.6207 0C59.2341 0 59.7854 0.424145 59.9513 1.05329C60.1353 1.75286 59.7835 2.48528 59.1324 2.75457C54.8083 4.54586 51.6329 7.61643 49.6735 11.8959C55.6703 13.503 59.9998 19.149 59.9998 25.7143C59.9998 33.5917 53.81 40 46.2018 40C38.5937 40 32.4046 33.5917 32.4046 25.7143C32.3931 25.6061 31.1752 4.30729 58.4116 0.0159988C58.4819 0.00485611 58.5513 0 58.6207 0ZM46.2018 37.1429C52.2879 37.1429 57.2388 32.0159 57.2388 25.7143C57.2388 19.9596 53.0805 15.0879 47.5668 14.3834C47.1463 14.329 46.7727 14.0786 46.5544 13.7033C46.3353 13.328 46.2949 12.8697 46.4452 12.4596C47.5526 9.43157 49.1286 6.86114 51.1588 4.76357C34.3216 10.8573 35.15 25.4416 35.1615 25.6083C35.1656 32.0159 40.1165 37.1429 46.2018 37.1429Z"
                                                    fill="#601FEB" />
                                                <path
                                                    d="M26.2269 0C26.8403 0 27.3916 0.424145 27.5575 1.05329C27.7415 1.75286 27.3897 2.48528 26.7386 2.75457C22.4145 4.54586 19.2391 7.61643 17.2797 11.8959C23.2765 13.503 27.606 19.149 27.606 25.7143C27.606 33.5917 21.4162 40 13.808 40C6.19994 40 0.0107918 33.5917 0.0107918 25.7143C-0.000663757 25.6061 -1.21865 4.30729 26.0178 0.0159988C26.0881 0.00485611 26.1575 0 26.2269 0ZM13.808 37.1429C19.8941 37.1429 24.845 32.0159 24.845 25.7143C24.845 19.9596 20.6867 15.0879 15.173 14.3834C14.7525 14.329 14.3789 14.0786 14.1606 13.7033C13.9415 13.328 13.9011 12.8697 14.0514 12.4596C15.1588 9.43157 16.7348 6.86114 18.765 4.76357C1.92776 10.8573 2.75622 25.4416 2.76768 25.6083C2.77182 32.0159 7.7227 37.1429 13.808 37.1429Z"
                                                    fill="#601FEB" />
                                            </svg>
                                            <p class="testimonial-text mt-4">"{{ trim(strip_tags($testimonial->testimoni)) }}"</p>
                                            <h4 class="name">{{ $testimonial->nama }}</h4>
                                            <p class="designation mb-0">{{ $testimonial->instansi }}</p>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <!-- Default Testimonial Slide jika tidak ada data -->
                                <div class="swiper-slide">
                                    <div class="testimonial-card">
                                        <div class="testimonial-thumbnail">
                                            <img src="frontend/img/bg-img/4.jpg" alt="">
                                        </div>
                                        <div class="testimonial-info">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="40"
                                                viewBox="0 0 60 40" fill="none">
                                                <path
                                                    d="M58.6207 0C59.2341 0 59.7854 0.424145 59.9513 1.05329C60.1353 1.75286 59.7835 2.48528 59.1324 2.75457C54.8083 4.54586 51.6329 7.61643 49.6735 11.8959C55.6703 13.503 59.9998 19.149 59.9998 25.7143C59.9998 33.5917 53.81 40 46.2018 40C38.5937 40 32.4046 33.5917 32.4046 25.7143C32.3931 25.6061 31.1752 4.30729 58.4116 0.0159988C58.4819 0.00485611 58.5513 0 58.6207 0ZM46.2018 37.1429C52.2879 37.1429 57.2388 32.0159 57.2388 25.7143C57.2388 19.9596 53.0805 15.0879 47.5668 14.3834C47.1463 14.329 46.7727 14.0786 46.5544 13.7033C46.3353 13.328 46.2949 12.8697 46.4452 12.4596C47.5526 9.43157 49.1286 6.86114 51.1588 4.76357C34.3216 10.8573 35.15 25.4416 35.1615 25.6083C35.1656 32.0159 40.1165 37.1429 46.2018 37.1429Z"
                                                    fill="#601FEB" />
                                                <path
                                                    d="M26.2269 0C26.8403 0 27.3916 0.424145 27.5575 1.05329C27.7415 1.75286 27.3897 2.48528 26.7386 2.75457C22.4145 4.54586 19.2391 7.61643 17.2797 11.8959C23.2765 13.503 27.606 19.149 27.606 25.7143C27.606 33.5917 21.4162 40 13.808 40C6.19994 40 0.0107918 33.5917 0.0107918 25.7143C-0.000663757 25.6061 -1.21865 4.30729 26.0178 0.0159988C26.0881 0.00485611 26.1575 0 26.2269 0ZM13.808 37.1429C19.8941 37.1429 24.845 32.0159 24.845 25.7143C24.845 19.9596 20.6867 15.0879 15.173 14.3834C14.7525 14.329 14.3789 14.0786 14.1606 13.7033C13.9415 13.328 13.9011 12.8697 14.0514 12.4596C15.1588 9.43157 16.7348 6.86114 18.765 4.76357C1.92776 10.8573 2.75622 25.4416 2.76768 25.6083C2.77182 32.0159 7.7227 37.1429 13.808 37.1429Z"
                                                    fill="#601FEB" />
                                            </svg>
                                            <p class="testimonial-text mt-4">"Belum ada testimoni yang tersedia saat ini."</p>
                                            <h4 class="name">-</h4>
                                            <p class="designation mb-0">-</p>
                                        </div>
                                    </div>
                                </div>
                                @endforelse

                            </div>
                        </div>

                        <!-- Background Circles -->
                        <div class="testimonial-circles">
                            <img src="frontend/img/core-img/circles.png" alt="">
                        </div>
                    </div>
                    <!-- Swiper Navigation -->
                    <div class="swiper-navigation-container mt-5 mb-8">
                        <div class="testimonial-button-prev">
                            <i class="ti ti-arrow-left"></i>
                        </div>
                        <div class="testimonial-button-next">
                            <i class="ti ti-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>

        <!-- Trusted Clients Logo -->
        {{-- <div class="trusted-clients-logo">
            <div class="container">
                <div class="row g-4 align-items-center justify-content-center">
                    <!-- Partner Logo -->
                    <div class="col-6 col-sm-4 col-lg-3 col-xl-2">
                        <div class="partner-logo">
                            <img src="frontend/img/partner-img/1.png" alt="">
                        </div>
                    </div>

                    <!-- Partner Logo -->
                    <div class="col-6 col-sm-4 col-lg-3 col-xl-2">
                        <div class="partner-logo">
                            <img src="frontend/img/partner-img/2.png" alt="">
                        </div>
                    </div>

                    <!-- Partner Logo -->
                    <div class="col-6 col-sm-4 col-lg-3 col-xl-2">
                        <div class="partner-logo">
                            <img src="frontend/img/partner-img/3.png" alt="">
                        </div>
                    </div>

                    <!-- Partner Logo -->
                    <div class="col-6 col-sm-4 col-lg-3 col-xl-2">
                        <div class="partner-logo">
                            <img src="frontend/img/partner-img/4.png" alt="">
                        </div>
                    </div>

                    <!-- Partner Logo -->
                    <div class="col-6 col-sm-4 col-lg-3 col-xl-2">
                        <div class="partner-logo">
                            <img src="frontend/img/partner-img/5.png" alt="">
                        </div>
                    </div>

                    <!-- Partner Logo -->
                    <div class="col-6 col-sm-4 col-lg-3 col-xl-2">
                        <div class="partner-logo">
                            <img src="frontend/img/partner-img/6.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}

        <!-- Divider -->
        <div class="divider"></div>
    </section>

    <!-- Blog Section -->
    <section id="blog" class="blog-section bg-white">
        <!-- Divider -->
        <div class="divider"></div>

        <!-- Section Heading -->
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-12 col-xl-6">
                    <div class="section-heading text-center">
                        <span class="sub-title">Our Blogs</span>
                        <h2 class="mb-0">Recent Blog & Articles about Technology</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider-sm"></div>

        <div class="container">
            <div class="row g-4 g-xxl-5 justify-content-center">

                @if($news->count() > 0)
                    @foreach($news as $index => $article)
                        <!-- Blog Card -->
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="blog-card wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="{{ 400 + ($index * 200) }}ms">
                                <!-- Post Image -->
                                <div class="post-img" style="height: 320px; overflow: hidden;">
                                    <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <!-- Post Body -->
                                <div class="post-body">
                                    <div class="blog-meta flex-wrap d-flex align-items-center gap-2">
                                        @if($article->categories->count() > 0)
                                            <a href="#">{{ $article->categories->first()->name }}</a>
                                        @else
                                            <a href="#">Uncategorized</a>
                                        @endif
                                        <div class="dot"></div>
                                        <span>{{ $article->published_at?->format('d M Y') ?? 'No Date' }}</span>
                                    </div>
                                    <a class="post-title h4" href="{{ route('news.show', $article->slug) }}">{{ Str::limit($article->title, 60) }}</a>
                                    <a class="read-more-btn" href="{{ route('news.show', $article->slug) }}">Learn More <i class="ti ti-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <!-- No Articles Available -->
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <i class="ti ti-news" style="font-size: 4rem; color: #ddd;"></i>
                            </div>
                            <h4 class="text-muted">No Articles Available</h4>
                            <p class="text-muted">Check back soon for new articles and updates.</p>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </section>

    <!-- Contact Page Section -->
    <div id="contact" class="contact-page-section">
        <!-- Divider -->
        <div class="divider"></div>

        <!-- Section Heading -->
        <div class="container">
            <div class="row justify-content-center align-items-center g-4 g-xxl-5 mb-2">
                <div class="col-12 col-xl-6">
                    <div class="section-heading text-center">
                        <span class="sub-title">Contact Us</span>
                        <h2 class="mb-0">Get in Touch with Us</h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="divider-sm"></div>

        <div class="container">
            <div class="row g-4 justify-content-center">
                <!-- Contact Small Card -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="contact-small-card">
                        <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                            <path
                                d="M20.0084 19.8214C23.2007 14.812 22.7994 15.437 22.8914 15.3064C24.0537 13.6671 24.668 11.7376 24.668 9.72656C24.668 4.39336 20.3402 0 15 0C9.67723 0 5.33203 4.38469 5.33203 9.72656C5.33203 11.7363 5.95922 13.7163 7.15957 15.3777L9.99152 19.8214C6.96369 20.2867 1.81641 21.6734 1.81641 24.7266C1.81641 25.8396 2.54285 27.4257 6.00363 28.6617C8.42016 29.5247 11.6151 30 15 30C21.3296 30 28.1836 28.2145 28.1836 24.7266C28.1836 21.6728 23.0423 20.2877 20.0084 19.8214ZM8.62787 14.4108C8.6182 14.3957 8.60812 14.381 8.59758 14.3664C7.59873 12.9923 7.08984 11.3637 7.08984 9.72656C7.08984 5.33098 10.6293 1.75781 15 1.75781C19.3617 1.75781 22.9102 5.33256 22.9102 9.72656C22.9102 11.3664 22.4109 12.9397 21.4661 14.2776C21.3814 14.3893 21.8231 13.703 15 24.4095L8.62787 14.4108ZM15 28.2422C8.08629 28.2422 3.57422 26.21 3.57422 24.7266C3.57422 23.7295 5.89266 22.0901 11.0302 21.4511L14.2588 26.5173C14.4202 26.7705 14.6996 26.9238 14.9999 26.9238C15.3002 26.9238 15.5798 26.7705 15.7411 26.5173L18.9697 21.4511C24.1073 22.0901 26.4258 23.7295 26.4258 24.7266C26.4258 26.1974 21.9543 28.2422 15 28.2422Z"
                                fill="white" />
                            <path
                                d="M15 5.33203C12.5769 5.33203 10.6055 7.30342 10.6055 9.72656C10.6055 12.1497 12.5769 14.1211 15 14.1211C17.4231 14.1211 19.3945 12.1497 19.3945 9.72656C19.3945 7.30342 17.4231 5.33203 15 5.33203ZM15 12.3633C13.5461 12.3633 12.3633 11.1804 12.3633 9.72656C12.3633 8.27268 13.5461 7.08984 15 7.08984C16.4539 7.08984 17.6367 8.27268 17.6367 9.72656C17.6367 11.1804 16.4539 12.3633 15 12.3633Z"
                                fill="white" />
                        </svg>
                        </div>

                        <div>
                        <h4>Office Address</h4>
                        <p class="mb-0">Bekasi, Indonesia</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Small Card -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="contact-small-card">
                        <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                            <g clip-path="url(#clip0_1_18591)">
                                <path
                                    d="M15 0C6.72898 0 0 7.34089 0 16.3636V23.1819C0 23.5587 0.304965 23.8637 0.68184 23.8637C1.05872 23.8637 1.36368 23.5587 1.36368 23.1819V16.3636C1.36368 8.09259 7.48074 1.36362 15 1.36362C22.5193 1.36362 28.6364 8.09259 28.6364 16.3636V23.1819C28.6364 23.5587 28.9413 23.8637 29.3182 23.8637C29.6951 23.8637 30.0001 23.5587 30.0001 23.1819V16.3636C30 7.34089 23.2711 0 15 0Z"
                                    fill="white" />
                                <path
                                    d="M7.49931 16.3633H5.45386C3.94975 16.3633 2.72656 17.5864 2.72656 19.0906V27.2724C2.72656 28.7765 3.94968 29.9997 5.45386 29.9997H7.49931C7.87619 29.9997 8.18115 29.6947 8.18115 29.3179V17.0451C8.18115 16.6682 7.87619 16.3633 7.49931 16.3633ZM6.81754 28.636H5.45386C4.70215 28.636 4.09024 28.0241 4.09024 27.2724V19.0906C4.09024 18.3389 4.70215 17.727 5.45386 17.727H6.81747L6.81754 28.636Z"
                                    fill="white" />
                                <path
                                    d="M24.5476 16.3633H22.5022C22.1253 16.3633 21.8203 16.6682 21.8203 17.0451V29.3179C21.8203 29.6947 22.1253 29.9997 22.5022 29.9997H24.5476C26.0517 29.9997 27.2749 28.7766 27.2749 27.2724V19.0906C27.2749 17.5864 26.0517 16.3633 24.5476 16.3633ZM25.9112 27.2724C25.9112 28.0241 25.2993 28.636 24.5476 28.636H23.184V17.7269H24.5476C25.2993 17.7269 25.9112 18.3388 25.9112 19.0905V27.2724Z"
                                    fill="white" />
                            </g>
                            <defs>
                                <clipPath id="clip0_1_18591">
                                    <rect width="30" height="30" fill="white" />
                                </clipPath>
                            </defs>
                        </svg>
                        </div>

                        <div>
                        <h4>Call Us For Support:</h4>
                        <p class="mb-0">+62 821-2317-4607</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Small Card -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="contact-small-card">
                        <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30" fill="none">
                            <path
                                d="M26.6882 3.31229C26.3667 2.99274 25.9609 2.77132 25.5182 2.67393C25.0754 2.57653 24.6141 2.60718 24.1882 2.76229L4.18821 10.4623C3.71824 10.6316 3.31281 10.9433 3.02843 11.354C2.74406 11.7647 2.59491 12.2538 2.60179 12.7533C2.60867 13.2528 2.77123 13.7377 3.06681 14.1404C3.36239 14.5431 3.77626 14.8435 4.25071 14.9998L12.3382 17.6873L15.0007 25.7498C15.154 26.222 15.4507 26.6347 15.8495 26.9305C16.2483 27.2262 16.7293 27.3902 17.2257 27.3998C17.7098 27.4039 18.1833 27.2584 18.5816 26.9832C18.9798 26.708 19.2834 26.3165 19.4507 25.8623L27.1507 5.86229C27.3305 5.43841 27.3825 4.97124 27.3003 4.51821C27.2182 4.06518 27.0054 3.64603 26.6882 3.31229ZM25.4382 5.19979L17.7382 25.1998C17.7048 25.304 17.6374 25.3939 17.5467 25.4552C17.456 25.5164 17.3473 25.5453 17.2382 25.5373C17.1316 25.5323 17.029 25.495 16.9441 25.4302C16.8592 25.3654 16.7961 25.2763 16.7632 25.1748L14.1007 17.1873L18.7507 12.5873C18.8385 12.4995 18.9082 12.3952 18.9557 12.2805C19.0033 12.1657 19.0277 12.0427 19.0277 11.9185C19.0277 11.7943 19.0033 11.6714 18.9557 11.5566C18.9082 11.4419 18.8385 11.3376 18.7507 11.2498C18.6629 11.162 18.5586 11.0923 18.4439 11.0448C18.3291 10.9972 18.2062 10.9728 18.082 10.9728C17.9578 10.9728 17.8348 10.9972 17.72 11.0448C17.6053 11.0923 17.501 11.162 17.4132 11.2498L12.8257 15.8373L4.83821 13.1873C4.73666 13.1544 4.64756 13.0913 4.5828 13.0064C4.51803 12.9215 4.48067 12.8189 4.47571 12.7123C4.46986 12.6036 4.49965 12.4959 4.56056 12.4057C4.62148 12.3154 4.71019 12.2475 4.81321 12.2123L24.8132 4.51229C24.9091 4.47785 25.0129 4.4714 25.1123 4.49368C25.2118 4.51595 25.3028 4.56604 25.3749 4.63811C25.447 4.71017 25.497 4.80124 25.5193 4.90069C25.5416 5.00014 25.5351 5.10387 25.5007 5.19979H25.4382Z"
                                fill="white" />
                        </svg>
                        </div>

                        <div>
                        <h4>Email Us Anytime:</h4>
                        <p class="mb-0">info@kainnova.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </div>

    <!-- CTA Wrapper -->
    <div class="cta-wrapper bg-img" style="background-image: url(frontend/img/core-img/grid.jpg)">
        <!-- Divider -->
        <div class="divider"></div>

        <div class="container">
            <div class="row g-4 g-xl-5 align-items-center">
                <div class="col-12 col-lg-6 col-xl-7">
                    <h2 class="mb-0 wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="400ms">Start Building Your
                        Business Now</h2>
                </div>

                <div class="col-12 col-lg-6 col-xl-5">
                    <p class="wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="600ms">Communicate your pricing
                        clearly and transparently to build trust with your customers. Hidden fees or
                        unclear pricing structures can lead to dissatisfaction.</p>
                    <a href="pricing.html" class="btn btn-primary btn-hover-border wow fadeInUp"
                        data-wow-duration="1000ms" data-wow-delay="800ms">Get Started <i
                            class="ti ti-arrow-up-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </div>
@endsection
