@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="mb-4">Career Overview</h4>
        <div class="row g-3 mb-4">
            @foreach ([
                ['Aktif', $metrics['active_vacancies']],
                ['Segera tutup', $metrics['closing_soon']],
                ['Baru terverifikasi', $metrics['new_verified']],
                ['Screening', $metrics['screening']],
                ['Interview', $metrics['interview']],
                ['Offered', $metrics['offered']],
                ['Hired', $metrics['hired']],
                ['Rejected', $metrics['rejected']],
            ] as $card)
                <div class="col-6 col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <small class="text-muted">{{ $card[0] }}</small>
                            <h3 class="mb-0">{{ number_format($card[1]) }}</h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header"><h5 class="mb-0">Lamaran per lowongan</h5></div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @forelse ($metrics['by_vacancy'] as $row)
                                <li class="d-flex justify-content-between py-2 border-bottom">
                                    <span>{{ $row->vacancy?->title ?? $row->job_vacancy_id }}</span>
                                    <strong>{{ $row->aggregate }}</strong>
                                </li>
                            @empty
                                <li class="text-muted">Belum ada data.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Aktivitas terbaru</h5></div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            @forelse ($metrics['recent'] as $row)
                                <li class="py-2 border-bottom">
                                    <a href="{{ route('career.applications.show', $row->id) }}">{{ $row->reference_number }}</a>
                                    — {{ $row->candidate?->full_name }} / {{ $row->vacancy?->title }}
                                </li>
                            @empty
                                <li class="text-muted">Belum ada aktivitas.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
