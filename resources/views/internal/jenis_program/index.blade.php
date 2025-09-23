@extends('layouts.main')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row g-6 mb-6">
            <div class="col-sm-12 col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="text-heading">Jenis Program</span>
                                <div class="d-flex align-items-center my-1">
                                    <h4 class="mb-0 me-2">{{ totalJenisProgram() }}</h4>
                                </div>
                                <small class="mb-0">Total Jenis Program</small>
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
        </div>
        <!-- Users List Table -->
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-0">Filters</h5>
                <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                    <div class="col-md-4 user_role"></div>
                    <div class="col-md-4 user_plan"></div>
                    <div class="col-md-4 user_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table id="TableJenisProg" class="datatables-users table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Jenis</th>
                            <th>Deskripsi</th>
                            <th>Gambar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- Offcanvas to add new user -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddJenisProg"
                aria-labelledby="offcanvasAddUserLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Tambah Jenis Program</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                    <form id="formJenisProg" name="formJenisProg" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="mb-6">
                            <label class="form-label" for="nama_jenis_program">Nama Jenis Program</label>
                            <input type="text" class="form-control" id="nama_jenis_program" name="nama_jenis_program"
                                placeholder="Masukkan nama jenis program">
                            <div id="nama_jenis_program-error" class="text-danger small"></div>
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="deskripsi_jenis_program">Deskripsi Jenis Program</label>
                            <textarea class="form-control" id="deskripsi_jenis_program" name="deskripsi_jenis_program"
                                placeholder="Masukkan deskripsi jenis program"></textarea>
                            <div id="deskripsi_jenis_program-error" class="text-danger small"></div>
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="gambar_jenis_program">Gambar Jenis Program</label>
                            <input type="file" class="form-control" id="gambar_jenis_program" name="gambar_jenis_program"
                                placeholder="Masukkan gambar jenis program">
                            <div id="gambar_jenis_program-error" class="text-danger small"></div>
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="status">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="true">Active</option>
                                <option value="false">Inactive</option>
                            </select>
                            <div id="status-error" class="text-danger small"></div>
                        </div>
                        <button type="submit" class="btn btn-primary me-3 data-submit">Submit</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="offcanvas">Cancel</button>
                    </form>
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
    <script src="{{ asset('assets/ajax/jenis_program.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
@endsection
