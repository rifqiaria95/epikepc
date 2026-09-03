@extends('layouts.main')

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
                <table id="TableMDetails" class="datatables-users table">
                    <thead class="border-top">
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="select-all">
                            </th>
                            <th>#</th>
                            <th>Menu Name</th>
                            <th>Route</th>
                            <th>Posisi</th>
                            <th>Menu Group</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <!-- Offcanvas to add new user -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddMenu"
                aria-labelledby="offcanvasAddUserLabel">
                <div class="offcanvas-header border-bottom">
                    <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Add Menu</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
                </div>
                <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
                    <form id="formMenuDetails" name="formMenuDetails" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-6">
                            <label class="form-label" for="nama_menu">Menu Name</label>
                            <input type="text" class="form-control" id="nama_menu" name="name"
                                placeholder="Enter nama menu" required>
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
                            <label class="form-label" for="route">Route</label>
                            <input type="text" id="route" class="form-control" name="route"
                                placeholder="Route Menu">
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="order">Posisi</label>
                            <input type="number" id="order" class="form-control" name="order"
                                placeholder="Posisi Menu">
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
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css" />
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="{{ asset('assets/ajax/menu-details.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
@endsection
