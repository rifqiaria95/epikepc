<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   
   {{-- Default Meta Tags (dapat di-override oleh halaman) --}}
   @hasSection('meta')
      @yield('meta')
   @else
      <meta name="description" content="Kainnova Digital Solutions - Solusi IT & Digital untuk mengembangkan bisnis Anda">
      <meta name="keywords" content="IT Solutions, Digital Services, Kainnova, Software Development, Indonesia">
   @endif

   <!-- Title & Favicon -->
   <title>@yield('title', 'Kainnova Digital Solutions | IT Solutions')</title>
   <link rel="shortcut icon" href="{{ url('/frontend/img/core-img/favicon.ico') }}" type="image/x-icon">

   <!-- Stylesheet -->
   <link rel="stylesheet" href="{{ url('/frontend/css/animate.css') }}">
   <link rel="stylesheet" href="{{ url('/frontend/css/tabler-icons.min.css') }}">
   <link rel="stylesheet" href="{{ url('/frontend/css/bootstrap.min.css') }}">
   <link rel="stylesheet" href="{{ url('/frontend/css/swiper-bundle.min.css') }}">
   <link rel="stylesheet" href="{{ url('/frontend/css/style.css') }}">
   
   {{-- Custom Styles per Halaman --}}
   @stack('styles')
   
   <!-- Smooth Scroll -->
   <style>
      html {
         scroll-behavior: smooth;
      }
   </style>
</head>

<body>
    <!-- Preloader -->
   <div class="preloader" id="preloader">
        <div class="spinner-grow" role="status">
        <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Search Form Overlay -->
    <div class="search-bg-overlay" id="searchOverlay"></div>

    <!-- Search Form Popup -->
    <div class="search-form-popup">
        <h2 class="mb-4">How can I help you, Today?</h2>
        <button type="button" class="close-btn" id="searchClose" aria-label="Close">
        <i class="ti ti-x"></i>
        </button>
        <form class="search-form">
        <input type="search" class="form-control" placeholder="Search...">
        <button type="submit" class="btn btn-primary">
            <i class="ti ti-search"></i> Search
        </button>
        </form>
    </div>

    @include('layouts/frontend.header')
    @yield('content')
    @include('layouts/frontend.footer')

   <!-- Cookie Alert -->
   <div class="cookiealert shadow-lg show">
      <p class="mb-4">We use cookies for the best experience on our website, for social media features and to anal
         traffic. accepting
         you agree to our use of cookies. Read <a href="#" target="_blank"> Cookies Policy.</a></p>
      <button class="btn btn-primary btn-sm acceptcookies" type="button" aria-label="Close">Accept</button>
   </div>

   <!-- Scroll To Top -->
   <button id="scrollTopButton" class="softora-scrolltop scrolltop-hide">
      <i class="ti ti-chevron-up"></i>
   </button>

   <!-- All JavaScript Files-->
   <script src="{{ url('/frontend/js/bootstrap.bundle.min.js') }}"></script>
   <script src="{{ url('/frontend/js/slideToggle.min.js') }}"></script>
   <script src="{{ url('/frontend/js/swiper-bundle.min.js') }}"></script>
   <script src="{{ url('/frontend/js/jarallax.min.js') }}"></script>
   <script src="{{ url('/frontend/js/index.js') }}"></script>
   <script src="{{ url('/frontend/js/cookiealert.js') }}"></script>
   <script src="{{ url('/frontend/js/imagesloaded.pkgd.min.js') }}"></script>
   <script src="{{ url('/frontend/js/isotope.pkgd.min.js') }}"></script>
   <script src="{{ url('/frontend/js/wow.min.js') }}"></script>
   <script src="{{ url('/frontend/js/active.js') }}"></script>

   {{-- Hybrid Navigation Script --}}
   <script>
      // Handle smooth scroll ketika redirect dari halaman detail dengan hash
      document.addEventListener('DOMContentLoaded', function() {
         // Cek apakah URL memiliki hash dan sedang di landing page
         if (window.location.hash && window.location.pathname === '/') {
            const hash = window.location.hash;
            const targetElement = document.querySelector(hash);
            
            if (targetElement) {
               // Delay sedikit untuk memastikan halaman sudah fully loaded
               setTimeout(function() {
                  const headerOffset = 100; // Offset untuk header navbar
                  const elementPosition = targetElement.getBoundingClientRect().top;
                  const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                  window.scrollTo({
                     top: offsetPosition,
                     behavior: 'smooth'
                  });
               }, 300);
            }
         }

         // Handle click pada menu link dari halaman detail (navbar dan footer)
         const menuLinks = document.querySelectorAll('.navbar-nav a[href^="/#"], .footer-nav a[href^="/#"]');
         menuLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
               const href = this.getAttribute('href');
               
               // Jika sedang di landing page, biarkan default behavior (smooth scroll)
               if (window.location.pathname === '/') {
                  return true;
               }
               
               // Jika tidak di landing page, redirect ke landing page dengan hash
               e.preventDefault();
               window.location.href = href;
            });
         });
      });
   </script>

   @stack('scripts')

</body>

</html>