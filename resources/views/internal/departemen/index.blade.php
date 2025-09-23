@extends('layouts.main')

@section('content')

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
                <small class="mb-0">Total Users</small>
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
                <span class="text-heading">Paid Users</span>
                <div class="d-flex align-items-center my-1">
                    <h4 class="mb-0 me-2">4,567</h4>
                    <p class="text-success mb-0">(+18%)</p>
                </div>
                <small class="mb-0">Last week analytics </small>
                </div>
                <div class="avatar">
                <span class="avatar-initial rounded bg-label-danger">
                    <i class="ti ti-user-plus ti-26px"></i>
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
                <span class="text-heading">Active Users</span>
                <div class="d-flex align-items-center my-1">
                    <h4 class="mb-0 me-2">{{ totalUser() }}</h4>
                    <p class="text-danger mb-0">(-14%)</p>
                </div>
                <small class="mb-0">Last week analytics</small>
                </div>
                <div class="avatar">
                <span class="avatar-initial rounded bg-label-success">
                    <i class="ti ti-user-check ti-26px"></i>
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
                <span class="text-heading">Pending Users</span>
                <div class="d-flex align-items-center my-1">
                    <h4 class="mb-0 me-2">237</h4>
                    <p class="text-success mb-0">(+42%)</p>
                </div>
                <small class="mb-0">Last week analytics</small>
                </div>
                <div class="avatar">
                <span class="avatar-initial rounded bg-label-warning">
                    <i class="ti ti-user-search ti-26px"></i>
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
            <div class="col-md-4">
                <label class="form-label" for="filter_divisi">Filter by Divisi</label>
                <select id="filter_divisi" class="form-select">
                    <option value="">Semua Divisi</option>
                    @foreach ($divisi as $d)
                        <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 user_plan"></div>
            <div class="col-md-4 user_status"></div>
        </div>
        </div>
        <div class="card-datatable table-responsive">
        <table id="TableDepartemen" class="datatables-users table">
            <thead class="border-top">
            <tr>
                <th>#</th>
                <th>Nama Departemen</th>
                <th>Divisi</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
            </thead>
        </table>
        </div>
        <!-- Offcanvas to add new user -->
        <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="offcanvasAddDepartemen"
        aria-labelledby="offcanvasAddDepartemenLabel">
            <div class="offcanvas-header border-bottom">
                <h5 id="offcanvasAddDepartemenLabel" class="offcanvas-title">Tambah Departemen</h5>
                <button
                type="button"
                class="btn-close text-reset"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                <form id="formDepartemen" name="formDepartemen" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="mb-6">
                        <label class="form-label" for="nama_departemen">Nama Departemen</label>
                        <input type="text" class="form-control" id="nama_departemen" name="nama_departemen" placeholder="Masukkan nama departemen">
                        <div id="nama_departemen-error" class="text-danger small"></div>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="divisi">Divisi</label>
                        <select id="id_divisi" class="select2 form-select" name="id_divisi">
                            <option value="" selected disabled>Pilih Divisi</option>
                            @foreach ($divisi as $d)
                                <option value="{{ $d->id }}">{{ $d->nama_divisi }}</option>
                            @endforeach
                        </select>
                        <div id="id_divisi-error" class="text-danger small"></div>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="deskripsi">Deskripsi</label>
                        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                        <div id="deskripsi-error" class="text-danger small"></div>
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
<script src="{{ url('assets/ajax/departemen.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
@endsection
