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
            <div class="col-md-4 user_role"></div>
            <div class="col-md-4 user_plan"></div>
            <div class="col-md-4 user_status"></div>
        </div>
        </div>
        <div class="card-datatable table-responsive">
        <table id="TableSubMenuDetails" class="datatables-users table">
            <thead class="border-top">
            <tr>
                <th>#</th>
                <th>Nama Menu</th>
                <th>Route</th>
                <th>Posisi</th>
                <th>Menu Group</th>
                <th>Menu Detail</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
            </thead>
        </table>
        </div>
        <!-- Offcanvas to add new user -->
        <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="offcanvasAddMenu"
        aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header border-bottom">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Menu</h5>
                <button
                type="button"
                class="btn-close text-reset"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                <form id="formSubMenuDetails" name="formSubMenuDetails" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-6">
                        <label class="form-label" for="nama_menu">Nama Menu</label>
                        <input type="text" class="form-control" id="nama_menu" name="name" placeholder="Masukkan nama menu" required>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="menu_group_id">Menu Group</label>
                        <select id="menu_group_id" class="select2 form-select" name="menu_group_id">
                            <option selected disabled>Pilih Menu Group</option>
                            @foreach ($menuGroup as $mg)
                                <option value="{{ $mg->id }}">{{ $mg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="menu_detail_id">Menu Detail</label>
                        <select id="menu_detail_id" class="select2 form-select" name="menu_detail_id">
                            <option selected disabled>Pilih Menu Detail</option>
                            @foreach ($menuDetail as $md)
                                <option value="{{ $md->id }}">{{ $md->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="route">Route</label>
                        <input type="text" id="route" class="form-control" name="route" placeholder="Route Menu">
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="order">Posisi</label>
                        <input type="number" id="order" class="form-control" name="order" placeholder="Posisi Menu">
                    </div>
                    <div class="mb-6">
                        <fieldset class="form-group">
                            <label class="form-label">Status</label>
                            <select class="select form-select" name="status" id="status" required>
                                <option>Pilih Status</option>
                                <option value="0">Inactive</option>
                                <option value="1">Active</option>
                            </select>
                        </fieldset>
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
<script src="{{ asset('assets/ajax/sub-menu-detail.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
@endsection
