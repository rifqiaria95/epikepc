<div class="swiper-slide">
    <div class="single-testi-slider-item">
        <div class="rating">
            <i class="fa-solid fa-star-sharp"></i>
            <i class="fa-solid fa-star-sharp"></i>
            <i class="fa-solid fa-star-sharp"></i>
            <i class="fa-solid fa-star-sharp"></i>
            <i class="fa-solid fa-star-sharp"></i>
        </div>
        <p>&ldquo; {{ $testimonial->testimoni }} &rdquo;</p>
        <div class="author-info d-flex align-items-center">
            <img src="{{ $testimonial->gambar_url }}" alt="{{ $testimonial->nama }}">
            <h5>{{ $testimonial->nama }}<span>{{ $testimonial->instansi }}</span></h5>
        </div>
        <img src="{{ asset('frontend/img/testimonial/testi-shap-1.png') }}" alt="" class="shap-icon">
    </div>
</div>
