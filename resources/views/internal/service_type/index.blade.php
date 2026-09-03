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
        <table id="Tableservice_type" class="datatables-users table">
            <thead class="border-top">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
            </thead>
        </table>
        </div>
        <!-- Offcanvas to add new user -->
        <div
        class="offcanvas offcanvas-end"
        tabindex="-1"
        id="offcanvasAddservice_type"
        aria-labelledby="offcanvasAddUserLabel">
            <div class="offcanvas-header border-bottom">
                <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Service Type</h5>
                <button
                type="button"
                class="btn-close text-reset"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
            </div>
            <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                <form id="formservice_type" name="formservice_type" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="mb-6">
                        <label class="form-label" for="name">Name</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter nama kategori">
                        <div id="name-error" class="text-danger small"></div>
                    </div>
                    <div class="mb-6">
                        <label class="form-label" for="type">Type</label>
                        <select class="form-select" id="type" name="type">
                            <option value="it">IT</option>
                            <option value="design">Design</option>
                        </select>
                        <div id="type-error" class="text-danger small"></div>
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
<script src="{{ asset('assets/ajax/service_type.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
@endsection
