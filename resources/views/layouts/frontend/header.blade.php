<!-- Header Area-->
<header class="header-area style-two">
    <div class="header-top">
        <div class="container h-100 d-flex align-items-center justify-content-between">
            <!-- Left Side -->
            <div class="left-side d-flex align-items-center gap-4 gap-lg-5">
                <div class="d-flex align-items-center gap-2 text-white">
                    <i class="ti ti-mail-filled"></i>
                    <span class="d-none d-lg-block">info@kainnova.com</span>
                </div>
                <div class="d-flex align-items-center gap-2 text-white">
                    <i class="ti ti-map-pin-filled"></i>
                    <span class="d-none d-lg-block">Bekasi, Indonesia</span>
                </div>
                <div class="d-flex align-items-center gap-2 text-white">
                    <i class="ti ti-phone"></i>
                    <span class="d-none d-lg-block">+62 821-2317-4607</span>
                </div>
            </div>

            <!-- Right Side -->
            <div class="right-side">
                <div class="social-nav d-flex align-items-center gap-3">
                    <a href="#">
                        <i class="ti ti-brand-facebook"></i>
                    </a>
                    <a href="#">
                        <i class="ti ti-brand-x"></i>
                    </a>
                    <a href="#">
                        <i class="ti ti-brand-linkedin"></i>
                    </a>
                    <a href="#">
                        <i class="ti ti-brand-instagram"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Navbar Brand -->
            <a class="navbar-brand" href="/">
                <img src="{{ url('/frontend/img/core-img/logo.png') }}" alt="Kainnova Digital Solutions"
                    style="width: 60px;">
                <span class="app-brand-text demo menu-text fw-bold">Kainnova</span>
            </a>

            <!-- Navbar Toggler -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#softoraNav"
                aria-controls="softoraNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="ti ti-category"></i>
            </button>

            <!-- Navbar Nav -->
            <div class="collapse navbar-collapse justify-content-between" id="softoraNav">
                <ul class="navbar-nav navbar-nav-scroll">
                    @php
                        // Deteksi apakah sedang di landing page
                        $isLandingPage = request()->is('/');
                    @endphp
                    <li class="softora-dd">
                        <a href="{{ $isLandingPage ? '#home' : url('/#home') }}">Home</a>
                    </li>
                    <li class="softora-dd">
                        <a href="{{ $isLandingPage ? '#services' : url('/#services') }}">Services</a>
                    </li>
                    <li class="softora-dd">
                        <a href="{{ $isLandingPage ? '#blog' : url('/#blog') }}">Updates</a>
                    </li>
                    <li class="softora-dd">
                        <a href="{{ $isLandingPage ? '#about' : url('/#about') }}">About</a>
                    </li>
                    <li class="softora-dd">
                        <a href="{{ $isLandingPage ? '#contact' : url('/#contact') }}">Contact</a>
                    </li>
                </ul>

                <div class="d-flex align-items-center mt-4 mt-lg-0">
                    <!-- Search Button -->
                    <div class="header-search-btn" id="searchButton">
                        <button class="btn">
                            <i class="ti ti-search"></i>
                        </button>
                    </div>

                    <!-- Button -->
                    <a href="https://wa.me/6282123174607" class="btn btn-primary" target="_blank"
                        rel="noopener noreferrer">
                        Get in Touch <i class="ti ti-arrow-up-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</header>
