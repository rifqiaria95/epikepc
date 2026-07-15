@php
    $categories = $project->category_tags;
    $detailUrl = route('frontend.projects.show', $project->slug);
@endphp

<div class="col-lg-4 col-xl-4 col-md-6 wow itfadeUp" data-wow-duration=".9s"
    data-wow-delay="{{ number_format($delay, 1) }}s">
    <div class="single-project-item mb-30">
        <img src="{{ $project->image_url }}" alt="{{ $project->title }}">
        <span class="icon">
            <a href="{{ $detailUrl }}">
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </span>
        <div class="single-project-content">
            <h3>
                <a href="{{ $detailUrl }}">{{ $project->title }}</a>
            </h3>
            @if (!empty($categories))
                <div class="project-cat">
                    @foreach ($categories as $category)
                        <span>{{ $category }}{{ !$loop->last ? ', ' : '' }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
