@extends('layouts.frontend.main')

@section('title', 'Verifikasi email | EPIKEPC')
@section('page', 'careers')

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Karir',
        'title' => 'Periksa email Anda',
        'items' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Karir', 'url' => route('frontend.careers.index')],
            ['label' => 'Verifikasi'],
        ],
    ])
@endsection

@section('content')
    <main class="section">
        <div class="container" style="max-width: 720px; padding: 40px 0 80px;">
            <p>Kami telah menerima data untuk posisi <strong>{{ $vacancy->title }}</strong>.</p>
            <p>Jika alamat email yang Anda masukkan valid, tautan verifikasi telah dikirim. Lamaran baru dianggap terkirim setelah email diverifikasi.</p>
            <form method="POST" action="{{ route('frontend.careers.resend', $vacancy->slug) }}" class="mt-4">
                @csrf
                <label for="email">Kirim ulang tautan verifikasi</label>
                <input class="field" id="email" name="email" type="email" value="{{ session('resend_email') }}" required style="width:100%;padding:12px;margin:8px 0;border:1px solid #d9dee8;">
                <input type="text" name="website" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;">
                <button class="btn btn--submit btn--static" type="submit">Kirim ulang</button>
            </form>
            @if (session('status'))
                <p class="mt-3">{{ session('status') }}</p>
            @endif
        </div>
    </main>
@endsection
