<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>@yield('title', 'EPIKEPC')</title>
    <meta name="description" content="@yield('meta_description', 'Trusted engineering, construction, and infrastructure services provider.')">
    @yield('meta')
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontend/img/logo-2.png') }}">
    <link rel="stylesheet preload" as="style" href="{{ asset('frontend/css/preload.min.css') }}" />
    <link rel="stylesheet preload" as="style" href="{{ asset('frontend/css/libs.min.css') }}" />
    {{-- Shared header_extension deco styles (same as About) --}}
    <style>
        .header_navbar .logo-box .site-logo {
            width: 110px;
            max-width: 100%;
            height: auto;
        }

        @media screen and (min-width: 991.98px) {
            .header_navbar .logo-box .site-logo {
                width: 180px;
            }
        }

        .header_navbar-nav .link--single.current,
        .header_navbar-nav .link--single.active {
            color: #FFdf08;
        }

        .header_extension .plan[data-role="deco"] {
            width: min(82vw, 1200px);
            max-width: 1200px;
            right: -6%;
            bottom: -20%;
            opacity: .32;
            transform: none;
            pointer-events: none;
        }
    </style>
    @stack('styles')
</head>
<body>
    @include('layouts.frontend.header')
    @yield('content')
    @include('layouts.frontend.footer')

    @stack('modals')

    <button id="scrollToTop" type="button" aria-label="Scroll to top">
        <i class="icon icon-arrow_right"></i>
    </button>

    <script src="{{ asset('frontend/js/common.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
