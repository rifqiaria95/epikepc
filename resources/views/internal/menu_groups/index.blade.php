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
                <table id="TableMGroup" class="datatables-users table">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Menu Name</th>
                            <th>Jenis Menu</th>
                            <th>Icon</th>
                            <th>Posisi</th>
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
                    <form id="formMenu" name="formMenu" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="id" id="id">
                        <div class="mb-6">
                            <label class="form-label" for="nama_menu">Menu Name</label>
                            <input type="text" class="form-control" id="nama_menu" name="name"
                                placeholder="Enter nama menu" required>
                        </div>
                        <div class="mb-6">
                            <fieldset class="form-group">
                                <label class="form-label">Jenis Menu</label>
                                <select class="select form-select" name="jenis_menu" id="jenis_menu" required>
                                    <option>Select Menu Type</option>
                                    <option value="1">Purchasing</option>
                                    <option value="2">HRD</option>
                                    <option value="3">Accounting</option>
                                    <option value="4">Inventory</option>
                                    <option value="5">Sales</option>
                                    <option value="6">Company</option>
                                    <option value="7">Admin</option>
                                    <option value="8">Portfolio</option>
                                    <option value="9">Program</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="icon">Icon</label>
                            <input type="text" id="icon" class="form-control" name="icon"
                                placeholder="fa fa-home">
                        </div>
                        <div class="mb-6">
                            <label class="form-label" for="order">Posisi</label>
                            <input type="number" id="order" class="form-control" name="order"
                                placeholder="Posisi Menu">
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
    <script src="{{ asset('assets/ajax/menu-groups.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.2/Sortable.min.js"></script>
@endsection
