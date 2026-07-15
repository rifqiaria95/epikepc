@php
    $contact = config('frontend_contact');
@endphp
<footer class="footer primary-bg">
    <div class="container">
        <div class="footer_main d-flex flex-wrap justify-content-md-between col-12">
            <div class="footer_main-block col-sm-12 col-xl-auto">
                <figure class="logo-box">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('frontend/img/logo-2.png') }}" alt="EPIKEPC" style="width: 180px;">
                    </a>
                </figure>
                <p class="footer_main-block_subtitle footer_main-block_subtitle--brand">
                    A trusted engineering and construction company delivering quality infrastructure solutions across Indonesia.
                </p>
            </div>
            <div class="footer_main-block col-12 col-sm-6 col-md-auto">
                <h4 class="footer_main-block_title">Contact</h4>
                <div class="group-wrapper d-flex justify-content-start">
                    <i class="icon-call icon"></i>
                    <div class="group d-flex flex-column">
                        <a href="{{ $contact['phone_href'] }}">{{ $contact['phone'] }}</a>
                    </div>
                </div>
                <div class="group-wrapper d-flex justify-content-start">
                    <i class="icon-location icon"></i>
                    <div class="group d-flex flex-column">
                        <span>{{ $contact['address'] }}</span>
                    </div>
                </div>
            </div>
            <div class="footer_main-block col-12 col-sm-6 col-md-auto">
                <h4 class="footer_main-block_title">Company</h4>
                <ul class="footer_main-block_nav">
                    <li class="list-item">
                        <a class="link d-inline-flex align-items-center" href="{{ route('frontend.about.index') }}">About</a>
                    </li>
                    <li class="list-item">
                        <a class="link d-inline-flex align-items-center" href="{{ route('frontend.services.index') }}">Services</a>
                    </li>
                    <li class="list-item">
                        <a class="link d-inline-flex align-items-center" href="{{ route('frontend.projects.index') }}">Projects</a>
                    </li>
                    <li class="list-item">
                        <a class="link d-inline-flex align-items-center" href="{{ route('frontend.news.index') }}">News</a>
                    </li>
                    <li class="list-item">
                        <a class="link d-inline-flex align-items-center" href="{{ route('frontend.team.index') }}">Team</a>
                    </li>
                    <li class="list-item">
                        <a class="link d-inline-flex align-items-center" href="{{ route('frontend.gallery.index') }}">Gallery</a>
                    </li>
                </ul>
            </div>
            <div class="footer_main-block col-12 col-md-auto">
                <h4 class="footer_main-block_title">Newsletter</h4>
                <p class="footer_main-block_subtitle footer_main-block_subtitle--newsletter">
                    Get the latest updates on our projects and services.
                </p>
                <form class="footer_main-block_form d-flex flex-wrap flex-sm-nowrap" data-type="newsletter" action="#" method="POST" name="newsletterForm" id="newsletterForm">
                    <input class="field required" name="newsletterEmail" id="newsletterEmail" type="email" placeholder="Email" data-type="email" />
                    <button class="btn btn--submit btn--static" type="submit">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer_secondary col-12 d-flex flex-wrap align-items-center justify-content-center justify-content-md-between">
            <p class="footer_secondary-copyright">
                <span>&copy; {{ date('Y') }} EPIKEPC</span>
                <span>All rights reserved</span>
            </p>
            <ul class="socials d-flex align-items-center justify-content-start socials--alt">
                <li class="socials_item">
                    <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer"><i class="icon-facebook"></i></a>
                </li>
                <li class="socials_item">
                    <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer"><i class="icon-instagram"></i></a>
                </li>
                <li class="socials_item">
                    <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer"><i class="icon-twitter"></i></a>
                </li>
                <li class="socials_item">
                    <a class="socials_item-link" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact['phone']) }}" target="_blank" rel="noopener noreferrer"><i class="icon-whatsapp"></i></a>
                </li>
            </ul>
        </div>
    </div>
</footer>
