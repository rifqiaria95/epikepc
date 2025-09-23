@extends('layouts.main')
@section('css')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
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
                                <i class="ti ti-users ti-26px"></i>
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
                                <h4 class="mb-0 me-2"></h4>
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
            <table id="TablePerusahaan" class="datatables-pegawai table">
                <thead class="border-top">
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Nama Perusahaan</th>
                        <th>Alamat</th>
                        <th>No Telp</th>
                        <th>Email</th>
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
                        <h4 class="modal-title" id="modal-judul">Tambah Perusahaan</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formPerusahaan" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id">
                            <ul id="save_errorList"></ul>
                            <div class="row">
                              <div class="col-xl-6 mb-6">
                                  <label class="form-label" for="add-pegawai-fullname">Nama
                                      Perusahaan</label>
                                  <input type="text" class="form-control" id="nama_perusahaan"
                                      placeholder="Masukkan nama perusahaan" name="nama_perusahaan"
                                      aria-label="Masukkan nama perusahaan"
                                    />
                                    <div id="nama_perusahaan-error" class="text-danger small"></div>
                              </div>
                              <div class="col-xl-6 mb-6">
                                  <label class="form-label" for="add-pegawai-fullname">Alamat</label>
                                  <input type="text" class="form-control" id="alamat_perusahaan"
                                      placeholder="Masukkan alamat perusahaan" name="alamat_perusahaan"
                                      aria-label="Masukkan alamat perusahaan" />
                                      <div id="alamat_perusahaan-error" class="text-danger small"></div>
                              </div>
                              <div class="col-xl-6 mb-6">
                                  <label class="form-label" for="add-pegawai-fullname">Email</label>
                                  <input type="email" class="form-control" id="email_perusahaan"
                                      placeholder="Masukkan email perusahaan" name="email_perusahaan"
                                      aria-label="Masukkan email perusahaan" />
                                      <div id="email_perusahaan-error" class="text-danger small"></div>
                              </div>
                              <div class="col-xl-6 mb-6">
                                  <label class="form-label" for="add-pegawai-fullname">No Telp</label>
                                  <input type="text" class="form-control" id="no_telp_perusahaan"
                                      placeholder="Masukkan no telp perusahaan" name="no_telp_perusahaan"
                                      aria-label="Masukkan no telp perusahaan"
                                      oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                      <div id="no_telp_perusahaan-error" class="text-danger small"></div>
                              </div>
                              <div class="col-xl-12 mb-6">
                                  <label class="form-label" for="foto-perusahaan">Logo Perusahaan</label>
                                  <input type="file" id="logo_perusahaan" class="form-control"
                                      name="logo_perusahaan" />
                                      <div id="logo_perusahaan-error" class="text-danger small"></div>
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
    window.images_path = "{{ asset('images') }}";
</script>
<script src="{{ url('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ url('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ url('assets/vendor/libs/@form-validation/popular.js') }}"></script>
<script src="{{ url('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
<script src="{{ url('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
<script src="{{ url('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
<script src="{{ url('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
<script src="{{ url('assets/ajax/perusahaan.js') }}"></script>
@endsection
