@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('internal.partials.stat-cards', ['stats' => $stats])
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Kandidat</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TableCandidates" class="table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Telepon</th>
                            <th>Domisili</th>
                            <th>Pendidikan</th>
                            <th>Pengalaman</th>
                            <th>Lamaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('assets/ajax/career/candidates.js') }}"></script>
@endsection
