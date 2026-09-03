@extends('layouts.frontend.main')

@section('title', ($vacancy->seo_title ?: $vacancy->title).' | EPIKEPC')
@section('page', 'careers')
@section('meta_description', $vacancy->seo_description ?: $vacancy->summary)

@section('meta')
    <link rel="canonical" href="{{ route('frontend.careers.show', $vacancy->slug) }}">
    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $vacancy->title,
            'description' => strip_tags($vacancy->description),
            'identifier' => ['@type' => 'PropertyValue', 'name' => 'EPIKEPC', 'value' => $vacancy->code],
            'datePosted' => optional($vacancy->published_at)?->toDateString(),
            'validThrough' => optional($vacancy->closes_at)?->toAtomString(),
            'employmentType' => $vacancy->employment_type->value,
            'hiringOrganization' => ['@type' => 'Organization', 'name' => 'EPIKEPC', 'sameAs' => url('/')],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $vacancy->location_city,
                    'addressRegion' => $vacancy->location_province,
                    'addressCountry' => 'ID',
                ],
            ],
        ], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
    </script>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/single-project.min.css') }}" />
    <style>
        .career-detail__summary {
            color: #4b5563;
            font-size: 17px;
            line-height: 1.7;
            margin-bottom: 8px;
        }
        .career-detail__section + .career-detail__section {
            margin-top: 8px;
        }
        .about_article-text {
            line-height: 1.75;
            font-size: 16px;
        }
        .about_article-text p,
        .about_article-text ul,
        .about_article-text ol {
            margin-bottom: 14px;
        }
        .about_article-text ul,
        .about_article-text ol {
            padding-left: 1.25rem;
        }
        .about_article-text li + li {
            margin-top: 6px;
        }
        .career-aside-card {
            background: #f7f9fc;
            border: 1px solid #e5e9f2;
            padding: 24px;
            margin-top: 20px;
        }
        .career-aside-card .title {
            margin-bottom: 16px;
        }
        .career-aside-card a {
            color: #253C74;
            font-weight: 600;
            text-decoration: none;
        }
        .career-aside-card a:hover {
            color: #202C38;
            text-decoration: underline;
        }
        .career-card__meta {
            color: #6b7280;
            font-size: 13px;
            margin-top: 4px;
        }
        .career-closed {
            padding: 14px 16px;
            background: #fff4e5;
            border: 1px solid #f5c27a;
            color: #7a4b00;
            font-size: 14px;
            line-height: 1.5;
            margin-top: 20px;
        }
        .career-apply-btn {
            margin-top: 24px;
        }
        @media screen and (min-width: 991.98px) {
            .about_info-table_row .value {
                text-transform: none;
                font-size: 16px;
                line-height: 1.4;
            }
        }
    </style>
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => $vacancy->department,
        'title' => $vacancy->title,
        'items' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Karir', 'url' => route('frontend.careers.index')],
            ['label' => \Illuminate\Support\Str::limit($vacancy->title, 40)],
        ],
    ])
@endsection

@section('content')
    <main>
        <div class="about section-nopb">
            <div class="container">
                <div class="row g-0 justify-content-between flex-lg-nowrap">
                    <article class="about_article col-lg-7 col-xl-auto">
                        @if ($vacancy->summary)
                            <p class="career-detail__summary">{{ $vacancy->summary }}</p>
                        @endif
                        <section class="career-detail__section">
                            <h3 class="about_article-header">Deskripsi</h3>
                            <div class="about_article-text">{!! $vacancy->description !!}</div>
                        </section>
                        <section class="career-detail__section">
                            <h3 class="about_article-header">Tanggung jawab</h3>
                            <div class="about_article-text">{!! $vacancy->responsibilities !!}</div>
                        </section>
                        <section class="career-detail__section">
                            <h3 class="about_article-header">Kualifikasi</h3>
                            <div class="about_article-text">{!! $vacancy->qualifications !!}</div>
                        </section>
                        @if ($vacancy->preferred_qualifications)
                            <section class="career-detail__section">
                                <h3 class="about_article-header">Kualifikasi tambahan</h3>
                                <div class="about_article-text">{!! $vacancy->preferred_qualifications !!}</div>
                            </section>
                        @endif
                    </article>
                    <aside class="about_aside col-lg-5 col-xl-auto">
                        <div class="about_info about_aside-item">
                            <div class="wrapper d-flex flex-column justify-content-between align-items-start">
                                <div class="wrapper--helper">
                                    <h3 class="about_info-title title">Informasi lowongan</h3>
                                    <table class="about_info-table">
                                        <tbody>
                                            <tr class="about_info-table_row">
                                                <td class="property">Kode</td>
                                                <td class="value">{{ $vacancy->code }}</td>
                                            </tr>
                                            <tr class="about_info-table_row">
                                                <td class="property">Departemen</td>
                                                <td class="value">{{ $vacancy->department }}</td>
                                            </tr>
                                            <tr class="about_info-table_row">
                                                <td class="property">Lokasi</td>
                                                <td class="value">{{ $vacancy->locationLabel() }}</td>
                                            </tr>
                                            <tr class="about_info-table_row">
                                                <td class="property">Tipe kerja</td>
                                                <td class="value">{{ $vacancy->employment_type->label() }}</td>
                                            </tr>
                                            <tr class="about_info-table_row">
                                                <td class="property">Pengaturan kerja</td>
                                                <td class="value">{{ $vacancy->work_arrangement->label() }}</td>
                                            </tr>
                                            <tr class="about_info-table_row">
                                                <td class="property">Level pengalaman</td>
                                                <td class="value">{{ $vacancy->experience_level->label() }}</td>
                                            </tr>
                                            @if ($vacancy->closes_at)
                                                <tr class="about_info-table_row">
                                                    <td class="property">Tutup</td>
                                                    <td class="value">{{ $vacancy->closes_at->translatedFormat('d M Y') }}</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                @if ($acceptsApplications)
                                    <a class="btn btn--submit btn--static career-apply-btn" href="{{ route('frontend.careers.apply', $vacancy->slug) }}">Lamar sekarang</a>
                                @else
                                    <p class="career-closed mb-0">Lowongan ini sudah ditutup dan tidak lagi menerima lamaran.</p>
                                @endif
                            </div>
                        </div>
                        @if ($related->isNotEmpty())
                            <div class="career-aside-card">
                                <h3 class="title">Lowongan terkait</h3>
                                <ul class="list-unstyled mb-0">
                                    @foreach ($related as $item)
                                        <li class="mb-3">
                                            <a href="{{ route('frontend.careers.show', $item->slug) }}">{{ $item->title }}</a>
                                            <div class="career-card__meta">{{ $item->department }} · {{ $item->locationLabel() }}</div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </aside>
                </div>
            </div>
        </div>
    </main>
@endsection
