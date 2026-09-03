@extends('layouts.main')
@section('css')
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ url('assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ url('assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ url('/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" />
@endsection
@section('content')
    <style>
        .delete-selected {
            display: none;
        }

        .delete-selected.show {
            display: inline-block;
        }

        .dt-checkboxes {
            cursor: pointer;
        }

        .form-check-input:checked {
            background-color: #696cff;
            border-color: #696cff;
        }
    </style>
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
                <table id="TablePermission" class="datatables-users table">
                    <thead class="border-top">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Menu Group</th>
                            <th>Menu Detail</th>
                            <th>Assgined To</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!--/ Add Permission Modal -->
            <div class="modal fade text-start" id="tambahModal" tabindex="-1" aria-labelledby="myModalLabel18"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-center" id="myModalLabel18">Add Permission</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="formPermission" name="formPermission" class="form-horizontal"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <input type="hidden" name="id" id="id">
                                <input type="hidden" id="guard_name" name="guard_name" class="guard_name form-control"
                                    value="web">

                                <div class="row g-3">
                                    <!-- Permission Name -->
                                    <div class="col-md-12">
                                        <label class="form-label">Permission Name</label>
                                        <input type="text" id="name" name="name" class="name form-control"
                                            value="" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Menu Groups</label>
                                        <select id="menu_groups" name="menu_groups" class="select2 form-select">
                                            <option disabled selected>Select Menu Groups</option>
                                            @foreach ($menuGroups as $group)
                                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label">Menu Details</label>
                                        <select id="menu_details" name="menu_details" class="select2 form-select">
                                            <option selected disabled>Select Menu Details</option>
                                            @foreach ($menuDetails as $detail)
                                                <option value="{{ $detail->id }}">{{ $detail->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary btn-block" id="btn-simpan"
                                    value="create">Save
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
    <script src="{{ url('assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/cleavejs/cleave.js') }}"></script>
    <script src="{{ url('assets/vendor/libs/cleavejs/cleave-phone.js') }}"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="{{ url('assets/ajax/permission.js') }}"></script>
@endsection
