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
                                <span class="text-heading">Session</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">21,459</h4>
                                    <p class="text-success mb-0">(+29%)</p>
                                </div>
                                <small class="mb-0">Total Pegawai</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-pegawais ti-26px"></i>
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
                                <span class="text-heading">Paid Pegawai</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">4,567</h4>
                                    <p class="text-success mb-0">(+18%)</p>
                                </div>
                                <small class="mb-0">Last week analytics </small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti ti-pegawai-plus ti-26px"></i>
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
                                <span class="text-heading">Active Pegawai</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ totalPegawai() }}</h4>
                                    <p class="text-danger mb-0">(-14%)</p>
                                </div>
                                <small class="mb-0">Last week analytics</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti ti-pegawai-check ti-26px"></i>
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
                                <span class="text-heading">Pending Pegawai</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">237</h4>
                                    <p class="text-success mb-0">(+42%)</p>
                                </div>
                                <small class="mb-0">Last week analytics</small>
                            </div>
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti ti-pegawai-search ti-26px"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pegawais List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Filters</h5>
                <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                    <div class="col-md-4 pegawai_role"></div>
                    <div class="col-md-4 pegawai_plan"></div>
                    <div class="col-md-4 pegawai_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TablePegawai" class="datatables-pegawai table">
                    <thead class="border-top">
                        <tr>
                            <th>ID</th>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Perusahaan</th>
                            <th>No. Telepon</th>
                            <th>Status</th>
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
                            <h4 class="modal-title" id="modal-judul">Tambah Pegawai</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formPegawai" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">
                                <input type="hidden" name="user_id" id="user_id">
                                <ul id="save_errorList"></ul>
                                <div class="row">
                                    <div class="col-xl-12">
                                        <div class="nav-align-top nav-tabs mb-6">
                                            <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <button type="button" class="nav-link active" role="tab"
                                                        data-bs-toggle="tab" data-bs-target="#navs-top-home"
                                                        aria-controls="navs-top-home" aria-selected="true">
                                                        Informasi Pribadi
                                                    </button>
                                                </li>
                                                <li class="nav-item">
                                                    <button type="button" class="nav-link" role="tab"
                                                        data-bs-toggle="tab" data-bs-target="#navs-top-profile"
                                                        aria-controls="navs-top-profile" aria-selected="false">
                                                        Alamat
                                                    </button>
                                                </li>
                                                <li class="nav-item">
                                                    <button type="button" class="nav-link" role="tab"
                                                        data-bs-toggle="tab" data-bs-target="#navs-top-messages"
                                                        aria-controls="navs-top-messages" aria-selected="false">
                                                        Info Lainnya
                                                    </button>
                                                </li>
                                            </ul>
                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="navs-top-home"
                                                    role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="add-pegawai-fullname">Nama
                                                                Pegawai</label>
                                                            <input type="text" class="form-control" id="nm_pegawai"
                                                                placeholder="John Doe" name="nm_pegawai"
                                                                aria-label="John Doe" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <fieldset class="form-group">
                                                                <label class="form-label">Jenis Kelamin</label>
                                                                <select class="select form-select" name="jenis_kelamin"
                                                                    id="jenis_kelamin" required>
                                                                    <option>Pilih Jenis Kelamin</option>
                                                                    <option value="0">Perempuan</option>
                                                                    <option value="1">Laki-Laki</option>
                                                                </select>
                                                            </fieldset>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="add-pegawai-fullname">Tanggal
                                                                Lahir</label>
                                                            <input type="date" class="form-control"
                                                                id="tgl_lahir_pegawai" placeholder="John Doe"
                                                                name="tgl_lahir_pegawai" aria-label="John Doe" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label"
                                                                for="add-pegawai-email">Email</label>
                                                            <input type="email" id="email" class="form-control"
                                                                placeholder="john.doe@example.com"
                                                                aria-label="john.doe@example.com" name="email" />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="add-pegawai-email">No
                                                                Tlp</label>
                                                            <input type="tel" id="no_telp_pegawai"
                                                                class="form-control" placeholder="+62"
                                                                aria-label="john.doe@example.com" name="no_telp_pegawai"
                                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label"
                                                                for="add-pegawai-company">KTP</label>
                                                            <input type="number" id="no_ktp_pegawai"
                                                                class="form-control" placeholder="3275031XXXXX"
                                                                aria-label="jdoe1" name="no_ktp_pegawai"
                                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label"
                                                                for="add-pegawai-company">SIM</label>
                                                            <input type="number" id="no_sim_pegawai"
                                                                class="form-control" placeholder="3275031XXXXX"
                                                                aria-label="jdoe1" name="no_sim_pegawai"
                                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label"
                                                                for="add-pegawai-company">NPWP</label>
                                                            <input type="number" id="no_npwp_pegawai"
                                                                class="form-control" placeholder="3275031XXXXX"
                                                                aria-label="jdoe1" name="no_npwp_pegawai"
                                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <fieldset class="form-group">
                                                                <label class="form-label">Status</label>
                                                                <select class="select form-select" name="status"
                                                                    id="status" required>
                                                                    <option>Pilih Status</option>
                                                                    <option value="inactive">Inactive</option>
                                                                    <option value="active">Active</option>
                                                                </select>
                                                            </fieldset>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="navs-top-profile" role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-xl-12 mb-6">
                                                            <label class="form-label"
                                                                for="add-pegawai-company">Alamat</label>
                                                            <textarea class="form-control" id="alamat_pegawai" name="alamat_pegawai" rows="3"></textarea>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label"
                                                                for="pegawai-provinsi">Provinsi</label>
                                                            <select id="pegawai-provinsi" class="select2 form-select"
                                                                name="id_provinsi">
                                                                <option selected disabled>Pilih Provinsi</option>
                                                                @foreach ($provinsi as $prv)
                                                                    <option value="{{ $prv->id_provinsi }}">
                                                                        {{ $prv->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="pegawai-kota">Kota</label>
                                                            <select id="pegawai-kota"
                                                                class="select2 form-select form-select-md" name="id_kota">
                                                                <option selected disabled>Pilih Kota</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="tab-pane fade" id="navs-top-messages" role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="add-pegawai-fullname">Tanggal
                                                                Masuk</label>
                                                            <input type="date" class="form-control"
                                                                id="tgl_masuk_pegawai" placeholder="John Doe"
                                                                name="tgl_masuk_pegawai" aria-label="John Doe" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="add-pegawai-fullname">Tanggal
                                                                Keluar</label>
                                                            <input type="date" class="form-control"
                                                                id="tgl_keluar_pegawai" placeholder="John Doe"
                                                                name="tgl_keluar_pegawai" aria-label="John Doe" />
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label"
                                                                for="pegawai-perusahaan">Perusahaan</label>
                                                            <select id="pegawai-perusahaan" class="select2 form-select"
                                                                name="id_perusahaan">
                                                                <option selected disabled>Pilih Perusahaan</option>
                                                                @foreach ($perusahaan as $per)
                                                                    <option value="{{ $per->id }}">
                                                                        {{ $per->nama_perusahaan }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="pegawai-cabang">Cabang</label>
                                                            <select id="pegawai-cabang" class="select2 form-select"
                                                                name="id_cabang">
                                                                <option selected disabled>Pilih Cabang</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="pegawai-divisi">Divisi</label>
                                                            <select id="pegawai-divisi" class="select2 form-select"
                                                                name="id_divisi">
                                                                <option selected disabled>Pilih Divisi</option>
                                                                @foreach ($divisi as $div)
                                                                    <option value="{{ $div->id }}">
                                                                        {{ $div->nama_divisi }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label"
                                                                for="pegawai-departemen">Departemen</label>
                                                            <select id="pegawai-departemen" class="select2 form-select"
                                                                name="id_departemen">
                                                                <option selected disabled>Pilih Departemen</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label"
                                                                for="pegawai-jabatan">Jabatan</label>
                                                            <select id="pegawai-jabatan" class="select2 form-select"
                                                                name="id_jabatan">
                                                                <option selected disabled>Pilih Jabatan</option>
                                                                @foreach ($jabatan as $jab)
                                                                    <option value="{{ $jab->id }}">
                                                                        {{ $jab->nama_jabatan }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-xl-6 mb-6">
                                                            <label class="form-label" for="add-pegawai-company">Gaji
                                                                Pegawai</label>
                                                            <input type="number" id="gaji_pegawai" class="form-control"
                                                                placeholder="3275031XXXXX" aria-label="jdoe1"
                                                                name="gaji_pegawai"
                                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                                        </div>
                                                        <div class="col-xl-12 mb-6">
                                                            <label for="exampleInputPassword1"
                                                                class="form-label">Foto</label>
                                                            <input type="file" id="foto_pegawai" name="foto_pegawai"
                                                                class="form-control">
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
    <script src="{{ asset('assets/ajax/pegawai.js') }}"></script>
    <script src="{{ url('assets/js/hierarchy-dropdown.js') }}"></script>
@endsection
