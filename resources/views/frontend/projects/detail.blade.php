@extends('layouts.frontend.main')

@section('title', ($project->title ?? 'Project Details') . ' | EPIKEPC')
@section('page', 'projects')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/single-project.min.css') }}" />
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Projects',
        'title'    => $project->title,
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Projects', 'url' => route('frontend.projects.index')],
            ['label' => Str::limit($project->title, 40)],
        ],
    ])
@endsection

@section('content')
    @php $contact = config('frontend_contact'); @endphp
    <!-- SINGLE PROJECT CONTENT START -->
    <main>
        <div class="about section-nopb">
            <div class="container">
                <div class="row g-0 justify-content-between flex-lg-nowrap">
                    <article class="about_article col-lg-7 col-xl-auto">
                        <div class="about_article-img">
                            <picture>
                                <source
                                    data-srcset="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    srcset="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    type="image/webp"
                                />
                                <img
                                    class="about_article-img_img lazy"
                                    data-src="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    src="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    alt="{{ $project->title }}"
                                />
                            </picture>
                        </div>
                        <h3 class="about_article-header">Project Description</h3>
                        <div class="about_article-text">
                            {!! $project->content ?? $project->excerpt !!}
                        </div>
                        @if ($project->challenge_solution)
                            <h3 class="about_article-header">Challenge &amp; Solution</h3>
                            <div class="about_article-text">{!! $project->challenge_solution !!}</div>
                        @endif
                        @if ($project->final_result)
                            <h3 class="about_article-header">Final Result</h3>
                            <div class="about_article-text">{!! $project->final_result !!}</div>
                        @endif
                        <ul class="about_article-list">
                            <li class="about_article-list_item d-flex align-items-center">
                                <i class="icon-check icon"></i>
                                Building the future through innovation
                            </li>
                            <li class="about_article-list_item d-flex align-items-center">
                                <i class="icon-check icon"></i>
                                Designed to excellence standards
                            </li>
                            <li class="about_article-list_item d-flex align-items-center">
                                <i class="icon-check icon"></i>
                                Finding solutions in every challenge
                            </li>
                            <li class="about_article-list_item d-flex align-items-center">
                                <i class="icon-check icon"></i>
                                Engineering the better way
                            </li>
                        </ul>
                    </article>
                    <div class="about_aside col-lg-5 col-xl-auto">
                        <div class="about_info about_aside-item">
                            <div class="wrapper d-flex flex-column justify-content-between align-items-start">
                                <div class="wrapper--helper">
                                    <h3 class="about_info-title title">Project Information</h3>
                                    <table class="about_info-table">
                                        <tbody>
                                            @if ($project->category)
                                                <tr class="about_info-table_row">
                                                    <td class="property">Category</td>
                                                    <td class="value">{{ $project->category }}</td>
                                                </tr>
                                            @endif
                                            @if ($project->client)
                                                <tr class="about_info-table_row">
                                                    <td class="property">Client</td>
                                                    <td class="value">{{ $project->client }}</td>
                                                </tr>
                                            @endif
                                            @if ($project->project_date)
                                                <tr class="about_info-table_row">
                                                    <td class="property">Project Date</td>
                                                    <td class="value">{{ \Carbon\Carbon::parse($project->project_date)->format('d M Y') }}</td>
                                                </tr>
                                            @endif
                                            @if ($project->website_url)
                                                <tr class="about_info-table_row">
                                                    <td class="property">Website</td>
                                                    <td class="value">
                                                        <a href="{{ $project->website_url }}" target="_blank" class="link">Visit</a>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <a class="link link-arrow" href="{{ route('frontend.contact.index') }}">
                                    Discuss Project
                                    <i class="icon-arrow_right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="about_contact about_aside-item primary-bg">
                            <div class="wrapper d-flex flex-column justify-content-between align-items-start">
                                <h3 class="about_contact-title title">Interested in Starting a Project?</h3>
                                <ul class="contact-info">
                                    <li class="contact-info_group">
                                        <span class="name">Address</span>
                                        <span class="content">{{ $contact['address'] }}</span>
                                    </li>
                                    <li class="contact-info_group">
                                        <span class="name">Email</span>
                                        <span class="content d-inline-flex flex-column">
                                            <a class="link" href="{{ $contact['email_href'] }}">{{ $contact['email'] }}</a>
                                        </span>
                                    </li>
                                    <li class="contact-info_group">
                                        <span class="name">Phone</span>
                                        <span class="content d-inline-flex flex-column">
                                            <a class="link" href="{{ $contact['phone_href'] }}">{{ $contact['phone'] }}</a>
                                        </span>
                                    </li>
                                </ul>
                                <ul class="socials d-flex align-items-center justify-content-sm-start socials--alt">
                                    <li class="socials_item">
                                        <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer">
                                            <i class="icon-facebook"></i>
                                        </a>
                                    </li>
                                    <li class="socials_item">
                                        <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer">
                                            <i class="icon-instagram"></i>
                                        </a>
                                    </li>
                                    <li class="socials_item">
                                        <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer">
                                            <i class="icon-twitter"></i>
                                        </a>
                                    </li>
                                    <li class="socials_item">
                                        <a class="socials_item-link" href="#" target="_blank" rel="noopener noreferrer">
                                            <i class="icon-whatsapp"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <section class="gallery">
            <div class="container-fluid p-0">
                <ul class="gallery_list d-flex flex-wrap">
                    <li class="gallery_list-item col-12 col-sm-6 col-xl-3">
                        <a
                            class="gallery_list-item_trigger"
                            href="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                            data-caption="{{ $project->title }}"
                            data-role="gallery-link"
                        >
                            <div class="img-wrapper">
                                <picture>
                                    <source
                                        data-srcset="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        srcset="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="lazy"
                                        data-src="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        src="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        alt="{{ $project->title }}"
                                    />
                                </picture>
                            </div>
                            <div class="text-wrapper d-flex flex-column justify-content-end">
                                <span class="subtitle">Our gallery</span>
                                <h4 class="title">{{ $project->title }}</h4>
                                <span class="label">{{ $project->category ?? 'Special Projects' }}</span>
                            </div>
                        </a>
                    </li>

                    <li class="gallery_list-item col-12 col-sm-6 col-xl-3">
                        <a
                            class="gallery_list-item_trigger"
                            href="{{ $project->image_secondary ?: ($project->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                            data-caption="{{ $project->title }}"
                            data-role="gallery-link"
                        >
                            <div class="img-wrapper">
                                <picture>
                                    <source
                                        data-srcset="{{ $project->image_secondary ?: ($project->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                        srcset="{{ $project->image_secondary ?: ($project->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="lazy"
                                        data-src="{{ $project->image_secondary ?: ($project->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                        src="{{ $project->image_secondary ?: ($project->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                        alt="{{ $project->title }}"
                                    />
                                </picture>
                            </div>
                            <div class="text-wrapper d-flex flex-column justify-content-end">
                                <span class="subtitle">Our gallery</span>
                                <h4 class="title">{{ $project->title }}</h4>
                                <span class="label">{{ $project->category ?? 'Special Projects' }}</span>
                            </div>
                        </a>
                    </li>

                    <li class="gallery_list-item col-12 col-sm-6 col-xl-3">
                        <a
                            class="gallery_list-item_trigger"
                            href="{{ $project->image_tertiary ?: ($project->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                            data-caption="{{ $project->title }}"
                            data-role="gallery-link"
                        >
                            <div class="img-wrapper">
                                <picture>
                                    <source
                                        data-srcset="{{ $project->image_tertiary ?: ($project->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                        srcset="{{ $project->image_tertiary ?: ($project->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="lazy"
                                        data-src="{{ $project->image_tertiary ?: ($project->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                        src="{{ $project->image_tertiary ?: ($project->image_url ?: asset('frontend/img/placeholder.jpg')) }}"
                                        alt="{{ $project->title }}"
                                    />
                                </picture>
                            </div>
                            <div class="text-wrapper d-flex flex-column justify-content-end">
                                <span class="subtitle">Our gallery</span>
                                <h4 class="title">{{ $project->title }}</h4>
                                <span class="label">{{ $project->category ?? 'Special Projects' }}</span>
                            </div>
                        </a>
                    </li>

                    <li class="gallery_list-item col-12 col-sm-6 col-xl-3">
                        <a
                            class="gallery_list-item_trigger"
                            href="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                            data-caption="{{ $project->title }}"
                            data-role="gallery-link"
                        >
                            <div class="img-wrapper">
                                <picture>
                                    <source
                                        data-srcset="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        srcset="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="lazy"
                                        data-src="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        src="{{ $project->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        alt="{{ $project->title }}"
                                    />
                                </picture>
                            </div>
                            <div class="text-wrapper d-flex flex-column justify-content-end">
                                <span class="subtitle">Our gallery</span>
                                <h4 class="title">{{ $project->title }}</h4>
                                <span class="label">{{ $project->category ?? 'Special Projects' }}</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </section>
        @if (!empty($relatedProjects) && count($relatedProjects) > 0)
            <section class="feedback section">
                <div
                    class="container d-flex flex-wrap align-items-start justify-content-between justify-content-md-center justify-content-lg-between"
                >
                    <div class="feedback_header section_header col-12 col-lg-5">
                        <span class="subtitle">See Also</span>
                        <h2 class="title">
                            Related
                            <span class="highlight">Projects</span>
                        </h2>
                        <p class="text">
                            Discover other projects we have completed to the highest quality standards for our clients.
                        </p>
                        <a class="btn" href="{{ route('frontend.projects.index') }}">All Projects</a>
                    </div>
                    <div class="wrapper col-lg-6">
                        <ul class="feedback_slider d-flex flex-wrap">
                            @forelse ($relatedProjects as $related)
                                <li class="feedback_slider-slide">
                                    <div class="feedback_slider-slide_wrapper d-flex flex-column justify-content-between">
                                        <p class="feedback_slider-slide_text">
                                            {{ Str::limit(strip_tags($related->excerpt ?? $related->content ?? ''), 140) }}
                                        </p>
                                        <div class="feedback_slider-slide_author d-flex align-items-center">
                                            <picture>
                                                <source
                                                    data-srcset="{{ $related->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                                    srcset="{{ $related->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                                    type="image/webp"
                                                />
                                                <img
                                                    class="avatar lazy"
                                                    data-src="{{ $related->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                                    src="{{ $related->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                                    alt="{{ $related->title }}"
                                                />
                                            </picture>
                                            <div class="wrapper">
                                                <span class="name">
                                                    <a href="{{ route('frontend.projects.show', $related->slug) }}" class="link">
                                                        {{ $related->title }}
                                                    </a>
                                                </span>
                                                <span class="company">{{ $related->category ?? '' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @empty
                            @endforelse
                        </ul>
                    </div>
                </div>
            </section>
        @endif
    </main>
    <!-- SINGLE PROJECT CONTENT END -->
@endsection

@push('scripts')
    <script src="{{ asset('frontend/js/singleproject.min.js') }}"></script>
@endpush
