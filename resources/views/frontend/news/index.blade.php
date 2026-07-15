@extends('layouts.frontend.main')

@section('title', 'News | EPIKEPC')
@section('page', 'blog')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/blog.min.css') }}" />
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Latest News',
        'title'    => 'News',
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'News'],
        ],
    ])
@endsection

@section('content')
        <!-- BLOG CONTENT START -->
        <main class="blog section">
            <div class="container d-flex flex-wrap flex-lg-nowrap justify-content-center justify-content-md-between">
                <div class="wrapper--content">
                    <ul class="blog_feed row g-0">
                        @forelse ($posts as $post)
                        <li class="blog_feed-item col-12 col-md-6" data-order="{{ $loop->iteration }}">
                            <div class="blog_feed-item_wrapper d-flex flex-column">
                                <div class="img-wrapper">
                                    <picture>
                                        <source
                                            data-srcset="{{ $post->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                            srcset="{{ $post->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                            type="image/webp"
                                        />
                                        <img
                                            class="lazy"
                                            data-src="{{ $post->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                            src="{{ $post->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                            alt="{{ $post->title }}"
                                        />
                                    </picture>
                                </div>
                                <div class="text-wrapper d-flex flex-column justify-content-between align-items-start flex-grow-1">
                                    <div class="info d-flex align-items-center">
                                        @if ($post->category)
                                            <a class="category" href="{{ route('frontend.news.index', ['category' => $post->category]) }}">{{ $post->category }}</a>
                                            <span class="divider"></span>
                                        @endif
                                        <span class="date">{{ $post->published_at?->format('F d, Y') }}</span>
                                    </div>
                                    <h4 class="title{{ $loop->first ? ' title--bookmarked' : '' }}">{{ $post->title }}</h4>
                                    <p class="text">{{ $post->excerpt }}</p>
                                    <a class="link link-arrow" href="{{ route('news.show', $post->slug) }}">
                                        Read post
                                        <i class="icon-arrow_right"></i>
                                    </a>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="col-12 text-center" style="padding: 60px 0;">
                            <p>No news has been published yet.</p>
                        </li>
                        @endforelse
                    </ul>
                    @if ($posts->hasPages())
                    <div class="pagination d-flex align-items-center justify-content-center justify-content-sm-start">
                        {{ $posts->links() }}
                    </div>
                    @endif
                </div>
                <aside class="widgets widgets--blog">
                    <form class="widgets_widget--search d-flex flex-nowrap" action="#" method="POST">
                        <input class="field required" type="text" placeholder="Search" />
                        <button class="btn btn--static" type="submit">
                            <i class="icon-search"></i>
                        </button>
                    </form>
                    <div class="widgets_widget widgets_widget--categories">
                        <h3 class="widgets_widget-title">Categories</h3>
                        <ul class="list">
                            @foreach ($categories as $category)
                            <li class="list-item">
                                <a class="link d-flex align-items-center" href="{{ route('frontend.news.index', ['category' => $category->id]) }}">
                                    <i class="icon-arrow_right icon--arrow"></i>
                                    {{ $category->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="widgets_widget widgets_widget--latest">
                        <h3 class="widgets_widget-title">Latest Articles</h3>
                        <ul class="list">
                            @foreach ($recentPosts as $recentPost)
                            <li class="list-item">
                                <picture>
                                    <source
                                        data-srcset="{{ $recentPost->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        srcset="{{ $recentPost->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="lazy preview"
                                        data-src="{{ $recentPost->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        src="{{ $recentPost->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        alt="{{ $recentPost->title }}"
                                    />
                                </picture>
                                <h4 class="title">{{ $recentPost->title }}</h4>
                                <a class="link link-arrow" href="{{ route('news.show', $recentPost->slug) }}">
                                    Read now
                                    <i class="icon-arrow_right icon"></i>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="widgets_widget widgets_widget--newsletter">
                        <h3 class="widgets_widget-title">Subscribe to our news</h3>
                        <p class="text">Find out about the last days and the latest promotions of our Corporation</p>
                        <form
                            class="d-flex flex-wrap flex-sm-nowrap form"
                            data-type="newsletter"
                            action="#"
                            method="POST"
                            name="newsletterForm"
                            id="newsletterForm--widget"
                        >
                            <input
                                class="field required"
                                name="newsletterEmail"
                                id="newsletterEmail--widget"
                                type="text"
                                placeholder="Email"
                                data-type="email"
                            />
                            <button class="btn btn--submit btn--static" type="submit">Subscribe</button>
                        </form>
                        <ul class="socials d-flex align-items-center justify-content-start">
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
                    <div class="widgets_widget widgets_widget--tags">
                        <h3 class="widgets_widget-title">Tags</h3>
                        <ul class="list d-flex flex-wrap align-items-baseline">
                            @if (!empty($allTags) && count($allTags) > 0)
                                @foreach ($allTags as $tag)
                                <li class="list-item">
                                    <a class="tag" href="#">{{ $tag->name }}</a>
                                </li>
                                @endforeach
                            @else
                            <li class="list-item"><a class="tag" href="#">Engineering</a></li>
                            <li class="list-item"><a class="tag" href="#">Technology</a></li>
                            <li class="list-item"><a class="tag" href="#">Materials</a></li>
                            <li class="list-item"><a class="tag" href="#">Future</a></li>
                            <li class="list-item"><a class="tag" href="#">Plan</a></li>
                            <li class="list-item"><a class="tag" href="#">Building</a></li>
                            <li class="list-item"><a class="tag" href="#">House</a></li>
                            <li class="list-item"><a class="tag" href="#">Design</a></li>
                            <li class="list-item"><a class="tag" href="#">Innovation</a></li>
                            <li class="list-item"><a class="tag" href="#">Draw</a></li>
                            @endif
                        </ul>
                    </div>
                    <div class="widgets_widget widgets_widget--comments">
                        <h3 class="widgets_widget-title">Recent Comments</h3>
                        <ul class="list">
                            <li class="list-item">
                                <i class="icon-comment icon--bubble"></i>
                                <a class="link" href="#">
                                    <span class="username">Admin</span>
                                    in tempor eros tortor, a ornare
                                </a>
                            </li>
                            <li class="list-item">
                                <i class="icon-comment icon--bubble"></i>
                                <a class="link" href="#">
                                    <span class="username">Admin</span>
                                    in tempor eros tortor, a ornare
                                </a>
                            </li>
                            <li class="list-item">
                                <i class="icon-comment icon--bubble"></i>
                                <a class="link" href="#">
                                    <span class="username">Admin</span>
                                    in tempor eros tortor, a ornare
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="widgets_widget widgets_widget--archives">
                        <h3 class="widgets_widget-title">Archives</h3>
                        <ul class="list">
                            <li class="list-item">
                                <a class="link d-flex align-items-center" href="#">
                                    <i class="icon-arrow_right icon--arrow"></i>
                                    December
                                </a>
                            </li>
                            <li class="list-item">
                                <a class="link d-flex align-items-center" href="#">
                                    <i class="icon-arrow_right icon--arrow"></i>
                                    January
                                </a>
                            </li>
                            <li class="list-item">
                                <a class="link d-flex align-items-center" href="#">
                                    <i class="icon-arrow_right icon--arrow"></i>
                                    February
                                </a>
                            </li>
                            <li class="list-item">
                                <a class="link d-flex align-items-center" href="#">
                                    <i class="icon-arrow_right icon--arrow"></i>
                                    March
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="widgets_widget widgets_widget--calendar">
                        <h3 class="widgets_widget-title">Calendar</h3>
                        <table class="table">
                            <caption class="table_header">
                                July, 2020
                            </caption>
                            <tbody class="table_body">
                                <tr class="table_body-week">
                                    <th class="table_body-week_day">S</th>
                                    <th class="table_body-week_day">M</th>
                                    <th class="table_body-week_day">T</th>
                                    <th class="table_body-week_day">W</th>
                                    <th class="table_body-week_day">T</th>
                                    <th class="table_body-week_day">F</th>
                                    <th class="table_body-week_day">S</th>
                                </tr>
                                <tr class="table_body-dates">
                                    <td class="table_body-dates_date"></td>
                                    <td class="table_body-dates_date"></td>
                                    <td class="table_body-dates_date"></td>
                                    <td class="table_body-dates_date">1</td>
                                    <td class="table_body-dates_date">2</td>
                                    <td class="table_body-dates_date">3</td>
                                    <td class="table_body-dates_date">4</td>
                                </tr>
                                <tr class="table_body-dates">
                                    <td class="table_body-dates_date">5</td>
                                    <td class="table_body-dates_date">6</td>
                                    <td class="table_body-dates_date">7</td>
                                    <td class="table_body-dates_date">8</td>
                                    <td class="table_body-dates_date">9</td>
                                    <td class="table_body-dates_date">10</td>
                                    <td class="table_body-dates_date">11</td>
                                </tr>
                                <tr class="table_body-dates">
                                    <td class="table_body-dates_date">12</td>
                                    <td class="table_body-dates_date">13</td>
                                    <td class="table_body-dates_date">14</td>
                                    <td class="table_body-dates_date">15</td>
                                    <td class="table_body-dates_date table_body-dates_date--current">16</td>
                                    <td class="table_body-dates_date">17</td>
                                    <td class="table_body-dates_date">18</td>
                                </tr>
                                <tr class="table_body-dates">
                                    <td class="table_body-dates_date">19</td>
                                    <td class="table_body-dates_date">20</td>
                                    <td class="table_body-dates_date">21</td>
                                    <td class="table_body-dates_date">22</td>
                                    <td class="table_body-dates_date">23</td>
                                    <td class="table_body-dates_date">24</td>
                                    <td class="table_body-dates_date">25</td>
                                </tr>
                                <tr class="table_body-dates">
                                    <td class="table_body-dates_date">26</td>
                                    <td class="table_body-dates_date">27</td>
                                    <td class="table_body-dates_date">28</td>
                                    <td class="table_body-dates_date">29</td>
                                    <td class="table_body-dates_date">30</td>
                                    <td class="table_body-dates_date">31</td>
                                    <td class="table_body-dates_date"></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="navigation d-flex align-items-center justify-content-between">
                            <a class="navigation_control navigation_control--prev d-inline-flex align-items-center" href="#">
                                <i class="icon-arrow_left navigation_control-icon"></i>
                                Previous
                            </a>
                            <a class="navigation_control navigation_control--next d-inline-flex align-items-center" href="#">
                                Next
                                <i class="icon-arrow_right navigation_control-icon"></i>
                            </a>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
        <!-- BLOG CONTENT END -->
@endsection
