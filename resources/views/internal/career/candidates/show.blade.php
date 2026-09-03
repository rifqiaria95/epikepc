@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4>{{ $candidate->full_name }}</h4>
        <p class="text-muted">{{ $candidate->email }} · {{ $candidate->phone }} · {{ $candidate->domicile_city }}, {{ $candidate->domicile_province }}</p>
        <div class="card">
            <div class="card-header"><h5 class="mb-0">Lamaran</h5></div>
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr><th>Referensi</th><th>Lowongan</th><th>Status</th><th>Dokumen</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($candidate->applications as $application)
                            <tr>
                                <td><a href="{{ route('career.applications.show', $application->id) }}">{{ $application->reference_number }}</a></td>
                                <td>{{ $application->vacancy?->title }}</td>
                                <td>{{ $application->status->label() }}</td>
                                <td>{{ $application->documents->count() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
