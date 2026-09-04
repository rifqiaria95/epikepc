@php
    $certificateItems = collect($certificates ?? [])
        ->filter(fn ($item) => ! empty($item['image_url']))
        ->values()
        ->all();
@endphp

@if (count($certificateItems) > 0)
    @php
        $certGalleryConfig = [
            'gesture_threshold_px' => (int) config('certificates.gesture_threshold_px', 50),
        ];
    @endphp

    <section class="cert-section section" id="certificateGallery" aria-label="Certifications and awards"
        data-items='@json($certificateItems)'
        data-config='@json($certGalleryConfig)'>
        <div class="container">
            <div class="cert-stage" data-cert-stage>
                <div class="cert-mask" aria-hidden="true"></div>

                <div class="cert-viewport" data-cert-viewport tabindex="0"
                    aria-label="Certificate gallery. Drag or use arrow keys to browse, press Enter to preview a certificate.">
                    <div class="cert-track" data-cert-track>
                        @foreach ($certificateItems as $item)
                            <button type="button" class="cert-thumb" data-cert-id="{{ $item['id'] }}"
                                aria-label="View {{ $item['title'] }}, issued by {{ $item['issuer'] }}">
                                <span class="cert-thumb-frame">
                                    <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] }}"
                                        loading="{{ $loop->first ? 'eager' : 'lazy' }}" decoding="async">
                                </span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="cert-center" data-aos="fade-up">
                    <span class="section_header" style="display:block;">
                        <span class="subtitle">Certified Excellence</span>
                    </span>
                    <h2 class="cert-center__title">Our <span class="highlight">Certifications</span></h2>
                    <p class="cert-center__desc">
                        Recognized standards that reflect EPIKEPC's commitment to quality, safety, compliance, and professional engineering excellence.
                    </p>
                </div>
            </div>

            <div class="cert-controls" data-cert-controls>
                <button type="button" class="cert-arrow" data-cert-prev aria-label="Previous certificate">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 5 L8 12 L15 19"/></svg>
                </button>
                <ul class="cert-dots" data-cert-dots role="tablist" aria-label="Certificate pages"></ul>
                <button type="button" class="cert-arrow" data-cert-next aria-label="Next certificate">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 5 L16 12 L9 19"/></svg>
                </button>
            </div>

            <p class="cert-section__live" data-cert-live aria-live="polite"></p>
        </div>
    </section>

    <div class="cert-lightbox" id="certLightbox" role="dialog" aria-modal="true" aria-hidden="true" aria-label="Certificate preview">
        <div class="cert-lightbox__inner">
            <button type="button" class="cert-lightbox__close" data-lb-close aria-label="Close certificate preview">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 6 L18 18 M18 6 L6 18"/></svg>
            </button>
            <button type="button" class="cert-lightbox__nav cert-lightbox__prev" data-lb-prev aria-label="Previous certificate">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M15 5 L8 12 L15 19"/></svg>
            </button>
            <button type="button" class="cert-lightbox__nav cert-lightbox__next" data-lb-next aria-label="Next certificate">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 5 L16 12 L9 19"/></svg>
            </button>

            <div class="cert-lightbox__viewport" data-lb-viewport>
                <div class="cert-lightbox__track" data-lb-track></div>
            </div>

            <div class="cert-lightbox__caption">
                <div class="cert-lightbox__title" data-lb-title></div>
                <div class="cert-lightbox__meta" data-lb-meta></div>
                <div class="cert-lightbox__counter" data-lb-counter></div>
            </div>
        </div>
    </div>
@endif
