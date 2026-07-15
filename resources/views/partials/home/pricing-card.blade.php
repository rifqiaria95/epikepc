@php
    $billingLabel = $plan->billing_period === 'year' ? '/Year' : '/Bulan';
    $planUrl = $plan->button_url ?: '#';
    $featureLimit = $featureLimit ?? null;
    $features = $featureLimit
        ? $plan->pricingFeatures->take($featureLimit)
        : $plan->pricingFeatures;
    $hasMoreFeatures = $featureLimit && $plan->pricingFeatures->count() > $featureLimit;
@endphp

@if (!empty($slider))
    <div class="swiper-slide">
        <div class="single-price-box pricing-slide-card h-100{{ $plan->is_popular ? ' active' : '' }}">
            @if ($plan->is_popular)
                <span class="tag">populer</span>
            @endif
            <h4 class="price-package">{{ $plan->name }}</h4>
            <h3 class="price">Rp {{ number_format((float) $plan->price, 0, ',', '.') }}
                <span>{{ $billingLabel }}</span>
            </h3>
            @if ($features->isNotEmpty())
                <ul class="pricing-feature-list">
                    @foreach ($features as $feature)
                        <li><i class="fa-regular fa-check"></i> {{ $feature->feature }}</li>
                    @endforeach
                    @if ($hasMoreFeatures)
                        <li class="pricing-feature-more">
                            <i class="fa-regular fa-ellipsis"></i> Dan fitur lainnya
                        </li>
                    @endif
                </ul>
            @endif
            <div class="price-box-btn mt-auto">
                <a href="{{ $planUrl }}" class="tv-btn-primary p-relative">
                    <span class="btn-wrap">
                        <span class="btn-text1">Select This Plan</span>
                        <span class="btn-text2">Select This Plan</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
@else
    <div class="col-xl-4 col-lg-6 col-md-6 wow itfadeUp" data-wow-duration=".9s"
        data-wow-delay="{{ number_format($delay ?? 0.3, 1) }}s">
        <div class="single-price-box mb-30{{ $plan->is_popular ? ' active' : '' }}">
            @if ($plan->is_popular)
                <span class="tag">populer</span>
            @endif
            <h4 class="price-package">{{ $plan->name }}</h4>
            <h3 class="price">Rp {{ number_format((float) $plan->price, 0, ',', '.') }}
                <span>{{ $billingLabel }}</span>
            </h3>
            @if ($plan->pricingFeatures->isNotEmpty())
                <ul>
                    @foreach ($plan->pricingFeatures as $feature)
                        <li><i class="fa-regular fa-check"></i> {{ $feature->feature }}</li>
                    @endforeach
                </ul>
            @endif
            <div class="price-box-btn">
                <a href="{{ $planUrl }}" class="tv-btn-primary p-relative">
                    <span class="btn-wrap">
                        <span class="btn-text1">Select This Plan</span>
                        <span class="btn-text2">Select This Plan</span>
                    </span>
                </a>
            </div>
        </div>
    </div>
@endif
