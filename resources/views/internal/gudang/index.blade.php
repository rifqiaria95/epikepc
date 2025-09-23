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
            <table id="TableGudang" class="datatables-pegawai table">
                <thead class="border-top">
                    <tr>
                        <th>ID</th>
                        <th>Nama Gudang</th>
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
                        <h4 class="modal-title" id="modal-judul">Tambah Gudang</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formGudang" class="form-horizontal" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="id" id="id">
                            <ul id="save_errorList"></ul>
                            <div class="row">
                              <div class="col-xl-6 mb-6">
                                  <label class="form-label" for="add-pegawai-fullname">Nama
                                      Gudang</label>
                                  <input type="text" class="form-control" id="nama_gudang"
                                      placeholder="Masukkan nama gudang" name="nama_gudang"
                                      aria-label="Masukkan nama gudang"
                                    />
                                    <div id="nama_gudang-error" class="text-danger small"></div>
                              </div>
                              <div class="col-xl-6 mb-6">
                                  <label class="form-label" for="add-pegawai-fullname">Alamat</label>
                                  <input type="text" class="form-control" id="alamat_gudang"
                                      placeholder="Masukkan alamat gudang" name="alamat_gudang"
                                      aria-label="Masukkan alamat gudang" />
                                      <div id="alamat_gudang-error" class="text-danger small"></div>
                              </div>
                              <div class="col-xl-6 mb-6">
                                  <label class="form-label" for="add-pegawai-fullname">Email</label>
                                  <input type="email" class="form-control" id="email_gudang"
                                      placeholder="Masukkan email gudang" name="email_gudang"
                                      aria-label="Masukkan email gudang" />
                                      <div id="email_gudang-error" class="text-danger small"></div>
                              </div>
                              <div class="col-xl-6 mb-6">
                                  <label class="form-label" for="add-pegawai-fullname">No Telp</label>
                                  <input type="text" class="form-control" id="no_telp_gudang"
                                      placeholder="Masukkan no telp gudang" name="no_telp_gudang"
                                      aria-label="Masukkan no telp gudang"
                                      oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                                      <div id="no_telp_gudang-error" class="text-danger small"></div>
                              </div>
                              <div class="col-xl-12 mb-6">
                                  <label class="form-label" for="foto-gudang">Foto Gudang</label>
                                  <input type="file" id="foto_gudang" class="form-control"
                                      name="foto_gudang" />
                                      <div id="foto_gudang-error" class="text-danger small"></div>
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
<script src="{{ url('assets/vendor/libs/moment/moment.js') }}"></script>
<script src="{{ url('assets/vendor/libs/select2/select2.js') }}"></script>
<script src="{{ url('assets/vendor/libs/@form-validation/popular.js') }}"></script>
<script src="{{ url('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
<script src="{{ url('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
<script src="{{ url('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
<script src="{{ url('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
<script src="{{ url('assets/ajax/gudang.js') }}"></script>
@endsection
