@extends('layouts.frontend.main')

@section('title', 'Lamaran terkirim | EPIKEPC')
@section('page', 'careers')

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Karir',
        'title' => 'Lamaran terkirim',
        'items' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Karir', 'url' => route('frontend.careers.index')],
            ['label' => 'Terkirim'],
        ],
    ])
@endsection

@section('content')
    <main class="section">
        <div class="container" style="max-width: 720px; padding: 40px 0 80px;">
            <p>Terima kasih. Lamaran Anda telah diverifikasi dan dikirim.</p>
            <p>Nomor referensi: <strong>{{ $public['reference_number'] }}</strong></p>
            <p>Posisi: {{ $public['vacancy_title'] }}</p>
            <p>Status: {{ $public['public_status'] }}</p>
            <p>Kami hanya menghubungi kandidat yang lolos seleksi awal. Email konfirmasi beserta tautan status aman telah dikirim.</p>
            <a class="btn btn--submit btn--static" href="{{ route('frontend.careers.index') }}">Lihat lowongan lain</a>
        </div>
    </main>
@endsection
