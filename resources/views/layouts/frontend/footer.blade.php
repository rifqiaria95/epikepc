<!-- Footer -->
<footer class="footer-wrapper">
    <!-- Divider -->
    <div class="divider"></div>

    <div class="container">
       <div class="row g-5">
          <!-- Footer Card -->
          <div class="col-12 col-sm-6 col-lg-4">
             <div class="footer-card pe-lg-5">
                <a href="#" class="d-block mb-4">
                   <img src="{{ url('/frontend/img/core-img/logo.png') }}" alt="Kainnova Digital Solutions" style="width: 60px;">
                   <span class="app-brand-text demo menu-text fw-bold" style="font-size: 28px;">Kainnova</span>
                </a>
                <p class="mb-0">Best IT & Digital Solution For Your Business</p>
                <!-- Social Nav -->
                <div class="social-nav">
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

          <!-- Footer Card -->
          <div class="col-12 col-sm-6 col-lg">
             <div class="footer-card">
                <h5 class="mb-4">Quick Links</h5>

                <!-- Footer Nav -->
                <ul class="footer-nav">
                   @php
                      $isLandingPage = request()->is('/');
                   @endphp
                   <li><a href="{{ $isLandingPage ? '#about' : url('/#about') }}">About Us</a></li>
                   <li><a href="{{ $isLandingPage ? '#blog' : url('/#blog') }}">Updates</a></li>
                   <li><a href="{{ $isLandingPage ? '#contact' : url('/#contact') }}">Contact Us</a></li>
                </ul>
             </div>
          </div>

          <!-- Footer Card -->
          <div class="col-12 col-sm-6 col-lg">
             <div class="footer-card">
                <h5 class="mb-4">Services</h5>

                <!-- Footer Nav -->
                <ul class="footer-nav">
                   @forelse($services ?? [] as $service)
                      <li><a href="{{ route('frontend.detail-service', $service->id) }}">{{ $service->serviceType->name }}</a></li>
                   @empty
                      <li><a href="#">No services found</a></li>
                   @endforelse
                </ul>
             </div>
          </div>

          <!-- Footer Card -->
          <div class="col-12 col-sm-6 col-lg">
             <div class="footer-card">
                <h5 class="mb-4">Information</h5>

                <!-- Footer Nav -->
                <ul class="footer-nav">
                   <li><a href="#">Working Process</a></li>
                   <li><a href="#">Privacy Policy</a></li>
                   <li><a href="#">Terms &amp; Conditions</a></li>
                   <li><a href="#">Faqs</a></li>
                </ul>
             </div>
          </div>
       </div>
    </div>

    <!-- Divider -->
    <div class="divider"></div>

    <!-- Copyright Wrapper -->
    <div class="copyright-wrapper">
       <div class="container">
          <div class="row align-items-center">
             <!-- Copyright -->
             <div class="col-12 col-md-6">
                <p class="mb-3 mb-md-0 copyright">Copyright © <span id="year">{{ date('Y') }}</span> <a href="#">
                    Kainnova Digital Solutions</a>
                   All
                   rights
                   reserved.</p>
                </p>
             </div>

             <!-- Footer Bottom Nav -->
             <div class="col-12 col-md-6">
                <div class="footer-bottom-nav">
                   <a href="#">Privacy &amp; Terms</a>
                   <a href="#">FAQ</a>
                   <a href="#">Contact Us</a>
                </div>
             </div>
          </div>
       </div>
    </div>
 </footer>