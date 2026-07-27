@extends('layouts.frontend.main')

@section('title', ($news->title ?? 'News Details') . ' | EPIKEPC')
@section('page', 'blog')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/single-post.min.css') }}" />
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Building communities',
        'title'    => $news->title,
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'News', 'url' => route('frontend.news.index')],
            ['label' => Str::limit($news->title, 40)],
        ],
    ])
@endsection

@section('content')
        <!-- SINGLE POST CONTENT START  -->
        <main class="post section-nopb">
            <div class="container d-flex flex-wrap justify-content-center justify-content-md-between">
                <div class="wrapper--content">
                    <article class="post_article">
                        <div class="post_article-img">
                            <picture>
                                <source
                                    data-srcset="{{ $news->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    srcset="{{ $news->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    type="image/webp"
                                />
                                <img
                                    class="post_article-img_img post_article-media lazy"
                                    data-src="{{ $news->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    src="{{ $news->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                    alt="{{ $news->title }}"
                                />
                            </picture>
                        </div>
                        <div class="post_article-info d-flex align-items-center flex-wrap">
                            <span class="date">{{ $news->published_at?->format('F d, Y') }}</span>
                            @if ($news->categories && $news->categories->isNotEmpty())
                            <span class="divider"></span>
                            <span class="author">
                                in
                                <a class="link" href="#">{{ $news->categories->first()->name }}</a>
                            </span>
                            @endif
                        </div>
                        <div class="post_article-body">
                            {!! $news->content !!}
                        </div>
                        <div
                            class="post_article-footer d-flex flex-wrap flex-md-nowrap flex-lg-wrap flex-xl-nowrap justify-content-between"
                        >
                            <ul class="post_article-footer_tags d-flex flex-wrap align-items-baseline">
                                @if ($news->tags && $news->tags->isNotEmpty())
                                    @foreach ($news->tags as $tag)
                                    <li class="list-item">
                                        <a class="tag" href="#">{{ $tag->name }}</a>
                                    </li>
                                    @endforeach
                                @endif
                            </ul>
                            <ul class="socials d-flex align-items-center justify-content-start justify-content-xl-end">
                                <li class="socials_item">
                                    <a class="socials_item-link" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer">
                                        <i class="icon-facebook"></i>
                                    </a>
                                </li>
                                <li class="socials_item">
                                    <a class="socials_item-link" href="https://www.instagram.com/" target="_blank" rel="noopener noreferrer">
                                        <i class="icon-instagram"></i>
                                    </a>
                                </li>
                                <li class="socials_item">
                                    <a class="socials_item-link" href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($news->title) }}" target="_blank" rel="noopener noreferrer">
                                        <i class="icon-twitter"></i>
                                    </a>
                                </li>
                                <li class="socials_item">
                                    <a class="socials_item-link" href="https://wa.me/?text={{ urlencode($news->title . ' ' . request()->url()) }}" target="_blank" rel="noopener noreferrer">
                                        <i class="icon-whatsapp"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </article>
                    <div class="post_nav d-flex flex-wrap flex-sm-nowrap align-items-center justify-content-between">
                        <div class="post_nav-item post_nav-item--prev d-flex align-items-center">
                            <a
                                class="post_nav-item_control post_nav-item_control--prev d-flex align-items-center justify-content-center"
                                href="{{ route('frontend.news.index') }}"
                            >
                                <i class="icon-arrow_left icon"></i>
                            </a>
                            <div class="post_nav-item_hint">
                                <span class="label">Back to</span>
                                <h4 class="title">All Articles</h4>
                            </div>
                        </div>
                    </div>
                    <section class="post_reply">
                        <h3 class="post_reply-title">Leave a Comment</h3>
                        <form class="post_reply-form d-flex flex-column" action="#" method="POST" name="replyForm" data-type="reply">
                            <div class="wrapper d-flex flex-wrap flex-sm-nowrap justify-content-between">
                                <input
                                    class="post_reply-form_field field required"
                                    data-type="name"
                                    type="text"
                                    name="replyUserName"
                                    id="replyUserName"
                                    placeholder="Full name"
                                />
                                <input
                                    class="post_reply-form_field field required"
                                    name="replyEmail"
                                    id="replyEmail"
                                    data-type="email"
                                    type="text"
                                    placeholder="Email"
                                />
                            </div>
                            <textarea
                                class="post_reply-form_field field required"
                                data-type="message"
                                name="replyText"
                                id="replyText"
                                placeholder="Comment"
                            ></textarea>
                            <div class="wrapper">
                                <input class="post_reply-form_field field required" type="checkbox" name="saveUserData" id="saveUserData" />
                                <label for="saveUserData"
                                    >Save my name, email, and website in this browser for the next time I comment.
                                </label>
                            </div>
                            <button class="post_reply-form_btn btn" type="submit">Post comment</button>
                        </form>
                    </section>
                </div>
                <aside class="widgets">
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
                <aside class="latest section col-md-12">
                    <div class="blog_header section_header">
                        <span class="subtitle">Our blog</span>
                        <h2 class="title" data-aos="fade-right" data-aos-duration="500">Latest Posts</h2>
                    </div>
                    <ul class="blog_list row g-0">
                        @foreach ($recentPosts as $recentPost)
                        <li class="blog_list-item col-12 col-md-6 col-lg-4" data-aos="fade-up" data-order="{{ $loop->iteration }}">
                            <div class="wrapper d-flex flex-column justify-content-between">
                                <div class="img-wrapper">
                                    <picture>
                                        <source
                                            data-srcset="{{ $recentPost->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                            srcset="{{ $recentPost->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                            type="image/webp"
                                        />
                                        <img
                                            class="lazy"
                                            data-src="{{ $recentPost->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                            src="{{ $recentPost->thumbnail_url ?: asset('frontend/img/placeholder.jpg') }}"
                                            alt="{{ $recentPost->title }}"
                                        />
                                    </picture>
                                </div>
                                <div class="text-wrapper d-flex flex-column justify-content-between">
                                    <div class="info d-flex align-items-center">
                                        <span class="date">{{ $recentPost->published_at?->format('F d, Y') }}</span>
                                    </div>
                                    <h4 class="title">{{ $recentPost->title }}</h4>
                                    <div class="divider--link">
                                        <a class="link link-arrow" href="{{ route('news.show', $recentPost->slug) }}">
                                            Read post
                                            <i class="icon-arrow_right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </aside>
            </div>
        </main>
        <!-- SINGLE POST CONTENT END  -->
@endsection
