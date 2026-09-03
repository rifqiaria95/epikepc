@extends('layouts.frontend.main')

@section('title', 'Tautan tidak valid | EPIKEPC')
@section('page', 'careers')

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Karir',
        'title' => 'Tautan tidak berlaku',
        'items' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Karir', 'url' => route('frontend.careers.index')],
        ],
    ])
@endsection

@section('content')
    <main class="section">
        <div class="container" style="max-width:720px;padding:40px 0 80px;">
            <p>{{ $message }}</p>
            <a class="btn btn--submit btn--static" href="{{ route('frontend.careers.index') }}">Kembali ke daftar karir</a>
        </div>
    </main>
@endsection
