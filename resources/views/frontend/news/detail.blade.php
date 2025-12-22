@extends('layouts.frontend.main')

@section('content')
    <!-- Breadcrumb Section -->
    <div class="breadcrumb-section bg-img" style="background-image: url('{{ asset('frontend/img/bg-img/90.jpg') }}');">
        <div class="container">
            <!-- Breadcrumb Content -->
            <div class="breadcrumb-content">
                <div class="divider"></div>
                <h2>{{ $news->title }}</h2>
                <ul class="list-unstyled">
                    <li><a href="{{ url('/') }}">Home</a></li>
                    <li>Blog Details</li>
                </ul>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </div>

    <!-- User Profile [SVG] -->
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="icon-user-profile" viewBox="0 0 20 20" fill="none">
            <path
                d="M10.001 0.650391C12.499 0.650391 14.5437 2.69437 14.5439 5.19238C14.5439 7.69056 12.4992 9.73535 10.001 9.73535C7.50299 9.73517 5.45898 7.69045 5.45898 5.19238C5.45919 2.69448 7.50308 0.650569 10.001 0.650391Z"
                stroke="#601FEB" stroke-width="1.3" />
            <path
                d="M6.2041 11.4083C6.22327 11.4045 6.26409 11.4086 6.30469 11.4269L6.34375 11.4493L6.34863 11.4523C7.41552 12.2397 8.68474 12.6455 9.99902 12.6456C11.2313 12.6456 12.4247 12.2892 13.4482 11.5958L13.6504 11.4523L13.6553 11.4493C13.6717 11.4374 13.7412 11.4077 13.8506 11.4171C15.3678 11.6303 16.7302 12.4459 17.6689 13.6915L17.8516 13.9474L17.8555 13.9523C18.0155 14.1834 18.152 14.4238 18.2607 14.671C18.1428 14.8748 18.0147 15.0722 17.8711 15.2697L17.7158 15.4767L17.708 15.4874C17.4915 15.7812 17.2481 16.0563 16.9902 16.3253L16.7285 16.5929C16.4317 16.8896 16.0922 17.1862 15.7559 17.4386C14.0785 18.6913 12.0607 19.3497 9.97656 19.3497C7.89732 19.3497 5.88498 18.6935 4.20996 17.4464C3.84577 17.1505 3.51261 16.8799 3.22559 16.5929L3.21875 16.5851L3.21094 16.5792L2.95215 16.3234C2.78377 16.1498 2.62475 15.9693 2.47168 15.7794L2.24609 15.4874L2.24316 15.4825L1.94434 15.0695C1.86428 14.9526 1.78843 14.8339 1.71875 14.7169C1.83566 14.4561 1.98209 14.1841 2.14258 13.9523L2.14355 13.9532L2.15137 13.9415C3.06835 12.5558 4.53571 11.6392 6.16504 11.4152L6.18457 11.4122L6.2041 11.4083Z"
                stroke="#601FEB" stroke-width="1.3" />
        </symbol>
    </svg>

    <!-- Message Box [SVG] -->
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="icon-message-box" viewBox="0 0 18 18" fill="none">
            <path d="M1.125 2.25H16.875V12.375H9L4.5 16.3125V12.375H1.125V2.25Z" stroke="#601FEB" stroke-linecap="round"
                stroke-linejoin="round" />
        </symbol>
    </svg>

    <!-- Calendar [SVG] -->
    <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
        <symbol id="icon-calendar" viewBox="0 0 18 18" fill="none">
            <g clip-path="url(#clip0)">
                <path
                    d="M16.1251 3H14.5001V4H16.0001V15H2.00013V4H3.50013V3H1.87513C1.75825 3.00195 1.6429 3.02691 1.53566 3.07345C1.42843 3.11999 1.33141 3.1872 1.25016 3.27125C1.1689 3.35529 1.105 3.45451 1.0621 3.56325C1.0192 3.67199 0.998142 3.78812 1.00013 3.905V15.095C0.998142 15.2119 1.0192 15.328 1.0621 15.4367C1.105 15.5455 1.1689 15.6447 1.25016 15.7288C1.33141 15.8128 1.42843 15.88 1.53566 15.9265C1.6429 15.9731 1.75825 15.998 1.87513 16H16.1251C16.242 15.998 16.3574 15.9731 16.4646 15.9265C16.5718 15.88 16.6688 15.8128 16.7501 15.7288C16.8314 15.6447 16.8953 15.5455 16.9382 15.4367C16.9811 15.328 17.0021 15.2119 17.0001 15.095V3.905C17.0021 3.78812 16.9811 3.67199 16.9382 3.56325C16.8953 3.45451 16.8314 3.35529 16.7501 3.27125C16.6688 3.1872 16.5718 3.11999 16.4646 3.07345C16.3574 3.02691 16.242 3.00195 16.1251 3Z"
                    fill="#601FEB" />
                <path d="M4 7H5V8H4V7Z" fill="#601FEB" />
                <path d="M7 7H8V8H7V7Z" fill="#601FEB" />
                <path d="M10 7H11V8H10V7Z" fill="#601FEB" />
                <path d="M13 7H14V8H13V7Z" fill="#601FEB" />
                <path d="M4 9.5H5V10.5H4V9.5Z" fill="#601FEB" />
                <path d="M7 9.5H8V10.5H7V9.5Z" fill="#601FEB" />
                <path d="M10 9.5H11V10.5H10V9.5Z" fill="#601FEB" />
                <path d="M13 9.5H14V10.5H13V9.5Z" fill="#601FEB" />
                <path d="M4 12H5V13H4V12Z" fill="#601FEB" />
                <path d="M7 12H8V13H7V12Z" fill="#601FEB" />
                <path d="M10 12H11V13H10V12Z" fill="#601FEB" />
                <path d="M13 12H14V13H13V12Z" fill="#601FEB" />
                <path
                    d="M5 5C5.13261 5 5.25979 4.94732 5.35355 4.85355C5.44732 4.75979 5.5 4.63261 5.5 4.5V1.5C5.5 1.36739 5.44732 1.24021 5.35355 1.14645C5.25979 1.05268 5.13261 1 5 1C4.86739 1 4.74021 1.05268 4.64645 1.14645C4.55268 1.24021 4.5 1.36739 4.5 1.5V4.5C4.5 4.63261 4.55268 4.75979 4.64645 4.85355C4.74021 4.94732 4.86739 5 5 5Z"
                    fill="#601FEB" />
                <path
                    d="M13 5C13.1326 5 13.2598 4.94732 13.3536 4.85355C13.4473 4.75979 13.5 4.63261 13.5 4.5V1.5C13.5 1.36739 13.4473 1.24021 13.3536 1.14645C13.2598 1.05268 13.1326 1 13 1C12.8674 1 12.7402 1.05268 12.6464 1.14645C12.5527 1.24021 12.5 1.36739 12.5 1.5V4.5C12.5 4.63261 12.5527 4.75979 12.6464 4.85355C12.7402 4.94732 12.8674 5 13 5Z"
                    fill="#601FEB" />
                <path d="M6.5 3H11.5V4H6.5V3Z" fill="#601FEB" />
            </g>
            <defs>
                <clipPath id="clip0">
                    <rect width="18" height="18" fill="white" />
                </clipPath>
            </defs>
        </symbol>
    </svg>

    <!-- Blog Section -->
    <div class="blog-section">
        <!-- Divider -->
        <div class="divider"></div>

        <div class="container">
            <div class="row g-5 g-md-4 g-xxl-5">
                <!-- Single Blog Content -->
                <div class="col-12 col-md-7 col-lg-8">
                    <div class="pe-lg-3">
                        <!-- Single Blog Content -->
                        <div class="single-blog-content">
                            <div class="blog-card-two wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="200ms">
                                <!-- Post Image -->
                                <div class="post-img">
                                    <img src="{{ $news->thumbnail_url }}" alt="{{ $news->title }}">
                                </div>

                                <!-- Post Body -->
                                <div class="post-body">
                                    <!-- Blog Meta -->
                                    <div class="blog-meta mb-2 flex-wrap d-flex align-items-center gap-4">
                                        <a href="#">
                                            <svg width="20" height="20">
                                                <use xlink:href="#icon-user-profile"></use>
                                            </svg>
                                            By {{ $news->user->name ?? 'Admin' }}
                                        </a>
                                        <a href="#">
                                            <svg width="18" height="18">
                                                <use xlink:href="#icon-calendar"></use>
                                            </svg>
                                            {{ $news->published_at?->format('d M, Y') ?? 'No Date' }}
                                        </a>
                                    </div>

                                    <!-- Post Title -->
                                    <h3 class="post-title mb-4">{{ $news->title }}</h3>

                                    @if($news->summary)
                                        <p class="lead">{{ $news->summary }}</p>
                                    @endif

                                    <div class="d-flex flex-column gap-4">
                                        {!! $news->content !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="divider-sm"></div>

                        <!-- Tag & Share -->
                        <div class="tag-share-wrapper">
                            @if($news->tags->count() > 0)
                                <ul class="list-unstyled tag-list">
                                    <li>Tags:</li>
                                    @foreach($news->tags as $tag)
                                        <li><a href="#">{{ $tag->name }}</a></li>
                                    @endforeach
                                </ul>
                            @endif

                            <ul class="list-unstyled share-list">
                                <li>Share:</li>
                                <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('news.show', $news->slug)) }}" target="_blank"><i class="ti ti-brand-facebook"></i></a></li>
                                <li><a href="https://twitter.com/intent/tweet?url={{ urlencode(route('news.show', $news->slug)) }}&text={{ urlencode($news->title) }}" target="_blank"><i class="ti ti-brand-x"></i></a></li>
                                <li><a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(route('news.show', $news->slug)) }}" target="_blank"><i class="ti ti-brand-linkedin"></i></a></li>
                                <li><a href="https://wa.me/?text={{ urlencode($news->title . ' ' . route('news.show', $news->slug)) }}" target="_blank"><i class="ti ti-brand-whatsapp"></i></a></li>
                            </ul>
                        </div>

                        <div class="divider-sm"></div>
                    </div>
                </div>

                <div class="col-12 col-md-5 col-lg-4">
                    <div class="d-flex flex-column gap-5">
                        <!-- Widget -->
                        <div class="blog-widget">
                            <h4 class="fw-bold mb-4">Search Here</h4>

                            <!-- Form -->
                            <form action="#" method="get">
                                <input type="search" placeholder="Search here..." class="form-control">
                                <button type="submit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 20 20" fill="none">
                                        <g clip-path="url(#clip0_1_17841)">
                                            <path
                                                d="M2.5 8.33333C2.5 9.09938 2.65088 9.85792 2.94404 10.5657C3.23719 11.2734 3.66687 11.9164 4.20854 12.4581C4.75022 12.9998 5.39328 13.4295 6.10101 13.7226C6.80875 14.0158 7.56729 14.1667 8.33333 14.1667C9.09938 14.1667 9.85792 14.0158 10.5657 13.7226C11.2734 13.4295 11.9164 12.9998 12.4581 12.4581C12.9998 11.9164 13.4295 11.2734 13.7226 10.5657C14.0158 9.85792 14.1667 9.09938 14.1667 8.33333C14.1667 7.56729 14.0158 6.80875 13.7226 6.10101C13.4295 5.39328 12.9998 4.75022 12.4581 4.20854C11.9164 3.66687 11.2734 3.23719 10.5657 2.94404C9.85792 2.65088 9.09938 2.5 8.33333 2.5C7.56729 2.5 6.80875 2.65088 6.10101 2.94404C5.39328 3.23719 4.75022 3.66687 4.20854 4.20854C3.66687 4.75022 3.23719 5.39328 2.94404 6.10101C2.65088 6.80875 2.5 7.56729 2.5 8.33333Z"
                                                stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M17.5 17.5L12.5 12.5" stroke="white" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </g>
                                        <defs>
                                            <clipPath id="clip0_1_17841">
                                                <rect width="20" height="20" fill="white" />
                                            </clipPath>
                                        </defs>
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- Widget -->
                        <div class="blog-widget">
                            <h4 class="fw-bold mb-4">Categories</h4>

                            <ul class="blog-list style-two">
                                @forelse($categories as $category)
                                    <li>
                                        <a href="#">
                                            {{ $category->name }}
                                            <span>{{ $category->news_count }}</span>
                                        </a>
                                    </li>
                                @empty
                                    <li>No categories available</li>
                                @endforelse
                            </ul>
                        </div>

                        <!-- Widget -->
                        <div class="blog-widget">
                            <h4 class="fw-bold mb-4">Recent Posts</h4>

                            <div class="d-flex flex-column gap-4">
                                @forelse($recentPosts as $post)
                                    <!-- Widget Blog Post -->
                                    <div class="widget-blog-post">
                                        <div class="blog-thumbnail">
                                            <img src="{{ $post->thumbnail_url }}" alt="{{ $post->title }}">
                                        </div>
                                        <div class="blog-content">
                                            <p class="mb-1 text-primary">{{ $post->published_at?->format('d M, Y') ?? 'No Date' }}</p>
                                            <a href="{{ route('news.show', $post->slug) }}">{{ Str::limit($post->title, 60) }}</a>
                                        </div>
                                    </div>
                                @empty
                                    <p>No recent posts available</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- Widget -->
                        <div class="blog-widget">
                            <h4 class="fw-bold mb-4">Tags</h4>

                            <ul class="tag-list list-unstyled">
                                @forelse($allTags as $tag)
                                    <li><a href="#">{{ $tag->name }}</a></li>
                                @empty
                                    <li>No tags available</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </div>

    <!-- CTA Wrapper -->
    <div class="cta-wrapper bg-img" style="background-image: url(assets/img/core-img/grid.jpg)">
        <!-- Divider -->
        <div class="divider"></div>

        <div class="container">
            <div class="row g-4 g-xl-5 align-items-center">
                <div class="col-12 col-lg-6 col-xl-7">
                    <h2 class="mb-0 wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="400ms">Start Building Your
                        Business Now</h2>
                </div>

                <div class="col-12 col-lg-6 col-xl-5">
                    <p class="wow fadeInUp" data-wow-duration="1000ms" data-wow-delay="600ms">Communicate your pricing
                        clearly and transparently to build trust with your customers. Hidden fees or
                        unclear pricing structures can lead to dissatisfaction.</p>
                    <a href="pricing.html" class="btn btn-primary btn-hover-border wow fadeInUp"
                        data-wow-duration="1000ms" data-wow-delay="800ms">Get Started <i
                            class="ti ti-arrow-up-right"></i></a>
                </div>
            </div>
        </div>

        <!-- Divider -->
        <div class="divider"></div>
    </div>
@endsection

@section('scripts')
@endsection
