@extends('layouts.frontend.main')

@section('title', 'Karir | EPIKEPC')
@section('page', 'careers')
@section('meta_description', 'Lowongan kerja EPIKEPC. Bergabunglah dengan tim engineering dan konstruksi kami.')

@section('meta')
    <link rel="canonical" href="{{ route('frontend.careers.index') }}">
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('frontend/css/projects.min.css') }}" />
    <style>
        .career-section { padding: 40px 0 80px; }
        .career-filters { margin: 32px 0 48px; padding: 24px; background: #f7f9fc; border: 1px solid #e5e9f2; }
        .career-filters .field, .career-filters select.field { width: 100%; border: 1px solid #d9dee8; background: #fff; padding: 12px 14px; font-size: 15px; color: #202C38; }
        .career-filters .field:focus, .career-filters select.field:focus { border-color: #ffdf08; outline: none; }
        .career-card { border: 1px solid #e5e9f2; background: #fff; padding: 28px; height: 100%; display: flex; flex-direction: column; }
        .career-card__meta { color: #6b7280; font-size: 14px; margin-bottom: 12px; }
        .career-card__title { color: #202C38; font-size: 22px; margin-bottom: 12px; }
        .career-card__summary { color: #4b5563; flex-grow: 1; margin-bottom: 20px; }
        .career-badge { display: inline-block; padding: 4px 10px; background: #253C74; color: #fff; font-size: 12px; margin-right: 6px; margin-bottom: 6px; }
        .career-empty { padding: 80px 20px; text-align: center; color: #6b7280; }
        .career-filters label { display: block; font-size: 13px; color: #253C74; margin-bottom: 6px; font-weight: 600; }
        .career-filters__actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }
        .career-filters__actions .btn {
            padding: 10px 18px;
            font-size: 14px;
            line-height: 1.2;
            width: auto;
            min-width: 0;
            text-transform: none;
            letter-spacing: 0;
        }
        .career-filters__actions .btn--reset {
            background: #fff;
            border: 1px solid #d9dee8;
            color: #253C74;
        }
        .career-filters__actions .btn--reset:hover,
        .career-filters__actions .btn--reset:focus {
            background: #f7f9fc;
            border-color: #253C74;
            color: #253C74;
            bottom: 0;
        }
    </style>
@endpush

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Building communities',
        'title' => 'Karir',
        'items' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Karir'],
        ],
    ])
@endsection

@section('content')
    <main class="projects section career-section">
        <div class="container">
            <div class="section_header" data-aos="fade-up">
                <span class="subtitle">Careers</span>
                <h2 class="title">Lowongan <span class="highlight">terbuka</span></h2>
                <p>Temukan posisi yang sesuai. Lamaran dikirim tanpa membuat akun.</p>
            </div>

            <form class="career-filters" method="GET" action="{{ route('frontend.careers.index') }}" role="search" aria-label="Filter lowongan">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="q">Kata kunci</label>
                        <input class="field" id="q" name="q" type="search" value="{{ request('q') }}" placeholder="Jabatan, departemen, kota">
                    </div>
                    <div class="col-md-2">
                        <label for="department">Departemen</label>
                        <select class="field" id="department" name="department">
                            <option value="">Semua</option>
                            @foreach ($filters['departments'] as $department)
                                <option value="{{ $department }}" @selected(request('department') === $department)>{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="location">Lokasi</label>
                        <select class="field" id="location" name="location">
                            <option value="">Semua</option>
                            @foreach ($filters['locations'] as $location)
                                <option value="{{ $location }}" @selected(request('location') === $location)>{{ $location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="employment_type">Tipe kerja</label>
                        <select class="field" id="employment_type" name="employment_type">
                            <option value="">Semua</option>
                            @foreach ($employmentTypes as $value => $label)
                                <option value="{{ $value }}" @selected(request('employment_type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="work_arrangement">Pengaturan kerja</label>
                        <select class="field" id="work_arrangement" name="work_arrangement">
                            <option value="">Semua</option>
                            @foreach ($workArrangements as $value => $label)
                                <option value="{{ $value }}" @selected(request('work_arrangement') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 career-filters__actions mt-4 justify-content-end">
                        <button class="btn btn--submit btn--static" type="submit">Terapkan filter</button>
                        <a class="btn btn--static btn--reset" href="{{ route('frontend.careers.index') }}">Reset</a>
                    </div>
                </div>
            </form>

            <div class="row g-4">
                @forelse ($vacancies as $vacancy)
                    <div class="col-12 col-md-6 col-xl-4">
                        <article class="career-card">
                            <div class="career-card__meta">
                                {{ $vacancy->department }} · {{ $vacancy->locationLabel() }}
                            </div>
                            <h3 class="career-card__title">
                                <a href="{{ route('frontend.careers.show', $vacancy->slug) }}">{{ $vacancy->title }}</a>
                            </h3>
                            <p class="career-card__summary">{{ $vacancy->summary }}</p>
                            <div>
                                <span class="career-badge">{{ $vacancy->employment_type->label() }}</span>
                                <span class="career-badge">{{ $vacancy->work_arrangement->label() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="career-card__meta mb-0">
                                    @if ($vacancy->closes_at)
                                        Ditutup {{ $vacancy->closes_at->translatedFormat('d M Y') }}
                                    @else
                                        Tidak ada batas waktu
                                    @endif
                                </span>
                                <a class="link link-arrow" href="{{ route('frontend.careers.show', $vacancy->slug) }}">
                                    Detail <i class="icon-arrow_right"></i>
                                </a>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12 career-empty">
                        <p>Tidak ada lowongan yang sesuai filter saat ini.</p>
                    </div>
                @endforelse
            </div>

            @if ($vacancies->hasPages())
                <div class="pagination d-flex align-items-center justify-content-center">
                    {{ $vacancies->links('vendor.pagination.frontend') }}
                </div>
            @endif
        </div>
    </main>
@endsection
