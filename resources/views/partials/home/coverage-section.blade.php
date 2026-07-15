@php
    $coverageStats = app(\App\Services\CoverageCheckService::class)->getStats();
@endphp

<div class="tv-blog-area pt-130 pb-130" id="coverage-area">
    <div class="container">
        <div class="row align-items-end mb-60">
            <div class="col-xl-7 col-lg-7">
                <div class="tv-section-title-box">
                    <span class="tv-section-subtitle tv-spltv-text tv-spltv-in-right">Coverage Area</span>
                    <h4 class="tv-section-title pb-20 tv-spltv-text tv-spltv-in-right">Check If Your Location<br> Is Covered</h4>
                    <p>Enter neighborhood, village, or housing name to check SFX NET network availability in your area.</p>
                </div>
            </div>
            <div class="col-xl-5 col-lg-5">
                <div class="row g-3">
                    <div class="col-4 text-center">
                        <div class="single-couter-wrap d-flex align-items-center justify-content-center">
                            <div class="inner">
                                <h5>{{ $coverageStats['kabupaten'] }}+</h5>
                                <h6>Regencies</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="single-couter-wrap d-flex align-items-center justify-content-center">
                            <div class="inner">
                                <h5>{{ $coverageStats['kelurahan'] }}+</h5>
                                <h6>Villages</h6>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="single-couter-wrap d-flex align-items-center justify-content-center">
                            <div class="inner">
                                <h5>{{ $coverageStats['coverage_points'] }}+</h5>
                                <h6>Coverage Points</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">
                <div class="tv-contact-wrap coverage-check-card wow itfadeUp" data-wow-delay=".2s">
                    <div class="coverage-check-card__header mb-30">
                        <h4 class="tv-section-title mb-10">Check Your Location</h4>
                        <p class="mb-0">Type address, neighborhood, village, or housing name.</p>
                    </div>

                    <form id="coverage-check-form" action="#" method="post" novalidate>
                        @csrf
                        <div class="row g-3 align-items-stretch">
                            <div class="col-lg-9">
                                <div class="coverage-autocomplete-wrap">
                                    <div class="tv-contact-input-box mb-0">
                                        <input type="text" id="coverage-location-input" name="location"
                                            placeholder="e.g. Meletan, Sawahan, Jakarta Selatan..." autocomplete="off"
                                            required aria-autocomplete="list" aria-controls="coverage-suggest-list"
                                            aria-expanded="false">
                                        <input type="hidden" id="coverage-location-lat" name="lat">
                                        <input type="hidden" id="coverage-location-lng" name="lng">
                                    </div>
                                    <div id="coverage-suggest-list" class="coverage-suggest-list d-none" role="listbox"></div>
                                </div>
                            </div>
                            <div class="col-lg-3">
                                <button type="submit" class="tv-btn-primary w-100 coverage-check-submit-btn" id="coverage-check-submit">
                                    <span class="btn-wrap">
                                        <span class="btn-text1"><i class="fa-solid fa-magnifying-glass me-2"></i>Search</span>
                                        <span class="btn-text2"><i class="fa-solid fa-magnifying-glass me-2"></i>Search</span>
                                    </span>
                                </button>
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-6">
                                <button type="button" class="coverage-action-btn w-100" id="coverage-use-current-location">
                                    <i class="fa-solid fa-location-crosshairs me-2"></i>Use My Current Location
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="coverage-action-btn w-100" id="coverage-open-map">
                                    <i class="fa-solid fa-map-location-dot me-2"></i>Select Location on Map
                                </button>
                            </div>
                        </div>
                    </form>

                    <div id="coverage-check-result" class="coverage-check-result mt-30 d-none" role="status" aria-live="polite"></div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
    <div class="modal fade" id="coverageMapModal" tabindex="-1" aria-labelledby="coverageMapModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl coverage-map-dialog">
            <div class="modal-content coverage-map-modal">
                <div class="modal-header coverage-map-modal__header">
                    <h5 class="modal-title" id="coverageMapModalLabel">Select Location on Map</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body coverage-map-modal__body">
                    <div class="coverage-map-inner">
                        <div id="coverage-map" class="coverage-map"></div>
                    </div>
                    <div class="coverage-map-selected">
                        <p class="mb-2 text-muted">Click on the map to select a location.</p>
                        <p class="mb-0" id="coverage-map-selected-label">No location selected yet.</p>
                    </div>
                </div>
                <div class="modal-footer coverage-map-modal__footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="tv-btn-primary" id="coverage-map-confirm" disabled>
                        <span class="btn-wrap">
                            <span class="btn-text1">Use This Location</span>
                            <span class="btn-text2">Use This Location</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        #coverage-area {
            scroll-margin-top: 120px;
        }

        .coverage-check-card {
            background: var(--tv-common-white);
        }

        .coverage-check-card__header .tv-section-title {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .coverage-check-card__header p {
            color: var(--tv-text-body);
        }

        .coverage-check-card .tv-contact-input-box input {
            width: 100%;
            height: 60px;
            border: 1px solid rgba(23, 36, 38, 0.1);
            background-color: var(--tv-gray-1);
            border-radius: 10px 0 10px 0;
            padding: 0 24px;
            color: var(--tv-heading-primary);
        }

        .coverage-check-card .tv-contact-input-box input:focus {
            outline: none;
            border-color: var(--tv-theme-1);
            background-color: var(--tv-common-white);
        }

        .coverage-check-card .coverage-check-submit-btn {
            min-height: 60px;
        }

        .coverage-check-result {
            border-radius: 10px 0 10px 0;
            padding: 20px 24px;
            background: var(--tv-gray-1);
            border: 1px solid rgba(23, 36, 38, 0.08);
        }

        .coverage-check-result.is-covered {
            background: rgba(25, 135, 84, 0.08);
            border-color: rgba(25, 135, 84, 0.25);
        }

        .coverage-check-result.is-not-covered {
            background: rgba(220, 53, 69, 0.06);
            border-color: rgba(220, 53, 69, 0.2);
        }

        .coverage-check-result h5 {
            color: var(--tv-heading-primary);
            margin-bottom: 8px;
        }

        .coverage-check-result p {
            color: var(--tv-text-body);
            margin-bottom: 0;
        }

        .coverage-suggestion-list {
            margin: 12px 0 0;
            padding-left: 18px;
            color: var(--tv-text-body);
        }

        .coverage-suggestion-list li + li {
            margin-top: 6px;
        }

        #coverage-check-submit[disabled],
        .coverage-action-btn[disabled] {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .coverage-autocomplete-wrap {
            position: relative;
        }

        .coverage-suggest-list {
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            z-index: 20;
            background: var(--tv-common-white);
            border: 1px solid rgba(23, 36, 38, 0.1);
            border-radius: 10px 0 10px 0;
            box-shadow: 0 12px 30px rgba(23, 36, 38, 0.12);
            max-height: 280px;
            overflow-y: auto;
        }

        .coverage-suggest-item {
            display: block;
            width: 100%;
            text-align: left;
            border: 0;
            background: transparent;
            padding: 12px 18px;
            border-bottom: 1px solid rgba(23, 36, 38, 0.06);
            color: var(--tv-heading-primary);
        }

        .coverage-suggest-item:last-child {
            border-bottom: 0;
        }

        .coverage-suggest-item:hover,
        .coverage-suggest-item.is-active {
            background: rgba(1, 95, 201, 0.08);
        }

        .coverage-suggest-item strong {
            display: block;
            font-size: 15px;
            margin-bottom: 2px;
        }

        .coverage-suggest-item small {
            color: var(--tv-text-body);
        }

        .coverage-action-btn {
            min-height: 54px;
            border: 1px solid rgba(23, 36, 38, 0.12);
            background: var(--tv-common-white);
            border-radius: 10px 0 10px 0;
            color: var(--tv-heading-primary);
            font-weight: 600;
            transition: 0.2s ease;
        }

        .coverage-action-btn:hover {
            border-color: var(--tv-theme-1);
            color: var(--tv-theme-1);
        }

        #coverageMapModal {
            padding-left: 16px;
            padding-right: 16px;
        }

        #coverageMapModal .modal-dialog.coverage-map-dialog {
            width: 100%;
            max-width: min(1140px, calc(100vw - 32px));
            margin-left: auto;
            margin-right: auto;
        }

        #coverageMapModal .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        #coverageMapModal .btn-close {
            position: static;
            top: auto;
            right: auto;
            width: 1em;
            height: 1em;
            margin: 0;
            padding: 0;
            border: 0;
            border-radius: 0;
            background-color: transparent !important;
            transform: none;
        }

        #coverageMapModal .btn-close:hover {
            transform: none;
        }

        .coverage-map-modal__header,
        .coverage-map-modal__body,
        .coverage-map-modal__footer {
            padding-left: 24px;
            padding-right: 24px;
        }

        .coverage-map-modal__header {
            padding-top: 20px;
            padding-bottom: 16px;
        }

        .coverage-map-modal__body {
            padding-bottom: 24px;
        }

        .coverage-map-modal__footer {
            padding-top: 16px;
            padding-bottom: 20px;
        }

        .coverage-map-inner {
            overflow: hidden;
            border-radius: 10px 0 10px 0;
            border: 1px solid rgba(23, 36, 38, 0.08);
        }

        .coverage-map-selected {
            padding-top: 16px;
        }

        .coverage-map {
            width: 100%;
            height: 420px;
        }

        .coverage-map-modal .modal-footer .tv-btn-primary[disabled] {
            opacity: 0.65;
            pointer-events: none;
        }

        @media (min-width: 768px) {
            #coverageMapModal {
                padding-left: 24px;
                padding-right: 24px;
            }

            #coverageMapModal .modal-dialog.coverage-map-dialog {
                max-width: min(1140px, calc(100vw - 48px));
            }

            .coverage-map-modal__header,
            .coverage-map-modal__body,
            .coverage-map-modal__footer {
                padding-left: 32px;
                padding-right: 32px;
            }
        }

        @media (max-width: 991px) {
            .coverage-check-card {
                padding: 40px 28px;
            }

            .coverage-check-card__header .tv-section-title {
                font-size: 26px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.coverageCheckConfig = {
            endpoints: {
                check: @json(route('frontend.coverage.check')),
                suggest: @json(route('frontend.coverage.suggest')),
                reverse: @json(route('frontend.coverage.reverse')),
            },
            map: @json(config('coverage_areas.map')),
            csrfToken: @json(csrf_token()),
        };
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="{{ asset('frontend/js/coverage-check.js') }}"></script>
@endpush
