@extends('layouts.frontend.main')

@section('title', 'Status lamaran | EPIKEPC')
@section('page', 'careers')

@section('header_extension')
    @include('partials.frontend.header-extension', [
        'subtitle' => 'Karir',
        'title' => 'Status lamaran',
        'items' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Karir', 'url' => route('frontend.careers.index')],
            ['label' => 'Status'],
        ],
    ])
@endsection

@section('content')
    <main class="section">
        <div class="container" style="max-width: 720px; padding: 40px 0 80px;">
            @if (session('status'))
                <p>{{ session('status') }}</p>
            @endif
            <p>Nomor referensi: <strong>{{ $public['reference_number'] }}</strong></p>
            <p>Posisi: {{ $public['vacancy_title'] }}</p>
            <p>Dikirim: {{ $public['submitted_at'] ? \Illuminate\Support\Carbon::parse($public['submitted_at'])->translatedFormat('d M Y H:i') : '—' }}</p>
            <p>Status: <strong>{{ $public['public_status'] }}</strong></p>
            @if (!empty($public['public_message']))
                <p>{{ $public['public_message'] }}</p>
            @endif
            @if (!empty($public['timeline']))
                <h3>Riwayat</h3>
                <ul>
                    @foreach ($public['timeline'] as $item)
                        <li>{{ $item['at'] ? \Illuminate\Support\Carbon::parse($item['at'])->translatedFormat('d M Y H:i') : '' }} — {{ $item['status'] }}@if ($item['message']) : {{ $item['message'] }} @endif</li>
                    @endforeach
                </ul>
            @endif
            @if ($allowWithdrawal && !in_array($public['public_status_code'], ['HIRED','REJECTED','WITHDRAWN','EXPIRED'], true))
                <form id="formWithdraw" method="POST" action="{{ route('frontend.careers.withdraw', $token) }}">
                    @csrf
                    <button class="btn btn--static" type="submit">Tarik lamaran</button>
                </form>
            @endif
        </div>
    </main>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.css') }}" />
@endpush

@push('scripts')
    <script src="{{ asset('assets/vendor/libs/sweetalert2/sweetalert2.js') }}"></script>
    <script src="{{ asset('assets/js/epik-swal.js') }}"></script>
    <script>
        (function () {
            var form = document.getElementById('formWithdraw');
            if (!form) return;
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                var ask = window.EpikSwal
                    ? window.EpikSwal.confirm({
                        title: 'Tarik lamaran?',
                        text: 'Lamaran ini akan ditarik dan tidak dapat dilanjutkan.',
                        confirmButtonText: 'Tarik lamaran',
                        cancelButtonText: 'Batal',
                        danger: true
                    })
                    : Promise.resolve(window.confirm('Tarik lamaran ini?'));

                ask.then(function (ok) {
                    if (ok) form.submit();
                });
            });
        })();
    </script>
@endpush
