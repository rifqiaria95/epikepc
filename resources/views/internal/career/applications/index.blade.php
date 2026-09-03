@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('internal.partials.stat-cards', ['stats' => $stats])
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Lamaran</h5>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TableApplications" class="table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Referensi</th>
                            <th>Kandidat</th>
                            <th>Email</th>
                            <th>Lowongan</th>
                            <th>Status</th>
                            <th>Verifikasi</th>
                            <th>Rekruter</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>window.userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));</script>
    <script src="{{ asset('assets/ajax/career/applications.js') }}"></script>
@endsection
