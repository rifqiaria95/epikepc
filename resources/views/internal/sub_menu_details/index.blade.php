@extends('layouts.main')

@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
            @include('internal.partials.stat-cards', ['stats' => $stats])

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
                <th>Menu Name</th>
                <th>Route</th>
                <th>Posisi</th>
                <th>Menu Group</th>
                <th>Menu Detail</th>
                <th>Status</th>
                <th>Actions</th>
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
                        <label class="form-label" for="nama_menu">Menu Name</label>
                        <input type="text" class="form-control" id="nama_menu" name="name" placeholder="Enter nama menu" required>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="menu_group_id">Menu Group</label>
                        <select id="menu_group_id" class="select2 form-select" name="menu_group_id">
                            <option selected disabled>Select Menu Group</option>
                            @foreach ($menuGroup as $mg)
                                <option value="{{ $mg->id }}">{{ $mg->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="menu_detail_id">Menu Detail</label>
                        <select id="menu_detail_id" class="select2 form-select" name="menu_detail_id">
                            <option selected disabled>Select Menu Detail</option>
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
                                <option>Select Status</option>
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
