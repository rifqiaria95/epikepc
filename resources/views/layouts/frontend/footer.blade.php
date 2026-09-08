@php
    $contact = config('frontend_contact');
@endphp
<style>
    .footer_main--compact {
        justify-content: flex-start !important;
        gap: 40px 56px;
    }

    .footer_main--compact .footer_main-block--brand {
        max-width: 300px;
        flex: 0 1 300px;
    }

    .footer_main--compact .footer_main-block--contact,
    .footer_main--compact .footer_main-block--company {
        flex: 0 0 auto;
    }

    .footer_main--compact .footer_company-nav {
        display: flex;
        align-items: flex-start;
        column-gap: 40px;
    }

    .footer_main--compact .footer_company-nav .footer_main-block_nav {
        min-width: 7.5rem;
        margin: 0;
    }

    @media (max-width: 767.98px) {
        .footer_main--compact {
            gap: 32px 24px;
        }

        .footer_main--compact .footer_main-block--brand {
            max-width: 100%;
            flex-basis: 100%;
        }

        .footer_main--compact .footer_company-nav {
            column-gap: 28px;
        }
    }
</style>
<footer class="footer primary-bg">
    <div class="container">
        <div class="footer_main footer_main--compact d-flex flex-wrap col-12">
            <div class="footer_main-block footer_main-block--brand col-sm-12 col-xl-auto">
                <figure class="logo-box">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('frontend/img/logo-3.png') }}" alt="EPIKEPC" style="width: 180px;">
                    </a>
                </figure>
                <p class="footer_main-block_subtitle footer_main-block_subtitle--brand">
                    A trusted engineering and construction company delivering quality infrastructure solutions across Indonesia.
                </p>
            </div>
            <div class="footer_main-block footer_main-block--contact col-12 col-sm-6 col-md-auto">
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
            <div class="footer_main-block footer_main-block--company col-12 col-sm-6 col-md-auto">
                <h4 class="footer_main-block_title">Company</h4>
                <div class="footer_company-nav">
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
                    </ul>
                    <ul class="footer_main-block_nav">
                        <li class="list-item">
                            <a class="link d-inline-flex align-items-center" href="{{ route('frontend.careers.index') }}">Careers</a>
                        </li>
                        <li class="list-item">
                            <a class="link d-inline-flex align-items-center" href="{{ route('frontend.team.index') }}">Team</a>
                        </li>
                        <li class="list-item">
                            <a class="link d-inline-flex align-items-center" href="{{ route('frontend.gallery.index') }}">Gallery</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="footer_secondary col-12 d-flex flex-wrap align-items-center justify-content-center justify-content-md-between">
            <p class="footer_secondary-copyright" style="font-size: 12px;">
                <span>&copy; {{ date('Y') }} PT Energi Persada Inti Konstruksi</span>
                <span>All rights reserved</span>
            </p>
            <ul class="socials d-flex align-items-center justify-content-start socials--alt" style="font-size: 12px;">
                <li class="socials_item">
                    <a class="socials_item-link" href="https://www.instagram.com/epikepc/" target="_blank" rel="noopener noreferrer"><i class="icon-instagram"></i></a>
                </li>
                <li class="socials_item">
                    <a class="socials_item-link" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact['phone']) }}" target="_blank" rel="noopener noreferrer"><i class="icon-whatsapp"></i></a>
                </li>
            </ul>
        </div>
    </div>
</footer>
