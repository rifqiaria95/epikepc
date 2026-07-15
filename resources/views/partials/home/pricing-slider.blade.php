<div class="tv-pricing-slider-wrap p-relative">
    <div class="row align-items-end mb-40 g-4">
        <div class="col-lg-8">
            <div class="tv-section-title-box mb-0">
                <span class="tv-section-subtitle tv-spltv-text tv-spltv-in-right">Pricing Plans</span>
                <h4 class="tv-section-title pb-20 tv-spltv-text tv-spltv-in-right mb-0">Internet Plans That Fit<br> Your Budget & Needs</h4>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="tv-pricing-arrow-box d-flex justify-content-lg-end justify-content-center gap-3">
                <button type="button" class="tv-pricing-prev" aria-label="Previous plan">
                    <i class="fa-light fa-arrow-left-long"></i>
                </button>
                <button type="button" class="tv-pricing-next" aria-label="Next plan">
                    <i class="fa-light fa-arrow-right-long"></i>
                </button>
            </div>
        </div>
    </div>

    @if ($pricingPlans->isNotEmpty())
        <div class="swiper-container tv-pricing-slider-active">
            <div class="swiper-wrapper">
                @foreach ($pricingPlans as $plan)
                    @include('partials.home.pricing-card', [
                        'plan' => $plan,
                        'slider' => true,
                        'featureLimit' => 7,
                    ])
                @endforeach
            </div>
            <div class="tv-pricing-pagination swiper-pagination"></div>
        </div>
    @else
        <div class="text-center">
            <p class="text-muted mb-0">No pricing plans are available at the moment.</p>
        </div>
    @endif
</div>

@push('styles')
    <style>
        .tv-pricing-slider-wrap {
            overflow: hidden;
        }

        .tv-pricing-slider-active {
            overflow: visible;
            padding-bottom: 8px;
        }

        .tv-pricing-slider-active .swiper-slide {
            height: auto;
            display: flex;
        }

        .pricing-slide-card {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-bottom: 0;
            min-height: 100%;
        }

        .pricing-slide-card h3.price {
            font-size: clamp(32px, 3vw, 48px);
            padding-bottom: 28px;
        }

        .pricing-slide-card .pricing-feature-list {
            list-style: none;
            margin: 0;
            padding: 0;
            margin-top: 28px;
            flex: 1 1 auto;
        }

        .pricing-slide-card .pricing-feature-list li {
            margin-top: 14px;
            color: var(--tv-heading-primary);
            font-size: 16px;
            line-height: 1.5;
        }

        .pricing-slide-card .pricing-feature-list li i {
            margin-right: 8px;
            color: var(--tv-theme-1);
        }

        .pricing-slide-card.active .pricing-feature-list li i {
            color: var(--tv-common-white);
        }

        .pricing-slide-card .pricing-feature-more {
            opacity: 0.85;
            font-style: italic;
        }

        .pricing-slide-card .price-box-btn {
            margin-top: 32px;
        }

        .tv-pricing-arrow-box button {
            padding: 0;
            height: 56px;
            width: 56px;
            line-height: 56px;
            font-size: 22px;
            transition: 0.3s;
            border-radius: 50%;
            color: var(--tv-theme-1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: var(--tv-common-white);
            border: 1px solid rgba(1, 95, 201, 0.15);
        }

        .tv-pricing-arrow-box button:hover,
        .tv-pricing-arrow-box button.swiper-button-disabled {
            opacity: 1;
        }

        .tv-pricing-arrow-box button:hover {
            color: var(--tv-common-white);
            background-color: var(--tv-theme-1);
            border-color: var(--tv-theme-1);
        }

        .tv-pricing-arrow-box button.swiper-button-disabled {
            opacity: 0.45;
            cursor: not-allowed;
            pointer-events: none;
        }

        .tv-pricing-pagination {
            position: relative;
            margin-top: 36px;
        }

        .tv-pricing-pagination .swiper-pagination-bullet {
            width: 10px;
            height: 10px;
            background: rgba(1, 95, 201, 0.25);
            opacity: 1;
            transition: 0.3s;
        }

        .tv-pricing-pagination .swiper-pagination-bullet-active {
            width: 28px;
            border-radius: 999px;
            background: var(--tv-theme-1);
        }

        @media (max-width: 991px) {
            .tv-pricing-slider-wrap .tv-section-title {
                text-align: center;
            }

            .tv-pricing-slider-wrap .tv-section-subtitle {
                display: inline-block;
            }
        }

        @media (max-width: 575px) {
            .pricing-slide-card {
                padding: 32px 24px;
            }

            .pricing-slide-card .tag {
                top: 72px;
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var pricingSliderEl = document.querySelector('.tv-pricing-slider-active');

            if (!pricingSliderEl || typeof Swiper === 'undefined') {
                return;
            }

            new Swiper('.tv-pricing-slider-active', {
                speed: 700,
                loop: {{ $pricingPlans->count() > 3 ? 'true' : 'false' }},
                slidesPerView: 1,
                spaceBetween: 20,
                grabCursor: true,
                watchOverflow: true,
                breakpoints: {
                    576: {
                        slidesPerView: 1,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 24,
                    },
                    1200: {
                        slidesPerView: 3,
                        spaceBetween: 28,
                    },
                },
                navigation: {
                    prevEl: '.tv-pricing-prev',
                    nextEl: '.tv-pricing-next',
                },
                pagination: {
                    el: '.tv-pricing-pagination',
                    clickable: true,
                },
            });
        });
    </script>
@endpush
