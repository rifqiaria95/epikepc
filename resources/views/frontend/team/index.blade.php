@extends('layouts.frontend.main')

@section('title', 'Our Team | EPIKEPC')
@section('page', 'team')

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/team.min.css') }}" />
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Building communities',
        'title'    => 'Our Team',
        'items'    => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Team'],
        ],
    ])
@endsection

@section('content')
    <!-- TEAM CONTENT START -->
    <main class="team section">
        <div class="container">
            <ul class="team_list row g-0">
                @forelse ($members as $index => $member)
                    <li class="team_list-item col-md-6 col-xl-4"
                        data-aos="fade-up"
                        data-aos-once="true"
                        data-aos-delay="{{ $index > 0 ? 30 : 0 }}"
                        data-order="{{ $index + 1 }}">
                        <div class="wrapper d-flex flex-column justify-content-between">
                            <div class="img-wrapper">
                                <picture>
                                    <source
                                        data-srcset="{{ $member->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        srcset="{{ $member->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        type="image/webp"
                                    />
                                    <img
                                        class="lazy"
                                        data-src="{{ $member->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        src="{{ $member->image_url ?: asset('frontend/img/placeholder.jpg') }}"
                                        alt="{{ $member->nama }}"
                                    />
                                </picture>
                            </div>
                            <div class="text-wrapper flex-grow-1 d-flex flex-column justify-content-between">
                                <h4 class="name">{{ $member->nama }}</h4>
                                <span class="position">{{ $member->jabatan }}</span>
                                <p class="text">{{ $member->deskripsi }}</p>
                                <ul class="socials d-flex align-items-center justify-content-center justify-content-md-start">
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
                    </li>
                @empty
                    <li class="col-12 text-center py-5">
                        <p>No team data available yet.</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </main>
    <!-- TEAM CONTENT END -->
@endsection
