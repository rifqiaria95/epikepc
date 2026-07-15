@php
    $contact = config('frontend_contact');
@endphp

<div class="tv-widget widget mb-30 wow itfadeUp" data-wow-duratoin=".9s" data-wow-delay=".3s">
    <div class="tv-widget-content">
        <h4>Need Help?</h4>
        <p>Need help? Contact us anytime — our team is ready to assist.</p>
        <div class="widget-contact-wrap-area mt-40">
            <div class="widget-contact-wrap d-flex align-items-center">
                <div class="icon">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <div class="contact-info">
                    <p>Phone</p>
                    <h5><a href="{{ $contact['phone_href'] }}">{{ $contact['phone'] }}</a></h5>
                </div>
            </div>
            <div class="widget-contact-wrap d-flex align-items-center">
                <div class="icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div class="contact-info">
                    <p>Email</p>
                    <h5><a href="{{ $contact['email_href'] }}">{{ $contact['email'] }}</a></h5>
                </div>
            </div>
            <div class="widget-contact-wrap d-flex align-items-center">
                <div class="icon">
                    <i class="fa-solid fa-location-dot"></i>
                </div>
                <div class="contact-info">
                    <p>Office Address</p>
                    <h5><a href="{{ $contact['address_href'] }}">{{ $contact['address'] }}</a></h5>
                </div>
            </div>
        </div>
    </div>
</div>
