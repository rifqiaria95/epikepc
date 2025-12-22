@extends('layouts.frontend.main')

@section('meta')
    {{-- SEO Meta Tags --}}
    <meta name="title" content="{{ $service->title }} - {{ $service->serviceType->name ?? 'Service' }} | Kainnova">
    <meta name="description" content="{{ $service->subtitle ?? 'Layanan ' . $service->title . ' dari Kainnova - Solusi profesional untuk mengembangkan bisnis Anda.' }}">
    <meta name="keywords" content="{{ $service->title }}, {{ $service->serviceType->name ?? '' }}, Kainnova, IT Solutions, Digital Services, Indonesia">
    
    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $service->title }} - {{ $service->serviceType->name ?? 'Service' }} | Kainnova">
    <meta property="og:description" content="{{ $service->subtitle ?? 'Layanan ' . $service->title . ' dari Kainnova.' }}">
    @if($service->getImageUrl())
        <meta property="og:image" content="{{ $service->getImageUrl() }}">
    @else
        <meta property="og:image" content="{{ asset('frontend/img/core-img/logo.png') }}">
    @endif
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $service->title }} | Kainnova">
    <meta name="twitter:description" content="{{ $service->subtitle ?? 'Layanan ' . $service->title . ' dari Kainnova.' }}">

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@section('title', $service->title . ' - ' . ($service->serviceType->name ?? 'Service') . ' | Kainnova')

@section('content')
    <!-- Breadcrumb Section -->
    <div class="breadcrumb-section bg-img" style="background-image: url('{{ asset('frontend/img/bg-img/pricing.jpg') }}');">
        <div class="container">
            <!-- Breadcrumb Content -->
            <div class="breadcrumb-content">
                <div class="divider"></div>
                <h2>{{ $service->title }}</h2>
                <ul class="list-unstyled">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li><a href="{{ route('frontend.services.index') }}">Services</a></li>
                    <li>{{ $service->title }}</li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </div>
    <!-- Pricing Plan Section (if service details exist) -->
    @if($service->serviceDetails->count() > 0)
        <section class="pricing-section">
            <!-- Divider -->
            <div class="divider"></div>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8">
                        <div class="section-heading text-center">
                            <span class="sub-title">Detail Service</span>
                            <h2 class="mb-0">{{ $service->title }}</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divider-sm"></div>

            <div class="container">
                <div class="row g-4 align-items-center justify-content-center">
                    @foreach($service->serviceDetails as $index => $serviceDetail)
                        <!-- Pricing Card -->
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="pricing-card-two {{ $index === 1 ? 'active' : '' }} wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="{{ ($index + 1) * 200 }}ms">
                                <!-- Package Name and Price -->
                                <div class="packgae-name-price">
                                    <h4>{{ $serviceDetail->title }}</h4>
                                    @if($serviceDetail->subtitle)
                                        <p class="mb-0">{{ $serviceDetail->subtitle }}</p>
                                    @endif
                                    <div class="border-top mt-4 mb-3"></div>
                                    @if($serviceDetail->price)
                                        <h2 class="price-value mb-0">{{ $serviceDetail->price }}</h2>
                                    @endif
                                </div>
                                
                                @if($serviceDetail->description)
                                    <!-- Package Content -->
                                    <div class="packgae-content">
                                        {!! $serviceDetail->description !!}
                                    </div>
                                @endif

                                <!-- Button -->
                                <a href="#" class="btn shadow-sm {{ $index === 1 ? 'btn-primary' : 'btn-dark' }}">Choose Package <i
                                        class="ti ti-arrow-up-right"></i></a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Divider -->
            <div class="divider"></div>
        </section>
        

        <!-- CTA Wrapper -->
        <div class="cta-wrapper bg-img" style="background-image: url(assets/img/core-img/grid.jpg)">
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
                    <a href="pricing.html" class="btn btn-primary btn-hover-border wow fadeInUp" data-wow-duration="1000ms"
                        data-wow-delay="800ms">Get Started <i class="ti ti-arrow-up-right"></i></a>
                </div>
            </div>
            </div>

            <!-- Divider -->
            <div class="divider"></div>
        </div>
    @endif

    {{-- JSON-LD Structured Data for SEO --}}
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Service",
        "name": "{{ $service->title }}",
        "description": "{{ $service->subtitle ?? 'Layanan ' . $service->title . ' dari Kainnova' }}",
        @if($service->getImageUrl())
        "image": "{{ $service->getImageUrl() }}",
        @endif
        "provider": {
            "@type": "Organization",
            "name": "Kainnova Digital Solutions",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('frontend/img/core-img/logo.png') }}",
            "address": {
                "@type": "PostalAddress",
                "addressLocality": "Bekasi",
                "addressCountry": "ID"
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+62-821-2317-4607",
                "contactType": "customer service"
            }
        },
        "serviceType": "{{ $service->serviceType->name ?? 'IT Service' }}",
        "areaServed": {
            "@type": "Country",
            "name": "Indonesia"
        }
    }
    </script>
@endsection
