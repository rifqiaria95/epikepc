@extends('layouts.frontend.main')

@section('content')
    <!-- Breadcrumb Section -->
    <div class="breadcrumb-section bg-img" style="background-image: url('{{ asset('frontend/img/bg-img/pricing.jpg') }}');">
        <div class="container">
            <!-- Breadcrumb Content -->
            <div class="breadcrumb-content">
                <div class="divider"></div>
                <h2>Our Services</h2>
                <ul class="list-unstyled">
                    <li><a href="index.html">Home</a></li>
                    <li>Services</li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </div>

    <!-- Service Section -->
    <section class="service-section">
        <!-- Divider -->
        <div class="divider"></div>

        <div class="container">
            <div class="row justify-content-center">
                <!-- Section Heading -->
                <div class="col-12 col-md-7">
                    <div class="section-heading text-center">
                        <span class="sub-title">Our Services</span>
                        <h2 class="mb-0">We Provide Experts IT & Digital Services</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider-sm"></div>

        <div class="container">
            <div class="row g-4 g-xl-5">
                @forelse($services as $index => $service)
                    <!-- Service Card -->
                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="service-card">
                            <div class="shape">
                                <img src="{{ asset('frontend/img/core-img/shape7.png') }}" alt="">
                            </div>
                            <div class="service-thumb">
                                <a href="{{ route('frontend.detail-service', $service->id) }}" class="btn">Learn More <i class="ti ti-arrow-right"></i></a>
                                @if($service->getImageUrl())
                                    <img src="{{ $service->getImageUrl() }}" alt="{{ $service->title }}">
                                @else
                                    <img src="{{ asset('frontend/img/bg-img/34.png') }}" alt="">
                                @endif
                            </div>
                            <div class="service-content">
                                <h4 class="service-title">{{ $service->title }}</h4>
                                <p class="mb-0 text-uppercase"><span class="sub-title">{{ strtoupper($service->serviceType->type ?? 'IT') }}</span></p>
                            </div>
                            <div class="service-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>
                @empty
                    <!-- Fallback Service Cards jika tidak ada data -->
                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="service-card">
                            <div class="shape">
                                <img src="{{ asset('frontend/img/core-img/shape7.png') }}" alt="">
                            </div>
                            <div class="service-thumb">
                                <a href="#" class="btn">Learn More <i class="ti ti-arrow-right"></i></a>
                                <img src="{{ asset('frontend/img/bg-img/34.png') }}" alt="">
                            </div>
                            <div class="service-content">
                                <h4 class="service-title">Cyber Security</h4>
                                <p class="mb-0">Empower your business with our cutting-edge tailored solutions.</p>
                            </div>
                            <div class="service-number">01</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="service-card">
                            <div class="shape">
                                <img src="{{ asset('frontend/img/core-img/shape7.png') }}" alt="">
                            </div>
                            <div class="service-thumb">
                                <a href="#" class="btn">Learn More <i class="ti ti-arrow-right"></i></a>
                                <img src="{{ asset('frontend/img/bg-img/35.png') }}" alt="">
                            </div>
                            <div class="service-content">
                                <h4 class="service-title">Data Protection</h4>
                                <p class="mb-0">Empower your business with our cutting-edge tailored solutions.</p>
                            </div>
                            <div class="service-number">02</div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-xl-4">
                        <div class="service-card">
                            <div class="shape">
                                <img src="{{ asset('frontend/img/core-img/shape7.png') }}" alt="">
                            </div>
                            <div class="service-thumb">
                                <a href="#" class="btn">Learn More <i class="ti ti-arrow-right"></i></a>
                                <img src="{{ asset('frontend/img/bg-img/36.png') }}" alt="">
                            </div>
                            <div class="service-content">
                                <h4 class="service-title">Network Security</h4>
                                <p class="mb-0">Empower your business with our cutting-edge tailored solutions.</p>
                            </div>
                            <div class="service-number">03</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </section>

    <!-- CTA Wrapper -->
    <div class="cta-wrapper bg-img" style="background-image: url({{ asset('frontend/img/core-img/grid.jpg') }})">
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
