@php
    $ig = $instagram ?? ['enabled' => false];
    $igHeading = trim((string) ($ig['heading'] ?? 'Follow Our Journey'));
    $igHeadingParts = preg_split('/\s+/', $igHeading, 2) ?: [$igHeading];
@endphp

@if (!empty($ig['enabled']) && (!empty($ig['has_feed']) || !empty($ig['has_stories'])))
    <section class="ig-section section" id="instagram" aria-labelledby="ig-heading">
        <div class="container">
            <div class="ig-header">
                <div class="ig-header__copy section_header">
                    <span class="subtitle">{{ $ig['eyebrow'] }}</span>
                    <h2 class="title" id="ig-heading">
                        <span class="highlight">{{ $igHeadingParts[0] }}</span>
                        @if (!empty($igHeadingParts[1]))
                            {{ $igHeadingParts[1] }}
                        @endif
                    </h2>
                    @if (!empty($ig['subtitle']))
                        <p class="text">{{ $ig['subtitle'] }}</p>
                    @endif
                </div>
                <a class="ig-follow"
                    href="{{ $ig['profile']['profile_url'] }}"
                    target="_blank"
                    rel="noopener noreferrer">
                    {{ $ig['cta_label'] }}
                </a>
            </div>

            @if (!empty($ig['has_stories']))
                <div class="ig-stories" role="list" aria-label="Instagram stories and highlights">
                    @foreach ($ig['rings'] ?? $ig['stories'] as $index => $ring)
                        @php
                            $opensViewer = ($ring['opens'] ?? 'viewer') === 'viewer';
                            $isLatest = !empty($ring['is_latest']);
                            $preview = $ring['preview_url'] ?? null;
                            $label = $ring['label'] ?? 'Story';
                        @endphp
                        @if ($opensViewer)
                            <button type="button"
                                class="ig-story"
                                role="listitem"
                                data-ig-story-open
                                data-ig-index="{{ $ring['story_index'] ?? 0 }}"
                                aria-haspopup="dialog"
                                aria-controls="ig-story-viewer">
                                <span class="ig-story__ring @if($isLatest) is-latest @endif">
                                    <span class="ig-story__avatar">
                                        <img src="{{ $preview ?: $ig['fallback_image'] }}"
                                            alt="{{ $ring['alt'] ?? $label }}"
                                            width="72"
                                            height="72"
                                            loading="lazy"
                                            decoding="async"
                                            data-ig-fallback="{{ $ig['fallback_image'] }}">
                                    </span>
                                    @if ($isLatest)
                                        <span class="ig-story__dot" aria-hidden="true"></span>
                                    @endif
                                </span>
                                <span class="ig-story__label">{{ $label }}</span>
                            </button>
                        @else
                            <a class="ig-story"
                                role="listitem"
                                href="{{ $ring['permalink'] ?: $ig['profile']['profile_url'] }}"
                                target="_blank"
                                rel="noopener noreferrer">
                                <span class="ig-story__ring">
                                    <span class="ig-story__avatar">
                                        <img src="{{ $preview ?: $ig['fallback_image'] }}"
                                            alt="{{ $ring['alt'] ?? $label }}"
                                            width="72"
                                            height="72"
                                            loading="lazy"
                                            decoding="async"
                                            data-ig-fallback="{{ $ig['fallback_image'] }}">
                                    </span>
                                </span>
                                <span class="ig-story__label">{{ $label }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif

            @if (!empty($ig['has_feed']))
                <div class="ig-feed" aria-label="Instagram feed">
                    @foreach ($ig['feed'] as $item)
                        <a class="ig-post"
                            href="{{ $item['permalink'] ?: $ig['profile']['profile_url'] }}"
                            target="_blank"
                            rel="noopener noreferrer">
                            <img src="{{ $item['preview_url'] ?: $ig['fallback_image'] }}"
                                alt="{{ $item['alt'] }}"
                                width="640"
                                height="640"
                                loading="lazy"
                                decoding="async"
                                data-ig-fallback="{{ $ig['fallback_image'] }}">

                            @if ($item['indicator'] === 'carousel')
                                <span class="ig-post__type" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M7 7h10v10H7V7zm12-2H9V3h12v12h-2V5zM3 9h2v12h12v2H3V9z"/></svg>
                                </span>
                                <span class="visually-hidden">Carousel</span>
                            @elseif ($item['indicator'] === 'video')
                                <span class="ig-post__type" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="18" height="18"><path fill="currentColor" d="M8 5v14l11-7L8 5z"/></svg>
                                </span>
                                <span class="visually-hidden">Video</span>
                            @endif

                            <span class="ig-post__overlay">
                                @if ($item['like_count'] !== null || $item['comments_count'] !== null)
                                    <span class="ig-post__stats">
                                        @if ($item['like_count'] !== null)
                                            <span>
                                                <svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true"><path fill="currentColor" d="M12 21s-7.2-4.4-9.3-8.2C1 9.7 3 6 6.4 6c2 0 3.3 1.2 3.9 2.2C11 7.2 12.3 6 14.3 6 17.7 6 19.7 9.7 21.3 12.8 19.2 16.6 12 21 12 21z"/></svg>
                                                {{ number_format($item['like_count']) }}
                                            </span>
                                        @endif
                                        @if ($item['comments_count'] !== null)
                                            <span>
                                                <svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true"><path fill="currentColor" d="M4 4h16v12H7l-3 3V4z"/></svg>
                                                {{ number_format($item['comments_count']) }}
                                            </span>
                                        @endif
                                    </span>
                                @endif
                                @if ($item['caption'] !== '')
                                    <span class="ig-post__caption">{{ $item['caption'] }}</span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="ig-more">
                <a href="{{ $ig['profile']['profile_url'] }}" target="_blank" rel="noopener noreferrer">
                    {{ $ig['view_more_label'] }}
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </section>

    @if (!empty($ig['has_live_stories']))
        <script>window.IG_STORIES = @json($ig['stories']);</script>
        <div class="ig-viewer" id="ig-story-viewer" hidden role="dialog" aria-modal="true" aria-labelledby="ig-viewer-title">
            <div class="ig-viewer__backdrop" data-ig-close></div>
            <div class="ig-viewer__dialog">
                <p id="ig-viewer-title" class="visually-hidden">Instagram story</p>
                <div class="ig-viewer__progress" data-ig-progress></div>
                <button type="button" class="ig-viewer__close" data-ig-close aria-label="Close stories">×</button>
                <button type="button" class="ig-viewer__nav ig-viewer__nav--prev" data-ig-prev aria-label="Previous story"></button>
                <button type="button" class="ig-viewer__nav ig-viewer__nav--next" data-ig-next aria-label="Next story"></button>
                <div class="ig-viewer__media" data-ig-hold>
                    <img data-ig-image alt="" hidden>
                    <video data-ig-video playsinline muted hidden></video>
                </div>
                <div class="ig-viewer__bar">
                    <p class="ig-viewer__caption" data-ig-caption></p>
                    <div class="ig-viewer__actions">
                        <button type="button" class="ig-viewer__mute" data-ig-mute hidden aria-label="Unmute">Unmute</button>
                        <a class="ig-viewer__cta" data-ig-permalink href="#" target="_blank" rel="noopener noreferrer">View on Instagram</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
@elseif (!empty($ig['enabled']) && empty($ig['last_synced_at']))
    <section class="ig-section ig-section--empty section" aria-labelledby="ig-heading-empty">
        <div class="container">
            <div class="section_header">
                <span class="subtitle">{{ $ig['eyebrow'] }}</span>
                <h2 class="title" id="ig-heading-empty">
                    <span class="highlight">{{ $igHeadingParts[0] }}</span>
                    @if (!empty($igHeadingParts[1]))
                        {{ $igHeadingParts[1] }}
                    @endif
                </h2>
                <p class="text">Follow EPIK EPC on Instagram for the latest projects and activities.</p>
            </div>
            <a class="ig-follow" href="{{ $ig['profile']['profile_url'] }}" target="_blank" rel="noopener noreferrer">
                {{ $ig['cta_label'] }}
            </a>
        </div>
    </section>
@endif
