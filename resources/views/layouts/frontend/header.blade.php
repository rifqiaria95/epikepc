@php
    $currentPage = View::yieldContent('page');
@endphp
<header class="header primary-bg" data-page="{{ $currentPage }}">
    <div class="header_navbar">
        <div class="container d-flex flex-wrap flex-lg-nowrap align-items-center justify-content-between">
            <figure class="logo-box">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('frontend/img/logo-3.png') }}" alt="EPIKEPC" style="width: 180px;">
                </a>
            </figure>
            <nav class="header_navbar-nav">
                <ul class="header_navbar-nav_list">
                    <li class="list-item">
                        <a class="link link--single {{ request()->is('/') ? 'current' : '' }}" href="{{ url('/') }}" data-page="home">Home</a>
                    </li>
                    <li class="list-item">
                        <a class="link link--single {{ request()->is('about') ? 'current' : '' }}" href="{{ route('frontend.about.index') }}" data-page="about">About</a>
                    </li>
                    <li class="list-item">
                        <a class="link link--single {{ request()->is('projects*') ? 'current' : '' }}" href="{{ route('frontend.projects.index') }}" data-page="projects">Projects</a>
                    </li>
                    <li class="list-item">
                        <a class="link link--single {{ request()->is('services*') ? 'current' : '' }}" href="{{ route('frontend.services.index') }}" data-page="services">Services</a>
                    </li>
                    <li class="list-item">
                        <a class="link link--single {{ request()->is('news*') ? 'current' : '' }}" href="{{ route('frontend.news.index') }}" data-page="blog">News</a>
                    </li>
                    <li class="list-item">
                        <a class="link link--single {{ request()->is('contact') ? 'current' : '' }}" href="{{ route('frontend.contact.index') }}" data-page="contacts">Contact</a>
                    </li>
                </ul>
            </nav>
            <button class="hamburger" type="button" aria-label="Open menu">
                <span class="line line--short"></span>
                <span class="line"></span>
                <span class="line line--short"></span>
                <span class="line"></span>
            </button>
        </div>
    </div>
    @yield('header_extension')
</header>
