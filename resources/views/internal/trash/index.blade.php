@extends('layouts.main')
@section('css')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/select2/select2.css') }}" />
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
@endsection
@section('content')
    <!-- Content -->
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
                <table id="deletedTable" class="datatables-users table">
                    <thead class="border-top">
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Deleted At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                </table>
            </div>
            <div class="modal fade text-start" id="editModal" tabindex="-1" aria-labelledby="myModalLabel18" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="titleEdit">Edit User</h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formEdit" name="formEdit" class="form-horizontal" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">
                                <ul class="alert alert-warning d-none" id="modalTitleEdit"></ul>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Name</label>
                                        <input type="text" id="name" name="name" class="name form-control" value="" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" id="email" name="email" class="email form-control" value="" required>
                                    </div>
                                    <div class="form-floating col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label">Role</label>
                                            <select class="select2 form-select" name="role" id="role" required>
                                                <option value="">Select Role</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="form-floating col-md-6">
                                        <fieldset class="form-group">
                                            <label class="form-label">Status</label>
                                            <select class="select2 form-select" name="active" id="active" required>
                                                <option>Select Role</option>
                                                <option value="0">Inactive</option>
                                                <option value="1">Active</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-12 mb-3">
                                        <label for="exampleInputPassword1" class="form-label">Avatar</label>
                                        <input type="file" name="avatar" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-block" id="btn-update" value="create">Save
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
<script src="{{ url('assets/ajax/trash.js') }}"></script>
@endsection
