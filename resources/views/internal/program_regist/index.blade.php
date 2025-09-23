@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <style>
        #modal-loading {
            display: none !important;
        }

        #modal-error {
            display: none !important;
        }
    </style>
@endsection
@section('content')
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Pendaftar</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ totalPendaftar() }}</h4>
                                </div>
                                <small class="mb-0">Total Pendaftar</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-user ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Pending</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ totalPendaftarPending() }}</h4>
                                </div>
                                <small class="mb-0">Pendaftar Pending</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti ti-progress-alert ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Approved</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ totalPendaftarApproved() }}</h4>
                                </div>
                                <small class="mb-0">Pendaftar Approved</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti ti-circle-dashed-check ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Rejected</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ totalPendaftarRejected() }}</h4>
                                </div>
                                <small class="mb-0">Pendaftar Rejected</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti ti-circle-dashed-x ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- pelanggans List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Filters</h5>
                <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                    <div class="col-md-4 pelanggan_role"></div>
                    <div class="col-md-4 pelanggan_plan"></div>
                    <div class="col-md-4 pelanggan_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TableProgramRegist" class="datatables-program-regist table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Program</th>
                            <th>User</th>
                            <th>Status</th>
                            <th>Alasan</th>
                            <th>Notes Admin</th>
                            <th>Tanggal Regist</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal fade text-start" id="tambahModal" tabindex="-1" aria-labelledby="myModalLabel18"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-judul">Tambah Program Regist</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div id="modal-error" class="alert alert-danger mt-5" style="display: none; margin: 0 1rem;">
                            <i class="ti ti-alert-circle me-2"></i><span id="modal-error-message"></span>
                        </div>
                        <div id="modal-loading" class="d-flex justify-content-center align-items-center"
                            style="display: none; margin: 1rem; padding: 20px;">
                            <div class="spinner-border text-primary me-2" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <span class="text-muted">Menyimpan data...</span>
                        </div>
                        <form id="formProgramRegist" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">
                                <ul id="save_errorList"></ul>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="nav-align-top nav-tabs mb-6">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <button type="button" class="nav-link active" role="tab"
                                                        data-bs-toggle="tab" data-bs-target="#navs-top-home"
                                                        aria-controls="navs-top-home" aria-selected="true">
                                                        Informasi Program Regist
                                                    </button>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="navs-top-home"
                                                    role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-lg-6 mb-6">
                                                            <label class="form-label" for="program_id">Program</label>
                                                            <select id="program_id" class="select2 form-select"
                                                                name="program_id">
                                                                <option selected disabled>Pilih Program</option>
                                                                @foreach ($program as $prg)
                                                                    <option value="{{ $prg->id }}">{{ $prg->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-6 mb-6">
                                                            <label class="form-label" for="user_id">User</label>
                                                            <select id="user_id" class="select2 form-select"
                                                                name="user_id">
                                                                <option selected disabled>Pilih User</option>
                                                                @foreach ($user as $usr)
                                                                    <option value="{{ $usr->id }}">{{ $usr->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-lg-3 mb-6">
                                                            <label class="form-label" for="registered_at">Tanggal
                                                                Regist</label>
                                                            <input type="date" id="registered_at" class="form-control"
                                                                placeholder="Tanggal Regist" aria-label="Tanggal Regist"
                                                                name="registered_at" />
                                                        </div>
                                                        <div class="col-lg-3 mb-6">
                                                            <label class="form-label" for="alasan">Alasan</label>
                                                            <input type="text" id="alasan" class="form-control"
                                                                placeholder="Alasan" aria-label="Alasan"
                                                                name="alasan" />
                                                        </div>
                                                        <div class="col-lg-6 mb-6">
                                                            <fieldset class="form-group">
                                                                <label class="form-label">Status</label>
                                                                <select class="select form-select" name="status"
                                                                    id="status" required>
                                                                    <option selected disabled>Pilih Status</option>
                                                                    <option value="pending">Pending</option>
                                                                    <option value="approved">Approved</option>
                                                                    <option value="rejected">Rejected</option>
                                                                </select>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-lg-12 mb-6">
                                                            <label class="form-label" for="add-program-description">Notes
                                                                Admin</label>
                                                            <textarea type="text" id="notes_admin" class="form-control" placeholder="Notes Admin" aria-label="Notes Admin"
                                                                name="notes_admin"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary btn-block" id="btn-simpan"
                                    value="create">Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- / Content -->
@endsection
@section('script')
    <script>
        window.userPermissions = @json(auth()->user()->getAllPermissions()->pluck('name'));
    </script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="{{ asset('assets/ajax/program_regist.js') }}"></script>
@endsection
