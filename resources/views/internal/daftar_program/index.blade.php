@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
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
                                <span class="text-heading">Program</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ totalProgram() }}</h4>
                                </div>
                                <small class="mb-0">Total Program</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-notes ti-26px"></i>
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
                                <span class="text-heading">Open Program</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ openProgram() }}</h4>
                                </div>
                                <small class="mb-0">Total Open Program</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti ti-lock-open-2 ti-26px"></i>
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
                                <span class="text-heading">Draft Program</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ totalDraftProgram() }}</h4>
                                </div>
                                <small class="mb-0">Total Draft Program</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti ti-progress ti-26px"></i>
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
                                <span class="text-heading">Closed Program</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ totalClosedProgram() }}</h4>
                                </div>
                                <small class="mb-0">Total Closed Program</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti ti-lock ti-26px"></i>
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
                <table id="TableProgram" class="datatables-program table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Jenis</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Berakhir</th>
                            <th>Kuota</th>
                            <th>Kategori</th>
                            <th>Sumber Dana</th>
                            <th>Status</th>
                            <th>Dibuat Oleh</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal fade text-start" id="tambahModal" tabindex="-1" aria-labelledby="myModalLabel18"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="modal-judul">Tambah Program</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formProgram" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">
                                <ul id="save_errorList"></ul>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="nav-align-top nav-tabs mb-6">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <button type="button" class="nav-link active" role="tab"
                                                        data-bs-toggle="tab" data-bs-target="#navs-top-home"
                                                        aria-controls="navs-top-home" aria-selected="true">
                                                        Informasi Program
                                                    </button>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="navs-top-home"
                                                    role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="add-pelanggan-fullname">Nama
                                                                Program</label>
                                                            <input type="text" class="form-control" id="name"
                                                                placeholder="Nama Program" name="name"
                                                                aria-label="Nama Program" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="jenis_program">Jenis
                                                                Program</label>
                                                            <select id="jenis_program" class="select2 form-select"
                                                                name="jenis_program_id">
                                                                <option selected disabled>Pilih Jenis Program</option>
                                                                @foreach ($jenis_program as $jns)
                                                                    <option value="{{ $jns->id }}">
                                                                        {{ $jns->nama_jenis_program }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="add-pelanggan-company">Tanggal
                                                                Mulai</label>
                                                            <input type="date" id="start_date" class="form-control"
                                                                placeholder="Tanggal Mulai" aria-label="Tanggal Mulai"
                                                                name="start_date" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="add-pelanggan-company">Tanggal
                                                                Berakhir</label>
                                                            <input type="date" id="end_date" class="form-control"
                                                                placeholder="Tanggal Berakhir"
                                                                aria-label="Tanggal Berakhir" name="end_date" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label"
                                                                for="add-pelanggan-company">Kuota</label>
                                                            <input type="number" id="kuota" class="form-control"
                                                                placeholder="Kuota" aria-label="Kuota" name="kuota" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label"
                                                                for="add-pelanggan-company">Kategori</label>
                                                            <input type="text" id="kategori" class="form-control"
                                                                placeholder="Kategori" aria-label="Kategori"
                                                                name="kategori" />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="add-pelanggan-company">Sumber
                                                                Dana</label>
                                                            <input type="text" id="sumber_dana" class="form-control"
                                                                placeholder="Sumber Dana" aria-label="Sumber Dana"
                                                                name="sumber_dana" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <fieldset class="form-group">
                                                                <label class="form-label">Status</label>
                                                                <select class="select form-select" name="status"
                                                                    id="status" required>
                                                                    <option>Pilih Status</option>
                                                                    <option value="draft">Draft</option>
                                                                    <option value="open">Open</option>
                                                                    <option value="closed">Closed</option>
                                                                </select>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label"
                                                                for="add-program-description">Deskripsi</label>
                                                            <textarea type="text" id="description" class="form-control" placeholder="Deskripsi" aria-label="Deskripsi"
                                                                name="description"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
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
    <script src="{{ asset('assets/ajax/program.js') }}"></script>
@endsection
