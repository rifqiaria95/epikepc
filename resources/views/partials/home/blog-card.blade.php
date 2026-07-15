<div class="col-lg-6 col-xl-{{ $isFeatured ? '6' : '3' }} col-md-6 wow itfadeUp" data-wow-duration=".9s"
    data-wow-delay="{{ number_format($delay, 1) }}s">
    <div class="single-blog-item{{ $isFeatured ? ' first' : '' }} mb-30">
        <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}">
        <div class="blog-content mt-30">
            <div class="blog-meta">
                <span class="author">{{ $article->user->name ?? 'Admin' }}</span>
                <span class="date">{{ $article->published_at?->locale('id')->translatedFormat('d F Y') }}</span>
            </div>
            <h2><a href="{{ route('news.show', $article->slug) }}">{{ $article->title }}</a></h2>
        </div>
    </div>
</div>
